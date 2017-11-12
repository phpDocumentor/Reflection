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

namespace phpDocumentor\Reflection\Php\Factory\File;

use phpDocumentor\Reflection\File\LocalFile;
use phpDocumentor\Reflection\Php\ProjectFactoryStrategies;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass phpDocumentor\Reflection\Php\Factory\File\CreateCommand
 * @covers ::__construct
 * @uses phpDocumentor\Reflection\File\LocalFile
 * @uses phpDocumentor\Reflection\Php\ProjectFactoryStrategies
 */
class CreateCommandTest extends TestCase
{
    /**
     * @var CreateCommand
     */
    private $fixture;

    /** @var LocalFile */
    private $file;

    /** @var ProjectFactoryStrategies */
    private $strategies;

    protected function setUp()
    {
        $this->file = new LocalFile(__FILE__);
        $this->strategies = new ProjectFactoryStrategies([]);
        $this->fixture = new CreateCommand($this->file, $this->strategies);
    }

    /**
     * @covers ::getFile
     */
    public function testGetFile()
    {
        $this->assertSame($this->file, $this->fixture->getFile());
    }

    /**
     * @covers ::getStrategies
     */
    public function testGetStrategies()
    {
        $this->assertSame($this->strategies, $this->fixture->getStrategies());
    }
}
