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

use Mockery as m;
use phpDocumentor\Reflection\DocBlock as DocblockDescriptor;
use phpDocumentor\Reflection\DocBlockFactoryInterface;
use phpDocumentor\Reflection\Php\StrategyContainer;
use PhpParser\Comment\Doc;
use stdClass;

/**
 * @uses \phpDocumentor\Reflection\Php\Factory\DocBlock::matches
 * @uses \phpDocumentor\Reflection\Php\Factory\DocBlock::create
 *
 * @coversDefaultClass \phpDocumentor\Reflection\Php\Factory\DocBlock
 * @covers \phpDocumentor\Reflection\Php\Factory\AbstractFactory
 * @covers ::__construct
 * @covers ::<private>
 * @covers ::<protected>
 */
class DocBlockTest extends TestCase
{
    /** @var m\MockInterface|DocBlockFactoryInterface */
    private $factoryMock;

    /** @var m\MockInterface|StrategyContainer */
    private $strategiesMock;

    protected function setUp() : void
    {
        $this->factoryMock    = m::mock(DocBlockFactoryInterface::class);
        $this->strategiesMock = m::mock(StrategyContainer::class);
        $this->fixture        = new DocBlock($this->factoryMock);
    }

    /**
     * @covers ::matches
     */
    public function testMatches() : void
    {
        $this->assertFalse($this->fixture->matches(new stdClass()));
        $this->assertTrue($this->fixture->matches(m::mock(Doc::class)));
    }

    /**
     * @covers ::create
     */
    public function testCreateWithNullReturnsNull() : void
    {
        $this->assertNull($this->fixture->create(null, $this->strategiesMock));
    }

    /**
     * @covers ::create
     * @covers ::matches
     */
    public function testCreateCallsFactory() : void
    {
        $expected = new DocblockDescriptor('');
        $this->factoryMock->shouldReceive('create')->once()->andReturn($expected);

        $docMock = m::mock(Doc::class);
        $docMock->shouldReceive('getText')->andReturn('');
        $docMock->shouldReceive('getLine')->andReturn(1);

        $result = $this->fixture->create($docMock, $this->strategiesMock);

        $this->assertSame($expected, $result);
    }
}
