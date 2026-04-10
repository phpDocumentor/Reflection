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
use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Location;
use phpDocumentor\Reflection\Metadata\MetaDataContainer as MetaDataContainerInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;

/** @property Interface_ $fixture */
#[CoversClass(Interface_::class)]
#[UsesClass('\phpDocumentor\Reflection\DocBlock')]
#[UsesClass('\phpDocumentor\Reflection\Fqsen')]
#[UsesClass('\phpDocumentor\Reflection\Php\Method')]
#[UsesClass('\phpDocumentor\Reflection\Php\Constant')]
#[UsesClass('\phpDocumentor\Reflection\Php\Visibility')]
#[UsesClass('\phpDocumentor\Reflection\Php\Property')]
final class Interface_Test extends TestCase
{
    use MetadataContainerTestHelper;

    private Fqsen $fqsen;

    private DocBlock $docBlock;

    /** @var Fqsen[] */
    private array $exampleParents;

    /**
     * Creates a new (empty) fixture object.
     */
    protected function setUp(): void
    {
        $this->exampleParents = [
            new Fqsen('\MySpace\MyParent'),
            new Fqsen('\MySpace\MyOtherParent'),
        ];

        $this->fqsen    = new Fqsen('\MySpace\MyInterface');
        $this->docBlock = new DocBlock('');
        $this->fixture  = new Interface_($this->fqsen, $this->exampleParents, $this->docBlock);
    }

    private function getFixture(): MetaDataContainerInterface
    {
        return $this->fixture;
    }

    public function testGetName(): void
    {
        $this->assertSame($this->fqsen->getName(), $this->fixture->getName());
    }

    public function testGetFqsen(): void
    {
        $this->assertSame($this->fqsen, $this->fixture->getFqsen());
    }

    public function testGetDocblock(): void
    {
        $this->assertSame($this->docBlock, $this->fixture->getDocBlock());
    }

    public function testSettingAndGettingConstants(): void
    {
        $this->assertEquals([], $this->fixture->getConstants());

        $constant = new Constant(new Fqsen('\MySpace\MyInterface::MY_CONSTANT'));

        $this->fixture->addConstant($constant);

        $this->assertEquals(['\MySpace\MyInterface::MY_CONSTANT' => $constant], $this->fixture->getConstants());
    }

    public function testSettingAndGettingMethods(): void
    {
        $this->assertEquals([], $this->fixture->getMethods());

        $method = new Method(new Fqsen('\MySpace\MyInterface::myMethod()'));

        $this->fixture->addMethod($method);

        $this->assertEquals(['\MySpace\MyInterface::myMethod()' => $method], $this->fixture->getMethods());
    }

    public function testSettingAndGettingProperties(): void
    {
        $this->assertEquals([], $this->fixture->getProperties());

        $property = new Property(new Fqsen('\MySpace\MyInterface::$myProperty'));

        $this->fixture->addProperty($property);

        $this->assertEquals(['\MySpace\MyInterface::$myProperty' => $property], $this->fixture->getProperties());
    }

    public function testReturningTheParentsOfThisInterface(): void
    {
        $this->assertSame($this->exampleParents, $this->fixture->getParents());
    }

    public function testLineAndColumnNumberIsReturnedWhenALocationIsProvided(): void
    {
        $fixture = new Interface_($this->fqsen, [], $this->docBlock, new Location(100, 20), new Location(101, 20));
        $this->assertLineAndColumnNumberIsReturnedWhenALocationIsProvided($fixture);
    }

    public function testArrayWithParentsMustBeFqsenObjects(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Interface_(new Fqsen('\MyInterface'), ['InvalidInterface']);
    }
}
