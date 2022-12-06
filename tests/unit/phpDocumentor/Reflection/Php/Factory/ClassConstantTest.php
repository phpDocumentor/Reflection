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
use phpDocumentor\Reflection\Element;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Php\Class_ as ClassElement;
use phpDocumentor\Reflection\Php\Constant as ConstantDescriptor;
use phpDocumentor\Reflection\Php\Enum_ as EnumElement;
use phpDocumentor\Reflection\Php\Interface_ as InterfaceElement;
use phpDocumentor\Reflection\Php\ProjectFactoryStrategies;
use phpDocumentor\Reflection\Php\Trait_ as TraitElement;
use phpDocumentor\Reflection\Types\Null_;
use PhpParser\Comment\Doc;
use PhpParser\Node\Const_;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Class_ as ClassNode;
use PhpParser\Node\Stmt\ClassConst;
use PhpParser\PrettyPrinter\Standard as PrettyPrinter;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use stdClass;

use function current;

/**
 * @uses   \phpDocumentor\Reflection\Php\Factory\ClassConstantIterator
 * @uses   \phpDocumentor\Reflection\Php\ProjectFactoryStrategies
 * @uses   \phpDocumentor\Reflection\Php\Constant
 * @uses   \phpDocumentor\Reflection\Php\Visibility
 *
 * @covers \phpDocumentor\Reflection\Php\Factory\ClassConstant
 * @covers \phpDocumentor\Reflection\Php\Factory\AbstractFactory
 */
final class ClassConstantTest extends TestCase
{
    use ProphecyTrait;

    private ObjectProphecy $docBlockFactory;

    protected function setUp(): void
    {
        $this->docBlockFactory = $this->prophesize(DocBlockFactoryInterface::class);
        $this->fixture = new ClassConstant(
            $this->docBlockFactory->reveal(),
            new PrettyPrinter()
        );
    }

    public function testMatches(): void
    {
        $this->assertFalse($this->fixture->matches(self::createContext(null), new stdClass()));
        $this->assertTrue($this->fixture->matches(self::createContext(null), $this->buildConstantIteratorStub()));
    }

    /** @dataProvider visibilityProvider */
    public function testCreateWithVisibility(int $input, string $expectedVisibility, bool $isFinal = false): void
    {
        $constantStub = $this->buildConstantIteratorStub($input);

        $class = $this->performCreate($constantStub);

        $constant = current($class->getConstants());
        $this->assertConstant($constant, $expectedVisibility);
        $this->assertSame($isFinal, $constant->isFinal());
    }

    /** @return array<string|int[]> */
    public function visibilityProvider(): array
    {
        return [
            [
                ClassNode::MODIFIER_PUBLIC,
                'public',
            ],
            [
                ClassNode::MODIFIER_PROTECTED,
                'protected',
            ],
            [
                ClassNode::MODIFIER_PRIVATE,
                'private',
            ],
            [
                ClassNode::MODIFIER_PRIVATE | ClassNode::MODIFIER_FINAL,
                'private',
                true,
            ],
        ];
    }

    public function testCreateForInterface(): void
    {
        $interface = new InterfaceElement(new Fqsen('\myInterface'));
        $const = new Const_('\Space\MyInterface::MY_CONST1', new String_('a'));
        $const->setAttribute('fqsen', new Fqsen((string) $const->name));
        $constantStub = new ClassConst([$const], ClassNode::MODIFIER_PUBLIC);

        $result = $this->performCreateWith($constantStub, $interface);

        self::assertInstanceOf(ConstantDescriptor::class, current($result->getConstants()));
    }

    public function testCreateForTrait(): void
    {
        $trait = new TraitElement(new Fqsen('\myTrait'));
        $const = new Const_('\Space\MyTrait::MY_CONST1', new String_('a'));
        $const->setAttribute('fqsen', new Fqsen((string) $const->name));
        $constantStub = new ClassConst([$const], ClassNode::MODIFIER_PUBLIC);

        $result = $this->performCreateWith($constantStub, $trait);

        self::assertInstanceOf(ConstantDescriptor::class, current($result->getConstants()));
    }

    public function testCreateForEnum(): void
    {
        $enum = new EnumElement(new Fqsen('\myEnum'), new Null_());
        $const = new Const_('\Space\MyEnum::MY_CONST1', new String_('a'));
        $const->setAttribute('fqsen', new Fqsen((string) $const->name));
        $constantStub = new ClassConst([$const], ClassNode::MODIFIER_PUBLIC);

        $result = $this->performCreateWith($constantStub, $enum);

        self::assertInstanceOf(ConstantDescriptor::class, current($result->getConstants()));
    }

    public function testCreateWithDocBlock(): void
    {
        $doc = new Doc('text');
        $docBlock = new DocBlockDescriptor('text');
        $this->docBlockFactory->create('text', null)->willReturn($docBlock);

        $const = new Const_('\Space\MyClass::MY_CONST1', new String_('a'), ['comments' => [$doc]]);
        $const->setAttribute('fqsen', new Fqsen((string) $const->name));
        $constantStub = new ClassConst([$const], ClassNode::MODIFIER_PUBLIC);

        $class = $this->performCreate($constantStub);

        $constant = current($class->getConstants());
        $this->assertConstant($constant, 'public');
        $this->assertSame($docBlock, $constant->getDocBlock());
    }

    private function buildConstantIteratorStub(int $modifier = ClassNode::MODIFIER_PUBLIC): ClassConst
    {
        $const = new Const_('\Space\MyClass::MY_CONST1', new String_('a'));
        $const->setAttribute('fqsen', new Fqsen((string) $const->name));

        return new ClassConst([$const], $modifier);
    }

    private function assertConstant(ConstantDescriptor $constant, string $visibility): void
    {
        $this->assertInstanceOf(ConstantDescriptor::class, $constant);
        $this->assertEquals('\Space\MyClass::MY_CONST1', (string) $constant->getFqsen());
        $this->assertEquals('\'a\'', $constant->getValue());
        $this->assertEquals($visibility, (string) $constant->getVisibility());
    }

    private function performCreate(ClassConst $constantStub): ClassElement
    {
        $class = new ClassElement(new Fqsen('\myClass'));
        $this->performCreateWith($constantStub, $class);

        return $class;
    }

    private function performCreateWith(ClassConst $constantStub, Element $parent): Element
    {
        $factory = new ProjectFactoryStrategies([]);
        $this->fixture->create(self::createContext(null)->push($parent), $constantStub, $factory);

        return $parent;
    }
}
