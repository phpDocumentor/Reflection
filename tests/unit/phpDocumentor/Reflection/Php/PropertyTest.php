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
use phpDocumentor\Reflection\Types\Integer;

/**
 * Tests the functionality for the Property class.
 *
 * @coversDefaultClass \phpDocumentor\Reflection\Php\Property
 * @covers ::__construct
 * @covers ::<private>
 * @property Property $fixture
 */
final class PropertyTest extends TestCase
{
    use MetadataContainerTest;

    private Fqsen $fqsen;

    private Visibility $visibility;

    private DocBlock $docBlock;

    protected function setUp(): void
    {
        $this->fqsen = new Fqsen('\My\Class::$property');
        $this->visibility = new Visibility('private');
        $this->docBlock = new DocBlock('');
        $this->fixture = new Property($this->fqsen);
    }

    private function getFixture(): MetaDataContainerInterface
    {
        return $this->fixture;
    }

    /**
     * @uses \phpDocumentor\Reflection\Php\Visibility
     *
     * @covers ::getFqsen
     * @covers ::getName
     */
    public function testGetFqsenAndGetName(): void
    {
        $property = new Property($this->fqsen);

        self::assertSame($this->fqsen, $property->getFqsen());
        self::assertEquals($this->fqsen->getName(), $property->getName());
    }

    /**
     * @uses \phpDocumentor\Reflection\Php\Visibility
     *
     * @covers ::isStatic
     * @covers ::__construct
     */
    public function testGettingWhetherPropertyIsStatic(): void
    {
        $property = new Property($this->fqsen, $this->visibility, $this->docBlock, null, false);
        self::assertFalse($property->isStatic());

        $property = new Property($this->fqsen, $this->visibility, $this->docBlock, null, true);
        self::assertTrue($property->isStatic());
    }

    /**
     * @uses \phpDocumentor\Reflection\Php\Visibility
     *
     * @covers ::isReadOnly
     * @covers ::__construct
     */
    public function testGettingWhetherPropertyIsReadOnly(): void
    {
        $property = new Property($this->fqsen, $this->visibility, $this->docBlock, null);
        self::assertFalse($property->isReadOnly());

        $property = new Property(
            $this->fqsen,
            $this->visibility,
            $this->docBlock,
            null,
            true,
            null,
            null,
            null,
            true
        );

        self::assertTrue($property->isReadOnly());
    }

    /**
     * @uses \phpDocumentor\Reflection\Php\Visibility
     *
     * @covers ::getVisibility
     * @covers ::__construct
     */
    public function testGettingVisibility(): void
    {
        $property = new Property($this->fqsen, $this->visibility, $this->docBlock, null, true);

        self::assertSame($this->visibility, $property->getVisibility());
    }

    /**
     * @uses \phpDocumentor\Reflection\Php\Visibility
     *
     * @covers ::getTypes
     * @covers ::addType
     */
    public function testSetAndGetTypes(): void
    {
        $property = new Property($this->fqsen, $this->visibility, $this->docBlock, null, true);
        self::assertEquals([], $property->getTypes());

        $property->addType('a');
        self::assertEquals(['a'], $property->getTypes());
    }

    /**
     * @uses \phpDocumentor\Reflection\Php\Visibility
     *
     * @covers ::getDefault
     */
    public function testGetDefault(): void
    {
        $property = new Property($this->fqsen, $this->visibility, $this->docBlock, null, false);
        self::assertNull($property->getDefault());

        $expression = new Expression('a');
        $property = new Property($this->fqsen, $this->visibility, $this->docBlock, $expression, true);
        self::assertSame('a', $property->getDefault());
        self::assertSame($expression, $property->getDefault(false));
    }

    /**
     * @uses \phpDocumentor\Reflection\Php\Visibility
     *
     * @covers ::getDocBlock
     */
    public function testGetDocBlock(): void
    {
        $property = new Property($this->fqsen, $this->visibility, $this->docBlock, null, false);
        self::assertSame($this->docBlock, $property->getDocBlock());
    }

    public function testLineAndColumnNumberIsReturnedWhenALocationIsProvided(): void
    {
        $fixture = new Property($this->fqsen, null, null, null, false, new Location(100, 20), new Location(101, 20));
        $this->assertLineAndColumnNumberIsReturnedWhenALocationIsProvided($fixture);
    }

    /**
     * @uses \phpDocumentor\Reflection\Php\Visibility
     * @uses \phpDocumentor\Reflection\Types\Integer
     *
     * @covers ::getType
     */
    public function testGetType(): void
    {
        $type = new Integer();
        $fixture = new Property(
            $this->fqsen,
            null,
            null,
            null,
            false,
            null,
            null,
            $type
        );

        self::assertSame($type, $fixture->getType());

        $fixture = new Property($this->fqsen);
        self::assertNull($fixture->getType());
    }
}
