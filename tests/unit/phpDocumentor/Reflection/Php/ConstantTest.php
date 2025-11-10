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
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;

/** @property Constant $fixture */
#[CoversClass(Constant::class)]
#[UsesClass('\phpDocumentor\Reflection\DocBlock')]
#[UsesClass('\phpDocumentor\Reflection\Php\Visibility')]
#[UsesClass('\phpDocumentor\Reflection\Fqsen')]
final class ConstantTest extends TestCase
{
    use MetadataContainerTestHelper;

    private Fqsen $fqsen;

    private DocBlock $docBlock;

    private string $value = 'Value';

    /**
     * Creates a new (empty) fixture object.
     */
    protected function setUp(): void
    {
        $this->fqsen = new Fqsen('\MySpace\CONSTANT');
        $this->docBlock = new DocBlock('');
        $this->fixture = new Constant($this->fqsen, $this->docBlock, new Expression($this->value));
    }

    private function getFixture(): MetaDataContainerInterface
    {
        return $this->fixture;
    }

    public function testGetValue(): void
    {
        self::assertEquals(new Expression($this->value), $this->fixture->getValue());
    }

    public function testIsFinal(): void
    {
        self::assertFalse($this->fixture->isFinal());
    }

    public function testGetFqsen(): void
    {
        self::assertSame($this->fqsen, $this->fixture->getFqsen());
        self::assertSame($this->fqsen->getName(), $this->fixture->getName());
    }

    public function testGetDocblock(): void
    {
        self::assertSame($this->docBlock, $this->fixture->getDocBlock());
    }

    public function testGetVisibility(): void
    {
        self::assertEquals(new Visibility(Visibility::PUBLIC_), $this->fixture->getVisibility());
    }

    public function testLineAndColumnNumberIsReturnedWhenALocationIsProvided(): void
    {
        $fixture = new Constant($this->fqsen, $this->docBlock, null, new Location(100, 20), new Location(101, 20));
        $this->assertLineAndColumnNumberIsReturnedWhenALocationIsProvided($fixture);
    }
}
