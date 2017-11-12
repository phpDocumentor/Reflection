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

namespace phpDocumentor\Reflection\Php;

use PHPUnit\Framework\TestCase;

/**
 * Test case for Visibility
 * @coversDefaultClass phpDocumentor\Reflection\Php\Visibility
 */
class VisibilityTest extends TestCase
{
    /**
     * @param $input
     * @param $expected
     *
     * @dataProvider visibilityProvider
     *
     * @covers ::__construct
     * @covers ::__toString
     */
    public function testVisibility($input, $expected)
    {
        $visibility = new Visibility($input);

        $this->assertEquals($expected, (string)$visibility);
    }

    public function visibilityProvider()
    {
        return [
            ['public', 'public'],
            ['protected', 'protected'],
            ['private', 'private'],
            ['PrIvate', 'private'],
        ];
    }

    /**
     * @expectedException \InvalidArgumentException
     * @covers ::__construct
     */
    public function testVisibilityChecksInput()
    {
        new Visibility('fooBar');
    }
}
