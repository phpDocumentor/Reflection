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

namespace phpDocumentor\Reflection;

use PhpParser\Node\Scalar\String_;
use PHPUnit\Framework\TestCase;

/**
 * Class for testing the PrettyPrinter.
 *
 * @link      http://phpdoc.org
 */
class PrettyPrinterTest extends TestCase
{
    /**
     * @covers \phpDocumentor\Reflection\PrettyPrinter::pScalar_String
     */
    public function testScalarStringPrinting() : void
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
