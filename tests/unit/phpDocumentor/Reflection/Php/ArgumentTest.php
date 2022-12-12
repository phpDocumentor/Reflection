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

namespace phpDocumentor\Reflection\Php;

use phpDocumentor\Reflection\Types\Mixed_;
use phpDocumentor\Reflection\Types\String_;
use PHPUnit\Framework\TestCase;

/**
 * Tests the functionality for the Argument class.
 *
 * @coversDefaultClass \phpDocumentor\Reflection\Php\Argument
 * @covers ::__construct
 * @covers ::<private>
 * @covers ::<protected>
 */
final class ArgumentTest extends TestCase
{
    /**
     * @covers ::getType
     */
    public function testGetTypes(): void
    {
        $argument = new Argument('myArgument', null, new Expression('myDefaultValue'), true, true);
        self::assertInstanceOf(Mixed_::class, $argument->getType());

        $argument = new Argument(
            'myArgument',
            new String_(),
            new Expression('myDefaultValue'),
            true,
            true
        );
        self::assertEquals(new String_(), $argument->getType());
    }

    /**
     * @covers ::getName
     */
    public function testGetName(): void
    {
        $argument = new Argument('myArgument', null, new Expression('myDefault'), true, true);

        self::assertEquals('myArgument', $argument->getName());
    }

    /**
     * @covers ::getDefault
     */
    public function testGetDefault(): void
    {
        $argument = new Argument('myArgument', null, new Expression('myDefaultValue'), true, true);
        self::assertEquals(new Expression('myDefaultValue'), $argument->getDefault());

        $argument = new Argument('myArgument', null, null, true, true);
        self::assertNull($argument->getDefault());
    }

    /**
     * @covers ::isByReference
     */
    public function testGetWhetherArgumentIsPassedByReference(): void
    {
        $argument = new Argument('myArgument', null, new Expression('myDefaultValue'), true, true);
        self::assertTrue($argument->isByReference());

        $argument = new Argument('myArgument', null, null, false, true);
        self::assertFalse($argument->isByReference());
    }

    /**
     * @covers ::isVariadic
     */
    public function testGetWhetherArgumentisVariadic(): void
    {
        $argument = new Argument('myArgument', null, new Expression('myDefaultValue'), true, true);
        self::assertTrue($argument->isVariadic());

        $argument = new Argument('myArgument', null, new Expression('myDefaultValue'), true, false);
        self::assertFalse($argument->isVariadic());
    }
}
