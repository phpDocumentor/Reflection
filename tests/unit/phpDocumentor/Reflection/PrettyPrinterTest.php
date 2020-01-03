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

namespace phpDocumentor\Reflection;

use PhpParser\Node\Scalar\String_;
use PHPUnit\Framework\TestCase;

/**
 * Class for testing the PrettyPrinter.
 *
 * @author    Vasil Rangelov <boen.robot@gmail.com>
 * @copyright 2010-2018 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */
class PrettyPrinterTest extends TestCase
{
    /**
     * @covers \phpDocumentor\Reflection\PrettyPrinter::pScalar_String
     */
    public function testScalarStringPrinting()
    {
        $object = new PrettyPrinter();
        $this->assertEquals(
            'Another value',
            $object->pScalar_String(
                new String_(
                    'Value',
                    ['originalValue' => 'Another value']
                )
            )
        );
    }
}
