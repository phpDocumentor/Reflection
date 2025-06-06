<?php

declare(strict_types=1);

namespace integration;

use EliasHaeussler\PHPUnitAttributes\Attribute\RequiresPackage;
use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\File\LocalFile;
use phpDocumentor\Reflection\Location;
use phpDocumentor\Reflection\Php\Argument;
use phpDocumentor\Reflection\Php\AsymmetricVisibility;
use phpDocumentor\Reflection\Php\Attribute;
use phpDocumentor\Reflection\Php\ProjectFactory;
use phpDocumentor\Reflection\Php\PropertyHook;
use phpDocumentor\Reflection\Php\Visibility;
use phpDocumentor\Reflection\Types\Compound;
use phpDocumentor\Reflection\Types\Integer;
use phpDocumentor\Reflection\Types\String_;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\TestCase;

#[RequiresPackage('nikic/php-parser', '>= 5.2')]
#[CoversNothing]
final class PropertyHookTest extends TestCase
{
    public function testPropertyHookWithDocblocks(): void
    {
        $file = __DIR__ . '/data/PHP84/PropertyHook.php';
        $projectFactory = ProjectFactory::createInstance();
        $project = $projectFactory->create('My project', [new LocalFile($file)]);

        $class = $project->getFiles()[$file]->getClasses()['\PropertyHook'];
        $hooks = $class->getProperties()['\PropertyHook::$example']->getHooks();

        $this->assertTrue($class->getProperties()['\PropertyHook::$example']->isVirtual());
        $this->assertCount(2, $hooks);
        $this->assertEquals('get', $hooks[0]->getName());
        $this->assertEquals(new Visibility(Visibility::PUBLIC_), $hooks[0]->getVisibility());
        $this->assertCount(1, $hooks[0]->getAttributes());
        $this->assertCount(0, $hooks[0]->getArguments());
        $this->assertSame('Not sure this works, but it gets', $hooks[0]->getDocBlock()->getSummary());

        $this->assertEquals('set', $hooks[1]->getName());
        $this->assertEquals(new Visibility(Visibility::PUBLIC_), $hooks[1]->getVisibility());
        $this->assertCount(1, $hooks[1]->getAttributes());
        $this->assertCount(1, $hooks[1]->getArguments());
        $this->assertEquals(new Argument(
            'value',
            new Compound(
                [
                    new String_(),
                    new Integer()
                ]
            ),
        ), $hooks[1]->getArguments()[0]);
        $this->assertSame('Not sure this works, but it gets', $hooks[0]->getDocBlock()->getSummary());
    }

    public function testPropertyHookAsymmetric(): void
    {
        $file = __DIR__ . '/data/PHP84/PropertyHookAsymmetric.php';
        $projectFactory = ProjectFactory::createInstance();
        $project = $projectFactory->create('My project', [new LocalFile($file)]);

        $class = $project->getFiles()[$file]->getClasses()['\PropertyHook'];
        $hooks = $class->getProperties()['\PropertyHook::$example']->getHooks();


        $this->assertEquals(
            new AsymmetricVisibility(
                new Visibility(Visibility::PUBLIC_),
                new Visibility(Visibility::PRIVATE_)
            ),
            $class->getProperties()['\PropertyHook::$example']->getVisibility()
        );
        $this->assertTrue($class->getProperties()['\PropertyHook::$example']->isVirtual());
        $this->assertCount(2, $hooks);
        $this->assertEquals('get', $hooks[0]->getName());
        $this->assertEquals(new Visibility(Visibility::PUBLIC_), $hooks[0]->getVisibility());
        $this->assertCount(0, $hooks[0]->getArguments());

        $this->assertEquals('set', $hooks[1]->getName());
        $this->assertEquals(new Visibility(Visibility::PRIVATE_), $hooks[1]->getVisibility());
        $this->assertCount(1, $hooks[1]->getArguments());
        $this->assertEquals(new Argument(
            'value',
            new Compound(
                [
                    new String_(),
                    new Integer()
                ]
            ),
        ), $hooks[1]->getArguments()[0]);
    }

    public function testVirtualProperty(): void
    {
        $file = __DIR__ . '/data/PHP84/PropertyHookVirtual.php';
        $projectFactory = ProjectFactory::createInstance();
        $project = $projectFactory->create('My project', [new LocalFile($file)]);

        $class = $project->getFiles()[$file]->getClasses()['\PropertyHookVirtual'];

        // Test get-only virtual property
        $fullNameProperty = $class->getProperties()['\PropertyHookVirtual::$fullName'];
        $this->assertTrue($fullNameProperty->isVirtual(), 'Property with getter that doesn\'t reference itself should be virtual');
        $this->assertCount(1, $fullNameProperty->getHooks());
        $this->assertEquals('get', $fullNameProperty->getHooks()[0]->getName());

        // Test set-only virtual property
        $compositeNameProperty = $class->getProperties()['\PropertyHookVirtual::$compositeName'];
        $this->assertTrue($compositeNameProperty->isVirtual(), 'Property with setter that doesn\'t reference itself should be virtual');
        $this->assertCount(1, $compositeNameProperty->getHooks());
        $this->assertEquals('set', $compositeNameProperty->getHooks()[0]->getName());

        // Test property with both get and set hooks that doesn't reference itself
        $completeFullNameProperty = $class->getProperties()['\PropertyHookVirtual::$completeFullName'];
        $this->assertTrue($completeFullNameProperty->isVirtual(), 'Property with getter and setter that don\'t reference itself should be virtual');
        $this->assertCount(2, $completeFullNameProperty->getHooks());

        $nonVirtualPropertyWithoutHooks = $class->getProperties()['\PropertyHookVirtual::$firstName'];
        $this->assertFalse($nonVirtualPropertyWithoutHooks->isVirtual(), 'Property without hooks should not be virtual');
        $this->assertCount(0, $nonVirtualPropertyWithoutHooks->getHooks());

        // Test non-virtual property that references itself
        $nonVirtualNameProperty = $class->getProperties()['\PropertyHookVirtual::$nonVirtualName'];
        $this->assertFalse($nonVirtualNameProperty->isVirtual(), 'Property with hooks that reference itself should not be virtual');
        $this->assertCount(2, $nonVirtualNameProperty->getHooks());
    }
}
