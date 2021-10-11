<?php

declare(strict_types=1);

namespace phpDocumentor\Reflection;

use phpDocumentor\Reflection\File\LocalFile;
use phpDocumentor\Reflection\Metadata\HookStrategy;
use phpDocumentor\Reflection\Php\Project;
use phpDocumentor\Reflection\Php\ProjectFactory;
use PHPUnit\Framework\TestCase;

/**
 * @coversNothing
 */
final class MetadataTest extends TestCase
{
    const FILE = __DIR__ . '/Reflection/Metadata/example.php';

    public function testCustomMetadata(): void
    {
        $projectFactory = ProjectFactory::createInstance();
        $projectFactory->addStrategy(new HookStrategy());

        /** @var Project $project */
        $project = $projectFactory->create('My project', [new LocalFile(self::FILE)]);
        $class = $project->getFiles()[self::FILE]->getClasses()['\myHookUsingClass'];

        self::assertArrayHasKey('project-metadata', $class->getMethods()['\myHookUsingClass::test()']->getMetadata());
    }
}
