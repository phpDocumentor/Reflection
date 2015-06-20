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

use phpDocumentor\Reflection\Php\Property as PropertyDescriptor;
use phpDocumentor\Reflection\DocBlock as DocBlockDescriptor;
use phpDocumentor\Reflection\Php\ProjectFactoryStrategies;
use Mockery as m;
use phpDocumentor\Reflection\Php\StrategyContainer;
use phpDocumentor\Reflection\PrettyPrinter;
use PhpParser\Comment\Doc;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Property as PropertyNode;

/**
 * Class ArgumentTest
 * @coversDefaultClass \phpDocumentor\Reflection\Php\Factory\Property
 * @covers ::<private>
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
        $this->assertTrue($this->fixture->matches(m::mock(PropertyNode::class)));
    }

    /**
     * @covers ::create
     */
    public function testPrivateCreate()
    {
        $factory = new ProjectFactoryStrategies(array());

        $propertyMock = $this->buildPropertyMock();
        $propertyMock->shouldReceive('isPrivate')->once()->andReturn(true);
        $propertyMock->shouldReceive('getDocComment')->once()->andReturnNull();

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

        $propertyMock = $this->buildPropertyMock();
        $propertyMock->shouldReceive('isPrivate')->once()->andReturn(false);
        $propertyMock->shouldReceive('isProtected')->once()->andReturn(true);
        $propertyMock->shouldReceive('getDocComment')->once()->andReturnNull();

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

        $propertyMock = $this->buildPropertyMock();
        $propertyMock->shouldReceive('isPrivate')->once()->andReturn(false);
        $propertyMock->shouldReceive('isProtected')->once()->andReturn(false);
        $propertyMock->shouldReceive('getDocComment')->once()->andReturnNull();

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
        $propertyMock = $this->buildPropertyMock();
        $propertyMock->shouldReceive('isPrivate')->once()->andReturn(true);
        $propertyMock->shouldReceive('getDocComment')->andReturn($doc);

        $docBlock = new DocBlockDescriptor('');

        $containerMock = m::mock(StrategyContainer::class);
        $containerMock->shouldReceive('findMatching->create')
            ->once()
            ->with($doc, $containerMock)
            ->andReturn($docBlock);

        /** @var PropertyDescriptor $property */
        $property = $this->fixture->create($propertyMock, $containerMock);

        $this->assertProperty($property, 'private');
        $this->assertSame($docBlock, $property->getDocBlock());
    }


    /**
     * @return m\MockInterface
     */
    private function buildPropertyMock()
    {
        $propertyMock = m::mock(PropertyNode::class);
        $propertyMock->name = '\myClass::$property';
        $propertyMock->default = new String_('MyDefault');
        $propertyMock->shouldReceive('isStatic')->andReturn(true);
        return $propertyMock;
    }

    /**
     * @param PropertyDescriptor $property
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
