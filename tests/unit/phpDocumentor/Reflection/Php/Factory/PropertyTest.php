<?php

declare(strict_types=1);

/**
 * phpDocumentor
 *
 * PHP Version 5.5
 *
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Reflection\Php\Factory;

use Mockery as m;
use phpDocumentor\Reflection\DocBlock as DocBlockDescriptor;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Php\ProjectFactoryStrategies;
use phpDocumentor\Reflection\Php\ProjectFactoryStrategy;
use phpDocumentor\Reflection\Php\Property as PropertyDescriptor;
use phpDocumentor\Reflection\Php\StrategyContainer;
use phpDocumentor\Reflection\PrettyPrinter;
use PhpParser\Comment\Doc;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Class_ as ClassNode;
use PhpParser\Node\Stmt\Property as PropertyNode;
use PhpParser\Node\Stmt\PropertyProperty;
use stdClass;

/**
 * Class ArgumentTest
 *
 * @coversDefaultClass \phpDocumentor\Reflection\Php\Factory\Property
 * @covers ::<!public>
 * @covers ::__construct
 */
class PropertyTest extends TestCase
{
    protected function setUp() : void
    {
        $this->fixture = new Property(new PrettyPrinter());
    }

    /**
     * @covers ::matches
     */
    public function testMatches() : void
    {
        $this->assertFalse($this->fixture->matches(new stdClass()));
        $this->assertTrue($this->fixture->matches(new PropertyIterator(new PropertyNode(1, []))));
    }

    /**
     * @covers ::create
     */
    public function testPrivateCreate() : void
    {
        $factory = new ProjectFactoryStrategies([]);

        $propertyMock = $this->buildPropertyMock(ClassNode::MODIFIER_PRIVATE);

        /** @var PropertyDescriptor $property */
        $property = $this->fixture->create($propertyMock, $factory);

        $this->assertProperty($property, 'private');
    }

    /**
     * @covers ::create
     */
    public function testProtectedCreate() : void
    {
        $factory = new ProjectFactoryStrategies([]);

        $propertyMock = $this->buildPropertyMock(ClassNode::MODIFIER_PROTECTED);

        /** @var PropertyDescriptor $property */
        $property = $this->fixture->create($propertyMock, $factory);

        $this->assertProperty($property, 'protected');
    }

    /**
     * @covers ::create
     */
    public function testCreatePublic() : void
    {
        $factory = new ProjectFactoryStrategies([]);

        $propertyMock = $this->buildPropertyMock(ClassNode::MODIFIER_PUBLIC);

        /** @var PropertyDescriptor $property */
        $property = $this->fixture->create($propertyMock, $factory);

        $this->assertProperty($property, 'public');
    }

    /**
     * @covers ::create
     */
    public function testCreateWithDocBlock() : void
    {
        $doc      = m::mock(Doc::class);
        $docBlock = new DocBlockDescriptor('');

        $property        = new PropertyProperty('property', new String_('MyDefault'), ['comments' => [$doc]]);
        $property->fqsen = new Fqsen('\myClass::$property');
        $node            = new PropertyNode(ClassNode::MODIFIER_PRIVATE | ClassNode::MODIFIER_STATIC, [$property]);

        $propertyMock  = new PropertyIterator($node);
        $strategyMock  = m::mock(ProjectFactoryStrategy::class);
        $containerMock = m::mock(StrategyContainer::class);

        $strategyMock->shouldReceive('create')
            ->with($doc, $containerMock, null)
            ->andReturn($docBlock);

        $containerMock->shouldReceive('findMatching')
            ->with($doc)
            ->andReturn($strategyMock);

        /** @var PropertyDescriptor $property */
        $property = $this->fixture->create($propertyMock, $containerMock);

        $this->assertProperty($property, 'private');
        $this->assertSame($docBlock, $property->getDocBlock());
    }

    private function buildPropertyMock(int $modifier) : PropertyIterator
    {
        $property        = new PropertyProperty('property', new String_('MyDefault'));
        $property->fqsen = new Fqsen('\myClass::$property');
        return new PropertyIterator(new PropertyNode($modifier | ClassNode::MODIFIER_STATIC, [$property]));
    }

    private function assertProperty(PropertyDescriptor $property, string $visibility) : void
    {
        $this->assertInstanceOf(PropertyDescriptor::class, $property);
        $this->assertEquals('\myClass::$property', (string) $property->getFqsen());
        $this->assertTrue($property->isStatic());
        $this->assertEquals('MyDefault', $property->getDefault());
        $this->assertEquals($visibility, (string) $property->getVisibility());
    }
}
