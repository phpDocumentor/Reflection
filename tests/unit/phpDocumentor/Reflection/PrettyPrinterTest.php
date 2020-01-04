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
 * @coversDefaultClass \phpDocumentor\Reflection\PrettyPrinter
 */
final class PrettyPrinterTest extends TestCase
{
    /**
     * @covers ::pScalar_String
     */
    public function testReturnsUnmodifiedValueWhenSet() : void
    {
        $object = new PrettyPrinter();
        $exampleString = new String_('Value', ['originalValue' => 'Another value']);

        $this->assertSame('Another value', $object->pScalar_String($exampleString));
    }

    /**
     * @covers ::pScalar_String
     */
    public function testReturnsModifiedValueWhenNoUnmodifiedIsSet() : void
    {
        $object = new PrettyPrinter();
        $exampleString = new String_('Value');

        $this->assertSame('Value', $object->pScalar_String($exampleString));
    }
}
