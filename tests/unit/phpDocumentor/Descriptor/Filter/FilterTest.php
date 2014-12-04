<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2013 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Descriptor\Filter;

use \Mockery as m;
use phpDocumentor\SimpleFilter\Chain;

/**
 * Tests the functionality for the Filter class.
 */
class FilterTest extends \PHPUnit_Framework_TestCase
{
    const FQCN = 'SomeFilterClass';

    /** @var ClassFactory|m\Mock */
    protected $classFactoryMock;

    /** @var Chain|m\Mock */
    protected $chainMock;

    /** @var Filter $fixture */
    protected $fixture;

    /**
     * Creates a new (empty) fixture object.
     */
    protected function setUp()
    {
        $this->classFactoryMock = m::mock('phpDocumentor\Descriptor\Filter\ClassFactory');
        $this->chainMock        = m::mock(new Chain());
        $this->fixture          = new Filter($this->classFactoryMock);
    }

    /**
     * @covers phpDocumentor\Descriptor\Filter\Filter::__construct
     */
    public function testClassFactoryIsSetUponConstruction()
    {
        $this->assertAttributeSame($this->classFactoryMock, 'factory', $this->fixture);
    }

    /**
     * @covers phpDocumentor\Descriptor\Filter\Filter::attach
     */
    public function testAttach()
    {
        $filterMock = m::mock('phpDocumentor\SimpleFilter\FilterInterface');

        $this->chainMock->shouldReceive('attach')->with($filterMock, Chain::DEFAULT_PRIORITY);
        $this->classFactoryMock->shouldReceive('getChainFor')->with(self::FQCN)->andReturn($this->chainMock);

        $this->fixture->attach(self::FQCN, $filterMock);
    }

    /**
     * @covers phpDocumentor\Descriptor\Filter\Filter::filter
     */
    public function testFilter()
    {
        $filterableMock = m::mock('phpDocumentor\Descriptor\Filter\Filterable');

        $this->chainMock->shouldReceive('filter')->with($filterableMock)->andReturn($filterableMock);
        $this->classFactoryMock
            ->shouldReceive('getChainFor')->with(get_class($filterableMock))->andReturn($this->chainMock);

        $this->assertSame($filterableMock, $this->fixture->filter($filterableMock));
    }
}
