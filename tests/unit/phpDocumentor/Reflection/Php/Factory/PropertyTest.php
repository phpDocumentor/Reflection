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
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Php\Class_ as ClassElement;
use phpDocumentor\Reflection\Php\ProjectFactoryStrategies;
use phpDocumentor\Reflection\Php\Property as PropertyDescriptor;
use PhpParser\Comment\Doc;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Class_ as ClassNode;
use PhpParser\Node\Stmt\Property as PropertyNode;
use PhpParser\Node\Stmt\PropertyProperty;
use PhpParser\PrettyPrinter\Standard as PrettyPrinter;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use stdClass;

use function current;
use function next;

/**
 * @uses \phpDocumentor\Reflection\Php\Factory\PropertyIterator
 * @uses \phpDocumentor\Reflection\Php\Property
 * @uses \phpDocumentor\Reflection\Php\Visibility
 * @uses \phpDocumentor\Reflection\Php\ProjectFactoryStrategies
 * @uses \phpDocumentor\Reflection\Php\Factory\Type
 *
 * @covers \phpDocumentor\Reflection\Php\Factory\Property
 * @covers \phpDocumentor\Reflection\Php\Factory\AbstractFactory
 */
final class PropertyTest extends TestCase
{
    use ProphecyTrait;

    private ObjectProphecy $docBlockFactory;

    protected function setUp(): void
    {
        $this->docBlockFactory = $this->prophesize(DocBlockFactoryInterface::class);
        $this->fixture = new Property($this->docBlockFactory->reveal(), new PrettyPrinter());
    }

    public function testMatches(): void
    {
        $this->assertFalse($this->fixture->matches(self::createContext(null), new stdClass()));
        $this->assertTrue($this->fixture->matches(self::createContext(null), new PropertyNode(1, [])));
    }

    /** @dataProvider visibilityProvider */
    public function testCreateWithVisibility(int $input, string $expectedVisibility): void
    {
        $constantStub = $this->buildPropertyMock($input);

        $class = $this->performCreate($constantStub);

        $property = current($class->getProperties());
        $this->assertProperty($property, $expectedVisibility);
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
        ];
    }

    public function testCreateWithDocBlock(): void
    {
        $doc = new Doc('text');
        $docBlock = new DocBlockDescriptor('text');
        $this->docBlockFactory->create('text', null)->willReturn($docBlock);

        $property = new PropertyProperty('property', new String_('MyDefault'), ['comments' => [$doc]]);
        $property->setAttribute('fqsen', new Fqsen('\myClass::$property'));
        $node = new PropertyNode(ClassNode::MODIFIER_PRIVATE | ClassNode::MODIFIER_STATIC, [$property]);
        $class = $this->performCreate($node);
        $property = current($class->getProperties());

        $this->assertProperty($property, 'private');
        $this->assertSame($docBlock, $property->getDocBlock());
    }

    public function testCreateMultipleInOneStatement(): void
    {
        $property1 = new PropertyProperty('property1', new String_('MyDefault1'));
        $property1->setAttribute('fqsen', new Fqsen('\myClass::$property1'));
        $property2 = new PropertyProperty('property2', new String_('MyDefault2'));
        $property2->setAttribute('fqsen', new Fqsen('\myClass::$property2'));
        $node = new PropertyNode(
            ClassNode::MODIFIER_PRIVATE | ClassNode::MODIFIER_STATIC,
            [$property1, $property2]
        );

        $class = $this->performCreate($node);
        $properties = $class->getProperties();
        $property1 = current($properties);
        next($properties);
        $property2 = current($properties);

        $this->assertProperty($property1, 'private', 'property1', '\'MyDefault1\'');
        $this->assertProperty($property2, 'private', 'property2', '\'MyDefault2\'');
    }

    private function buildPropertyMock(int $modifier): PropertyNode
    {
        $property = new PropertyProperty('property', new String_('MyDefault'));
        $property->setAttribute('fqsen', new Fqsen('\myClass::$property'));

        return new PropertyNode($modifier | ClassNode::MODIFIER_STATIC, [$property]);
    }

    private function assertProperty(
        PropertyDescriptor $property,
        string $visibility,
        string $name = 'property',
        ?string $default = '\'MyDefault\''
    ): void {
        $this->assertInstanceOf(PropertyDescriptor::class, $property);
        $this->assertEquals('\myClass::$' . $name, (string) $property->getFqsen());
        $this->assertTrue($property->isStatic());
        $this->assertEquals($default, $property->getDefault());
        $this->assertEquals($visibility, (string) $property->getVisibility());
    }

    private function performCreate(PropertyNode $property): ClassElement
    {
        $factory = new ProjectFactoryStrategies([]);
        $class = new ClassElement(new Fqsen('\myClass'));
        $this->fixture->create(self::createContext(null)->push($class), $property, $factory);

        return $class;
    }
}
