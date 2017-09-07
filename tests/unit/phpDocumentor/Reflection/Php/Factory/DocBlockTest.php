<?php
/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright 2010-2015 Mike van Riel<mike@phpdoc.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Reflection\Php\Factory;

use Mockery as m;
use phpDocumentor\Reflection\DocBlock as DocblockDescriptor;
use phpDocumentor\Reflection\DocBlockFactoryInterface;
use phpDocumentor\Reflection\Php\StrategyContainer;
use PhpParser\Comment\Doc;

/**
 * Test case for \phpDocumentor\Reflection\Php\Factory\DocBlock
 * @coversDefaultClass \phpDocumentor\Reflection\Php\Factory\DocBlock
 */
class DocBlockTest extends TestCase
{
    /**
     * @var m\MockInterface
     */
    private $factoryMock;

    private $strategiesMock;

    protected function setUp()
    {
        $this->factoryMock = m::mock(DocBlockFactoryInterface::class);
        $this->strategiesMock = m::mock(StrategyContainer::class);
        $this->fixture = new DocBlock($this->factoryMock);
    }

    /**
     * @covers ::matches
     */
    public function testMatches()
    {
        $this->assertFalse($this->fixture->matches(new \stdClass()));
        $this->assertTrue($this->fixture->matches(m::mock(Doc::class)));
    }

    /**
     * @covers ::create
     */
    public function testCreateWithNullReturnsNull()
    {
        $this->assertNull($this->fixture->create(null, $this->strategiesMock));
    }

    /**
     * @covers ::__construct
     * @covers ::create
     */
    public function testCreateCallsFactory()
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
