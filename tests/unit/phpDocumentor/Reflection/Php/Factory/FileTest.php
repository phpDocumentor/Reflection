<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link http://phpdoc.org
 */

namespace phpDocumentor\Reflection\Php\Factory;

use InvalidArgumentException;
use Mockery as m;
use phpDocumentor\Reflection\DocBlock as DocBlockDescriptor;
use phpDocumentor\Reflection\DocBlockFactoryInterface;
use phpDocumentor\Reflection\File as SourceFile;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Middleware\Middleware;
use phpDocumentor\Reflection\Php\File as FileElement;
use phpDocumentor\Reflection\Php\NodesFactory;
use phpDocumentor\Reflection\Php\ProjectFactoryStrategy;
use phpDocumentor\Reflection\Php\StrategyContainer;
use PhpParser\Comment as CommentNode;
use PhpParser\Comment\Doc as DocBlockNode;
use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Class_ as ClassNode;
use PhpParser\Node\Stmt\Namespace_ as NamespaceNode;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use stdClass;

use function current;
use function file_get_contents;

#[CoversClass(File::class)]
#[CoversClass(AbstractFactory::class)]
#[UsesClass('\phpDocumentor\Reflection\Php\File')]
#[UsesClass('\phpDocumentor\Reflection\File\LocalFile')]
#[UsesClass('\phpDocumentor\Reflection\Middleware\ChainFactory')]
#[UsesClass('\phpDocumentor\Reflection\Php\Class_')]
#[UsesClass('\phpDocumentor\Reflection\Php\Trait_')]
#[UsesClass('\phpDocumentor\Reflection\Php\Interface_')]
#[UsesClass('\phpDocumentor\Reflection\Php\Function_')]
#[UsesClass('\phpDocumentor\Reflection\Php\Constant')]
#[UsesClass('\phpDocumentor\Reflection\Php\Visibility')]
#[UsesClass('\phpDocumentor\Reflection\Php\Factory\GlobalConstantIterator')]
#[UsesClass('\phpDocumentor\Reflection\Types\NamespaceNodeToContext')]
#[UsesClass('\phpDocumentor\Reflection\Php\Factory\File\CreateCommand')]
final class FileTest extends TestCase
{
    use ProphecyTrait;

    private ObjectProphecy $nodesFactoryMock;

    private ObjectProphecy $docBlockFactory;

    protected function setUp(): void
    {
        $this->docBlockFactory = $this->prophesize(DocBlockFactoryInterface::class);
        $this->nodesFactoryMock = $this->prophesize(NodesFactory::class);
        $this->fixture = new File($this->docBlockFactory->reveal(), $this->nodesFactoryMock->reveal());
    }

    public function testMatches(): void
    {
        $this->assertFalse($this->fixture->matches(self::createContext(null), new stdClass()));
        $this->assertTrue($this->fixture->matches(self::createContext(null), m::mock(SourceFile::class)));
    }

    public function testMiddlewareIsExecuted(): void
    {
        $file = new FileElement('aa', __FILE__);
        $this->nodesFactoryMock->create(file_get_contents(__FILE__), Argument::any())->willReturn([]);
        $middleware = $this->prophesize(Middleware::class);
        $middleware->execute(Argument::any(), Argument::any())->shouldBeCalled()->willReturn($file);
        $fixture = new File(
            $this->docBlockFactory->reveal(),
            $this->nodesFactoryMock->reveal(),
            [$middleware->reveal()],
        );
        $context = self::createContext();
        $containerMock = $this->prophesize(StrategyContainer::class);

        $fixture->create($context, new SourceFile\LocalFile(__FILE__), $containerMock->reveal());

        $result = current($context->getProject()->getFiles());
        $this->assertSame($result, $file);
    }

    public function testMiddlewareIsChecked(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new File($this->docBlockFactory->reveal(), $this->nodesFactoryMock->reveal(), [new stdClass()]);
    }

    #[DataProvider('nodeProvider')]
    public function testFileGetsCommentFromFirstNode(Node $node, DocBlockDescriptor $docblock): void
    {
        $this->nodesFactoryMock->create(file_get_contents(__FILE__), Argument::any())->willReturn([$node]);
        $this->docBlockFactory->create('Text', Argument::any())->willReturn($docblock);

        $strategies = $this->prophesize(StrategyContainer::class);
        $strategies->findMatching(Argument::type(ContextStack::class), $node)->willReturn(
            $this->prophesize(ProjectFactoryStrategy::class)->reveal(),
        );

        $context = self::createContext();

        $this->fixture->create($context, new SourceFile\LocalFile(__FILE__), $strategies->reveal());

        $file = current($context->getProject()->getFiles());
        $this->assertInstanceOf(FileElement::class, $file);
        $this->assertSame($docblock, $file->getDocBlock());
    }

    /** @return array<string, mixed[]> */
    public static function nodeProvider(): array
    {
        $docBlockNode = new DocBlockNode('Text');
        $namespaceNode = new NamespaceNode(new Name('mySpace'));
        $namespaceNode->getAttribute('fsqen', new Fqsen('\mySpace'));
        $namespaceNode->setAttribute('comments', [$docBlockNode]);

        $classNode = new ClassNode('myClass');
        $classNode->setAttribute('comments', [$docBlockNode, new DocBlockNode('')]);

        $namespaceNode2 = new NamespaceNode(new Name('mySpace'));
        $namespaceNode2->getAttribute('fsqen', new Fqsen('\mySpace'));
        $namespaceNode2->setAttribute('comments', [new CommentNode('@codingStandardsIgnoreStart'), $docBlockNode]);

        return [
            'With namespace' => [
                $namespaceNode,
                new DocBlockDescriptor(''),
            ],
            'With class' => [
                $classNode,
                new DocBlockDescriptor(''),
            ],
            'With comments' => [
                $namespaceNode2,
                new DocBlockDescriptor(''),
            ],
        ];
    }
}
