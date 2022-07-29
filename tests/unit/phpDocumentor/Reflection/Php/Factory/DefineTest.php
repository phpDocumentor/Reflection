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

use phpDocumentor\Reflection\DocBlock as DocBlockDescriptor;
use phpDocumentor\Reflection\DocBlockFactoryInterface;
use phpDocumentor\Reflection\Php\Constant as ConstantDescriptor;
use phpDocumentor\Reflection\Php\File as FileElement;
use phpDocumentor\Reflection\Php\ProjectFactoryStrategies;
use phpDocumentor\Reflection\Types\Context;
use PhpParser\Comment\Doc;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\Exit_;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Expression;
use PhpParser\PrettyPrinter\Standard as PrettyPrinter;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use stdClass;

use function current;

/**
 * @uses   \phpDocumentor\Reflection\Php\ProjectFactoryStrategies
 * @uses   \phpDocumentor\Reflection\Php\Constant
 * @uses   \phpDocumentor\Reflection\Php\Visibility
 *
 * @covers \phpDocumentor\Reflection\Php\Factory\Define
 * @covers \phpDocumentor\Reflection\Php\Factory\AbstractFactory
 */
final class DefineTest extends TestCase
{
    use ProphecyTrait;

    private ObjectProphecy $docBlockFactory;

    protected function setUp(): void
    {
        $this->docBlockFactory = $this->prophesize(DocBlockFactoryInterface::class);
        $this->fixture = new Define($this->docBlockFactory->reveal(), new PrettyPrinter());
    }

    public function testMatches(): void
    {
        $invalidExpressionType = new Expression(new Exit_());
        $invalidFunctionCall = new Expression(new FuncCall(new Name('print')));

        $this->assertFalse($this->fixture->matches(self::createContext(null), new stdClass()));
        $this->assertFalse($this->fixture->matches(self::createContext(null), $invalidExpressionType));
        $this->assertFalse($this->fixture->matches(self::createContext(null), $invalidFunctionCall));
        $this->assertTrue($this->fixture->matches(self::createContext(null), $this->buildDefineStub()));
    }

    public function testCreate(): void
    {
        $constantStub = $this->buildDefineStub();
        $file = new FileElement('hash', 'path');
        $contextStack = self::createContext(new Context('Space\\MyClass'))->push($file);

        $this->fixture->create($contextStack, $constantStub, new ProjectFactoryStrategies([]));

        $constant = current($file->getConstants());

        $this->assertConstant($constant, '');
    }

    public function testCreateNamespace(): void
    {
        $constantStub = $this->buildDefineStub('\\OtherSpace\\MyClass');
        $file = new FileElement('hash', 'path');
        $contextStack = self::createContext(new Context('Space\\MyClass'))->push($file);

        $this->fixture->create($contextStack, $constantStub, new ProjectFactoryStrategies([]));

        $constant = current($file->getConstants());

        $this->assertConstant($constant, '\\OtherSpace\\MyClass');
    }

    public function testCreateGlobal(): void
    {
        $constantStub = $this->buildDefineStub();
        $file = new FileElement('hash', 'path');
        $contextStack = self::createContext()->push($file);

        $this->fixture->create($contextStack, $constantStub, new ProjectFactoryStrategies([]));

        $constant = current($file->getConstants());

        $this->assertConstant($constant, '');
    }

    public function testCreateWithDocBlock(): void
    {
        $doc = new Doc('Text');
        $docBlock = new DocBlockDescriptor('');

        $constantStub = new Expression(
            new FuncCall(
                new Name('define'),
                [
                    new Arg(new String_('MY_CONST1')),
                    new Arg(new String_('a')),
                ]
            ),
            ['comments' => [$doc]]
        );
        $typeContext = new Context('Space\\MyClass');

        $this->docBlockFactory->create('Text', $typeContext)->willReturn($docBlock);

        $file = new FileElement('hash', 'path');
        $contextStack = self::createContext($typeContext)->push($file);

        $this->fixture->create($contextStack, $constantStub, new ProjectFactoryStrategies([]));

        $constant = current($file->getConstants());
        $this->assertConstant($constant, '');
        $this->assertSame($docBlock, $constant->getDocBlock());
    }

    private function buildDefineStub(string $namespace = ''): Expression
    {
        return new Expression(
            new FuncCall(
                new Name('define'),
                [
                    new Arg(new String_($namespace ?  $namespace . '\\MY_CONST1' : 'MY_CONST1')),
                    new Arg(new String_('a')),
                ]
            )
        );
    }

    private function assertConstant(ConstantDescriptor $constant, string $namespace): void
    {
        $this->assertInstanceOf(ConstantDescriptor::class, $constant);
        $this->assertEquals($namespace . '\\MY_CONST1', (string) $constant->getFqsen());
        $this->assertEquals('\'a\'', $constant->getValue());
        $this->assertEquals('public', (string) $constant->getVisibility());
    }
}
