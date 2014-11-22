<?php

namespace phpDocumentor\SimpleFilter;

use \Mockery as m;

class ChainTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Chain
     */
    private $chain;

    public function setUp()
    {
        $this->chain = new Chain();
    }

    public function testAttachFilterProperty()
    {
        /** @var FilterInterface $filterMock */
        $filterMock = m::mock('phpDocumentor\SimpleFilter\FilterInterface');

        $filter = $this->chain->attach($filterMock);
        $this->assertInstanceOf('phpDocumentor\SimpleFilter\Chain', $filter);
    }

    public function testAttachCallbackProperty()
    {
        $callback = $this->chain->attach(function () {});
        $filter = $this->chain->attach($callback);

        $this->assertInstanceOf('phpDocumentor\SimpleFilter\Chain', $filter);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testAttachInvalidProperty()
    {
        $this->chain->attach(null);
    }

    /**
     * @covers \phpDocumentor\SimpleFilter\Chain::filter
     */
    public function testFilterCallableProperty()
    {
        $value = function ($input) { return 'foo' . $input; };

        $this->chain->attach($value);
        $filter = $this->chain->filter('bar');

        $this->assertSame('foobar', $filter);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testFilterInvalidProperty()
    {
        $chain = new \ReflectionClass($this->chain);
        $mockedQueue = new \SplPriorityQueue();
        $mockedQueue->insert('foo', 1000);

        $innerQueue = $chain->getProperty('innerQueue');
        $innerQueue->setAccessible(true);
        $innerQueue->setValue($this->chain, $mockedQueue);

        $this->chain->filter($innerQueue);
    }

    public function testCountIncreasesAfterEachAttach()
    {
        $this->assertCount(0, $this->chain);

        for ($i = 1; $i < 5; $i++) {
            $this->chain->attach(function () {});
            $this->assertCount($i, $this->chain);
        }
    }

    public function testGetIterator()
    {
        $iterator = $this->chain->getIterator();
        $this->assertInstanceOf('SplPriorityQueue', $iterator);
    }
}
