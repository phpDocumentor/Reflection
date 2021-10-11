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

    /** @var EnumCase */
    private $fixture;

    /** @var Fqsen */
    private $fqsen;

    /** @var DocBlock */
    private $docBlock;

    /**
     * Creates a new (emoty) fixture object.
     */
    protected function setUp(): void
    {
        $this->fqsen    = new Fqsen('\Enum::VALUE');
        $this->docBlock = new DocBlock('');

        $this->fixture = new EnumCase($this->fqsen, $this->docBlock);
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
        $this->assertSame($this->fqsen->getName(), $this->fixture->getName());
    }

    /**
     * @covers ::getFqsen
     */
    public function testGettingFqsen(): void
    {
        $this->assertSame($this->fqsen, $this->fixture->getFqsen());
    }

    /**
     * @covers ::getDocBlock
     */
    public function testGettingDocBlock(): void
    {
        $this->assertSame($this->docBlock, $this->fixture->getDocBlock());
    }

    /**
     * @covers ::getValue
     */
    public function testGetValue(): void
    {
        $this->assertNull($this->fixture->getValue());
    }

    /**
     * @covers ::getLocation
     */
    public function testGetLocationReturnsDefault(): void
    {
        self::assertEquals(new Location(-1), $this->fixture->getLocation());
    }
}
