<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.5
 *
 * @copyright 2010-2015 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Reflection\Php;

/**
 * Test case for Visibility
 * @coversDefaultClass phpDocumentor\Reflection\Php\Visibility
 */
class VisibilityTest extends \PHPUnit_Framework_TestCase
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
