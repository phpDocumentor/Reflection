<?php
/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright 2010-2018 Mike van Riel<mike@phpdoc.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Reflection\File;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass phpDocumentor\Reflection\File\LocalFile
 * @covers ::__construct
 */
class LocalFileTest extends TestCase
{
    /**
     * @covers ::getContents
     */
    public function testGetContents()
    {
        $file = new LocalFile(__FILE__);
        $this->assertStringEqualsFile(__FILE__, $file->getContents());
    }

    /**
     * @covers ::md5
     */
    public function testMd5()
    {
        $file = new LocalFile(__FILE__);
        $this->assertEquals(md5_file(__FILE__), $file->md5());
    }

    /**
     * @covers ::path
     */
    public function testPath()
    {
        $file = new LocalFile(__FILE__);
        $this->assertEquals(__FILE__, $file->path());
    }
}
