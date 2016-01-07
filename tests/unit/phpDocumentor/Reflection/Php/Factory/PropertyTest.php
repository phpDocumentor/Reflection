<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.5
 *
 * @copyright 2010-2015 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Reflection\Php\Factory;

use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Php\Property as PropertyDescriptor;
use phpDocumentor\Reflection\DocBlock as DocBlockDescriptor;
use phpDocumentor\Reflection\Php\ProjectFactoryStrategies;
use Mockery as m;
use phpDocumentor\Reflection\Php\StrategyContainer;
use phpDocumentor\Reflection\PrettyPrinter;
use phpDocumentor\Reflection\Types\Context;
use PhpParser\Comment\Doc;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Class_ as ClassNode;
use PhpParser\Node\Stmt\Property as PropertyNode;
use PhpParser\Node\Stmt\PropertyProperty;

/**
 * Class ArgumentTest
 * @coversDefaultClass \phpDocumentor\Reflection\Php\Factory\Property
 * @covers ::<!public>
 * @covers ::__construct
 */
class PropertyTest extends TestCase
{
    protected function setUp()
    {
        $this->fixture = new Property(new PrettyPrinter());
    }

    /**
     * @covers ::matches
     */
    public function testMatches()
    {
        $this->assertFalse($this->fixture->matches(new \stdClass()));
        $this->assertTrue($this->fixture->matches(new PropertyIterator(new PropertyNode(1, []))));
    }

    /**
     * @covers ::create
     */
    public function testPrivateCreate()
    {
        $factory = new ProjectFactoryStrategies(array());

        $propertyMock = $this->buildPropertyMock(ClassNode::MODIFIER_PRIVATE);

        /** @var PropertyDescriptor $property */
        $property = $this->fixture->create($propertyMock, $factory);

        $this->assertProperty($property, 'private');
    }

    /**
     * @covers ::create
     */
    public function testProtectedCreate()
    {
        $factory = new ProjectFactoryStrategies(array());

        $propertyMock = $this->buildPropertyMock(ClassNode::MODIFIER_PROTECTED);

        /** @var PropertyDescriptor $property */
        $property = $this->fixture->create($propertyMock, $factory);

        $this->assertProperty($property, 'protected');
    }

    /**
     * @covers ::create
     */
    public function testCreatePublic()
    {
        $factory = new ProjectFactoryStrategies(array());

        $propertyMock = $this->buildPropertyMock(ClassNode::MODIFIER_PUBLIC);

        /** @var PropertyDescriptor $property */
        $property = $this->fixture->create($propertyMock, $factory);

        $this->assertProperty($property, 'public');
    }

    /**
     * @covers ::create
     */
    public function testCreateWithDocBlock()
    {
        $doc = m::mock(Doc::class);
        $docBlock = new DocBlockDescriptor('');

        $property = new PropertyProperty('property', new String_('MyDefault'), ['comments' => [$doc]]);
        $property->fqsen = new Fqsen('\myClass::$property');
        $node = new PropertyNode(ClassNode::MODIFIER_PRIVATE | ClassNode::MODIFIER_STATIC, [$property]);

        $propertyMock = new PropertyIterator($node);

        $containerMock = m::mock(StrategyContainer::class);
        $containerMock->shouldReceive('findMatching->create')
            ->once()
            ->with($doc, $containerMock, null)
            ->andReturn($docBlock);

        /** @var PropertyDescriptor $property */
        $property = $this->fixture->create($propertyMock, $containerMock);

        $this->assertProperty($property, 'private');
        $this->assertSame($docBlock, $property->getDocBlock());
    }


    /**
     * @return PropertyIterator
     */
    private function buildPropertyMock($modifier)
    {
        $property = new PropertyProperty('property', new String_('MyDefault'));
        $property->fqsen = new Fqsen('\myClass::$property');
        $propertyMock = new PropertyIterator(new PropertyNode($modifier | ClassNode::MODIFIER_STATIC, [$property]));

        return $propertyMock;
    }

    /**
     * @param PropertyDescriptor $property
     * @param string $visibility
     */
    private function assertProperty($property, $visibility)
    {
        $this->assertInstanceOf(PropertyDescriptor::class, $property);
        $this->assertEquals('\myClass::$property', (string)$property->getFqsen());
        $this->assertTrue($property->isStatic());
        $this->assertEquals('MyDefault', $property->getDefault());
        $this->assertEquals($visibility, (string)$property->getVisibility());
    }
}
