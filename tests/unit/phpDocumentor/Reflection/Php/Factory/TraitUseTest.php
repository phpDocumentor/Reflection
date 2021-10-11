<?php

declare(strict_types=1);

namespace phpDocumentor\Reflection\Php\Factory;

use InvalidArgumentException;
use phpDocumentor\Reflection\Element;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Php\Class_ as Class_Element;
use phpDocumentor\Reflection\Php\Interface_;
use phpDocumentor\Reflection\Php\ProjectFactoryStrategies;
use phpDocumentor\Reflection\Php\Trait_ as Trait_Element;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Stmt\TraitUse as TraitUseNode;

/**
 * @coversDefaultClass \phpDocumentor\Reflection\Php\Factory\TraitUse
 * @covers \phpDocumentor\Reflection\Php\Factory\AbstractFactory
 */
final class TraitUseTest extends TestCase
{
    /** @return mixed[][] */
    public function consumerProvider(): array
    {
        return [
            [new Class_Element(new Fqsen('\MyClass'))],
            [new Trait_Element(new Fqsen('\MyTrait'))],
        ];
    }

    protected function setUp(): void
    {
        $this->fixture = new TraitUse();
    }

    /**
     * @covers ::matches
     */
    public function testMatchesOnlyTraitUseNode(): void
    {
        self::assertTrue(
            $this->fixture->matches(
                self::createContext(),
                $this->givenTraitUse()
            )
        );
    }

    /** @covers ::create */
    public function testCreateThrowsExceptionWhenStackDoesNotContainClass(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $context = self::createContext()->push(new Interface_(new Fqsen('\Interface')));
        $this->fixture->create($context, $this->givenTraitUse(), new ProjectFactoryStrategies([]));
    }

    /**
     * @param Class_Element|Trait_Element $traitConsumer
     *
     * @covers ::create
     * @dataProvider consumerProvider
     */
    public function testCreateWillAddUsedTraitToContextTop(Element $traitConsumer): void
    {
        $context = self::createContext()->push($traitConsumer);
        $this->fixture->create($context, $this->givenTraitUse(), new ProjectFactoryStrategies([]));

        self::assertEquals(['\Foo' => new Fqsen('\Foo')], $traitConsumer->getUsedTraits());
    }

    private function givenTraitUse(): TraitUseNode
    {
        return new TraitUseNode([new FullyQualified('Foo')]);
    }
}
