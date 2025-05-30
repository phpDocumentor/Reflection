<?php

declare(strict_types=1);

namespace phpDocumentor\Reflection\Php\Factory;

use phpDocumentor\Reflection\DocBlockFactoryInterface;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Location;
use phpDocumentor\Reflection\Php\StrategyContainer;
use phpDocumentor\Reflection\Php\Visibility;
use PhpParser\Node\Stmt\Property as PropertyNode;
use PhpParser\Node\Stmt\PropertyProperty;
use PhpParser\PrettyPrinter;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(PropertyBuilder::class)]
class PropertyBuilderTest extends TestCase
{
    public function testBuildsPropertyElementWithCorrectAttributes(): void
    {
        $fqsen = new Fqsen('\MyClass::$property');
        $visibility = new Visibility(Visibility::PUBLIC_);
        $startLocation = new Location(10);
        $endLocation = new Location(20);

        $docBlockFactory = $this->createMock(DocBlockFactoryInterface::class);
        $valueConverter = $this->createMock(PrettyPrinter\Standard::class);
        $strategies = $this->createMock(StrategyContainer::class);
        $reducers = [];

        $prop1 = new PropertyProperty('prop1');
        $propertyNode = new PropertyNode(1, [$prop1]);
        $properties = new PropertyIterator($propertyNode);

        $builder = PropertyBuilder::create($valueConverter, $docBlockFactory, $strategies, $reducers);
        $builder->fqsen($fqsen)
            ->visibility($properties)
            ->docblock($properties->getDocComment())
            ->default($properties->getDefault())
            ->static(true)
            ->startLocation($startLocation)
            ->endLocation($endLocation)
            ->type($properties->getType())
            ->readOnly(true)
            ->hooks($properties->getHooks());

        $context = \phpDocumentor\Reflection\Php\Factory\TestCase::createContext();
        $property = $builder->build($context);

        $this->assertSame($fqsen, $property->getFqsen());
        $this->assertEquals($visibility, $property->getVisibility());
        $this->assertNull($property->getDocBlock());
        $this->assertNull($property->getDefault());
        $this->assertTrue($property->isStatic());
        $this->assertSame($startLocation, $property->getLocation());
        $this->assertSame($endLocation, $property->getEndLocation());
        $this->assertNull($property->getType());
        $this->assertTrue($property->isReadOnly());
    }
}
