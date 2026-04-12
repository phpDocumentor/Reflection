<?php

declare(strict_types=1);

namespace integration;

use EliasHaeussler\PHPUnitAttributes\Attribute\RequiresPackage;
use phpDocumentor\Reflection\File\LocalFile;
use phpDocumentor\Reflection\Php\ProjectFactory;
use phpDocumentor\Reflection\Php\Visibility;
use phpDocumentor\Reflection\Types\Integer;
use phpDocumentor\Reflection\Types\String_;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\TestCase;

#[RequiresPackage('nikic/php-parser', '>= 5.2')]
#[CoversNothing]
final class InterfacePropertyTest extends TestCase
{
    public function testInterfacePropertiesAreParsed(): void
    {
        $file = __DIR__ . '/data/PHP84/InterfaceProperties.php';
        $projectFactory = ProjectFactory::createInstance();
        $project = $projectFactory->create('My project', [new LocalFile($file)]);

        $interfaces = $project->getFiles()[$file]->getInterfaces();

        $hasId = $interfaces['\PHP84\HasId'];
        $properties = $hasId->getProperties();
        $this->assertCount(1, $properties);
        $idProperty = $properties['\PHP84\HasId::$id'];
        $this->assertEquals(new Integer(), $idProperty->getType());
        $this->assertEquals(new Visibility(Visibility::PUBLIC_), $idProperty->getVisibility());
        $this->assertCount(1, $idProperty->getHooks());
        $this->assertEquals('get', $idProperty->getHooks()[0]->getName());

        $hasName = $interfaces['\PHP84\HasName'];
        $properties = $hasName->getProperties();
        $this->assertCount(1, $properties);
        $nameProperty = $properties['\PHP84\HasName::$name'];
        $this->assertEquals(new String_(), $nameProperty->getType());
        $this->assertCount(2, $nameProperty->getHooks());
    }
}
