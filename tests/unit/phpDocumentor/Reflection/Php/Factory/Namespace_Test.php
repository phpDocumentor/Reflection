<?php

declare(strict_types=1);

namespace phpDocumentor\Reflection\Php\Factory;

use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Php\Class_ as ClassElement;
use phpDocumentor\Reflection\Php\File;
use phpDocumentor\Reflection\Php\ProjectFactoryStrategy;
use phpDocumentor\Reflection\Php\StrategyContainer;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Class_ as ClassNode;
use PhpParser\Node\Stmt\Namespace_ as NamespaceNode;
use Prophecy\Argument;
use stdClass;

use function current;

/**
 * @coversDefaultClass \phpDocumentor\Reflection\Php\Factory\Namespace_
 */
final class Namespace_Test extends TestCase
{
    protected function setUp(): void
    {
        $this->fixture = new Namespace_();
    }

    /**
     * @covers ::matches
     */
    public function testMatches(): void
    {
        $this->assertFalse($this->fixture->matches(self::createContext(null), new stdClass()));
        $this->assertTrue($this->fixture->matches(
            self::createContext(null),
            $this->prophesize(NamespaceNode::class)->reveal()
        ));
    }

    /**
     * @covers ::create
     */
    public function testCreateThrowsException(): void
    {
        $this->expectException('InvalidArgumentException');
        $this->fixture->create(
            self::createContext(null),
            new stdClass(),
            $this->prophesize(StrategyContainer::class)->reveal()
        );
    }

    /**
     * @covers ::create
     */
    public function testIteratesStatements(): void
    {
        $class           = new ClassNode('\MyClass');
        $classElement = new ClassElement(new Fqsen('\MyClass'));
        $strategyMock      = $this->prophesize(ProjectFactoryStrategy::class);
        $containerMock     = $this->prophesize(StrategyContainer::class);
        $namespace         = new NamespaceNode(new Name('MyNamespace'));
        $namespace->fqsen = new Fqsen('\MyNamespace');
        $namespace->stmts = [$class];

        $strategyMock->create(Argument::type(ContextStack::class), $class, $containerMock)
            ->will(function ($args) use ($classElement): void {
                $args[0]->peek()->addClass($classElement);
            })
            ->shouldBeCalled();

        $containerMock->findMatching(
            Argument::type(ContextStack::class),
            $class
        )->willReturn($strategyMock->reveal());

        $file = new File('hash', 'path');
        $this->fixture->create(self::createContext(null)->push($file), $namespace, $containerMock->reveal());
        $class = current($file->getClasses());
        $fqsen = current($file->getNamespaces());

        $this->assertInstanceOf(ClassElement::class, $class);
        $this->assertEquals('\MyClass', (string) $class->getFqsen());
        $this->assertSame($namespace->fqsen, $fqsen);
    }
}
