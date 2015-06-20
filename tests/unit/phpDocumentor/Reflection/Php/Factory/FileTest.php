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
use phpDocumentor\Reflection\Php\Function_;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Php\NodesFactory;
use phpDocumentor\Reflection\Php\StrategyContainer;

/**
 * Test case for \phpDocumentor\Reflection\Php\Factory\File
 * @coversDefaultClass \phpDocumentor\Reflection\Php\Factory\File
 * @covers ::<private>
 * @covers ::__construct
 */
class FileTest extends TestCase
{
    /**
     * @var m\MockInterface
     */
    private $nodesFactoryMock;

    protected function setUp()
    {
        $this->nodesFactoryMock = m::mock(NodesFactory::class);
        $this->fixture = new File($this->nodesFactoryMock);
    }

    /**
     * @covers ::matches
     */
    public function testMatches()
    {
        $this->assertFalse($this->fixture->matches(new \stdClass()));
        $this->assertTrue($this->fixture->matches(__FILE__));
    }

    /**
     * @covers ::create
     */
    public function testFileWithFunction()
    {
        $functionNode = new \PhpParser\Node\Stmt\Function_('myFunction');
        $this->nodesFactoryMock->shouldReceive('create')
            ->with(file_get_contents(__FILE__))
            ->andReturn(
                [
                    $functionNode
                ]
            );

        $containerMock = m::mock(StrategyContainer::class);
        $containerMock->shouldReceive('findMatching->create')
            ->once()
            ->with($functionNode, $containerMock)
            ->andReturn(new Function_(new Fqsen('\myFunction()')));

        $this->fixture->create(__FILE__, $containerMock);
    }
}
