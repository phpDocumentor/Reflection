<?php

declare(strict_types=1);

namespace integration;

use EliasHaeussler\PHPUnitAttributes\Attribute\RequiresPackage;
use phpDocumentor\Reflection\File\LocalFile;
use phpDocumentor\Reflection\Php\ProjectFactory;
use PHPUnit\Framework\TestCase;

#[RequiresPackage('nikic/php-parser', '>= 5.2')]
final class PropertyHookTest extends TestCase
{
    public function testPropertyHook()
    {
        $file = __DIR__ . '/data/PHP84/PropertyHook.php';
        $projectFactory = ProjectFactory::createInstance();
        $project = $projectFactory->create('My project', [new LocalFile($file)]);

        $class = $project->getFiles()[$file]->getClasses()['\PropertyHook'];
    }
}
