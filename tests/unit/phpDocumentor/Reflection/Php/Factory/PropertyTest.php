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

use phpDocumentor\Descriptor\Property as PropertyDescriptor;
use phpDocumentor\Reflection\Php\ProjectFactoryStrategies;
use Mockery as m;
use PhpParser\Node\Stmt\Property as PropertyNode;

/**
 * Class ArgumentTest
 * @coversDefaultClass \phpDocumentor\Reflection\Php\Factory\Property
 */
class PropertyTest extends TestCase
{
    protected function setUp()
    {
        $this->fixture = new Property();
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
    public function testCreate()
    {
        $factory = new ProjectFactoryStrategies(array());

        $argMock = m::mock(PropertyNode::class);
        $argMock->name = '\myClass::$property';
        $argMock->default = 'MyDefault';
        $argMock->static = true;
        $argMock->visibility = 'private';

        /** @var PropertyDescriptor $argument */
        $argument = $this->fixture->create($argMock, $factory);

        $this->assertInstanceOf(PropertyDescriptor::class, $argument);
        $this->assertEquals('\myClass::$property', (string)$argument->getFqsen());
        $this->assertTrue($argument->isStatic());
        $this->assertEquals('MyDefault', $argument->getDefault());
    }
}
