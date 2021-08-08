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

namespace phpDocumentor\Reflection\Php\Factory\File;

use phpDocumentor\Reflection\File\LocalFile;
use phpDocumentor\Reflection\Php\Factory\ContextStack;
use phpDocumentor\Reflection\Php\Project;
use phpDocumentor\Reflection\Php\ProjectFactoryStrategies;
use PHPUnit\Framework\TestCase;

/**
 * @uses phpDocumentor\Reflection\File\LocalFile
 * @uses phpDocumentor\Reflection\Php\ProjectFactoryStrategies
 *
 * @coversDefaultClass phpDocumentor\Reflection\Php\Factory\File\CreateCommand
 * @covers ::__construct
 */
class CreateCommandTest extends TestCase
{
    /** @var CreateCommand */
    private $fixture;

    /** @var LocalFile */
    private $file;

    /** @var ProjectFactoryStrategies */
    private $strategies;

    protected function setUp(): void
    {
        $this->file       = new LocalFile(__FILE__);
        $this->strategies = new ProjectFactoryStrategies([]);
        $this->fixture    = new CreateCommand(
            new ContextStack(new Project('test')),
            $this->file,
            $this->strategies
        );
    }

    /**
     * @covers ::getFile
     */
    public function testGetFile(): void
    {
        $this->assertSame($this->file, $this->fixture->getFile());
    }

    /**
     * @covers ::getStrategies
     */
    public function testGetStrategies(): void
    {
        $this->assertSame($this->strategies, $this->fixture->getStrategies());
    }
}
