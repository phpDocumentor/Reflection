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

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * Test case for Visibility
 *
 * @coversDefaultClass \phpDocumentor\Reflection\Php\Visibility
 */
class VisibilityTest extends TestCase
{
    /**
     * @dataProvider visibilityProvider
     * @covers ::__construct
     * @covers ::__toString
     */
    public function testVisibility(string $input, string $expected): void
    {
        $visibility = new Visibility($input);

        $this->assertEquals($expected, (string) $visibility);
    }

    /**
     * @return string[][]
     */
    public function visibilityProvider(): array
    {
        return [
            ['public', 'public'],
            ['protected', 'protected'],
            ['private', 'private'],
            ['PrIvate', 'private'],
        ];
    }

    /**
     * @covers ::__construct
     */
    public function testVisibilityChecksInput(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Visibility('fooBar');
    }
}
