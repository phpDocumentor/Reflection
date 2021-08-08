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

namespace phpDocumentor\Reflection\File;

use PHPUnit\Framework\TestCase;

use function md5_file;

/**
 * @coversDefaultClass phpDocumentor\Reflection\File\LocalFile
 * @covers ::__construct
 */
class LocalFileTest extends TestCase
{
    /**
     * @covers ::getContents
     */
    public function testGetContents(): void
    {
        $file = new LocalFile(__FILE__);
        $this->assertStringEqualsFile(__FILE__, $file->getContents());
    }

    /**
     * @covers ::md5
     */
    public function testMd5(): void
    {
        $file = new LocalFile(__FILE__);
        $this->assertEquals(md5_file(__FILE__), $file->md5());
    }

    /**
     * @covers ::__construct
     */
    public function testNotExistingFileThrowsException(): void
    {
        $this->expectException('InvalidArgumentException');
        new LocalFile('aa');
    }

    /**
     * @covers ::path
     */
    public function testPath(): void
    {
        $file = new LocalFile(__FILE__);
        $this->assertEquals(__FILE__, $file->path());
    }
}
