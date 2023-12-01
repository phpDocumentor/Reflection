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

use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Location;
use phpDocumentor\Reflection\Metadata\MetaDataContainer as MetaDataContainerInterface;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \phpDocumentor\Reflection\Php\EnumCase
 * @covers ::__construct
 * @covers ::<private>
 * @covers ::<protected>
 */
final class EnumCaseTest extends TestCase
{
    use MetadataContainerTest;

    private EnumCase $fixture;

    private Fqsen $fqsen;

    private DocBlock $docBlock;

    /**
     * Creates a new (empty) fixture object.
     */
    protected function setUp(): void
    {
        $this->fqsen    = new Fqsen('\Enum::VALUE');
        $this->docBlock = new DocBlock('');

        // needed for MetaDataContainer testing
        $this->fixture = new EnumCase(
            $this->fqsen,
            $this->docBlock
        );
    }

    private function getFixture(): MetaDataContainerInterface
    {
        return $this->fixture;
    }

    /**
     * @covers ::getName
     */
    public function testGettingName(): void
    {
        $fixture = new EnumCase(
            $this->fqsen,
            $this->docBlock
        );

        $this->assertSame($this->fqsen->getName(), $fixture->getName());
    }

    /**
     * @covers ::getFqsen
     */
    public function testGettingFqsen(): void
    {
        $fixture = new EnumCase(
            $this->fqsen,
            $this->docBlock
        );

        $this->assertSame($this->fqsen, $fixture->getFqsen());
    }

    /**
     * @covers ::getDocBlock
     */
    public function testGettingDocBlock(): void
    {
        $fixture = new EnumCase(
            $this->fqsen,
            $this->docBlock
        );

        $this->assertSame($this->docBlock, $fixture->getDocBlock());
    }

    /**
     * @covers ::getValue
     */
    public function testValueCanBeOmitted(): void
    {
        $fixture = new EnumCase(
            $this->fqsen,
            $this->docBlock
        );

        $this->assertNull($fixture->getValue());
    }

    /**
     * @uses Expression
     *
     * @covers ::getValue
     */
    public function testValueCanBeProvidedAsAnExpression(): void
    {
        $expression = new Expression('Enum case expression');
        $fixture = new EnumCase(
            $this->fqsen,
            $this->docBlock,
            null,
            null,
            $expression
        );

        $this->assertSame($expression, $fixture->getValue(false));
    }

    /**
     * @uses Expression
     *
     * @covers ::getValue
     */
    public function testValueCanBeReturnedAsString(): void
    {
        $expression = new Expression('Enum case expression');
        $fixture = new EnumCase(
            $this->fqsen,
            $this->docBlock,
            null,
            null,
            $expression
        );

        $this->assertSame('Enum case expression', $fixture->getValue(true));
    }

    /**
     * @covers ::getLocation
     */
    public function testGetLocationReturnsProvidedValue(): void
    {
        $location = new Location(15, 10);
        $fixture = new EnumCase(
            $this->fqsen,
            $this->docBlock,
            $location
        );

        self::assertSame($location, $fixture->getLocation());
    }

    /**
     * @uses Location
     *
     * @covers ::getLocation
     */
    public function testGetLocationReturnsUnknownByDefault(): void
    {
        $fixture = new EnumCase(
            $this->fqsen,
            $this->docBlock
        );

        self::assertEquals(new Location(-1), $fixture->getLocation());
    }

    /**
     * @covers ::getEndLocation
     */
    public function testGetEndLocationReturnsProvidedValue(): void
    {
        $location = new Location(11, 23);
        $fixture = new EnumCase(
            $this->fqsen,
            $this->docBlock,
            null,
            $location
        );

        self::assertSame($location, $fixture->getEndLocation());
    }

    /**
     * @covers ::getEndLocation
     */
    public function testGetEndLocationReturnsUnknownByDefault(): void
    {
        $fixture = new EnumCase(
            $this->fqsen,
            $this->docBlock
        );

        self::assertEquals(new Location(-1), $fixture->getEndLocation());
    }
}
