<?php

declare(strict_types=1);

namespace phpDocumentor\Reflection;

use EliasHaeussler\PHPUnitAttributes\Attribute\RequiresPackage;
use phpDocumentor\Reflection\File\LocalFile;
use phpDocumentor\Reflection\Php\ProjectFactory;
use PHPUnit\Framework\TestCase;

/** @coversNothing */
#[RequiresPackage('nikic/php-parser', '>= 5.2')]
final class AsyncAccessorTest extends TestCase
{
    public function testAsyncAccessor(): void
    {
        $file = __DIR__ . '/data/PHP84/AsyncAccessor.php';
        $projectFactory = ProjectFactory::createInstance();
        $project = $projectFactory->create('My project', [new LocalFile($file)]);

        $class = $project->getFiles()[$file]->getClasses()['\AsyncAccessor'];

        self::assertEquals(
            'public',
            $class->getProperties()['\AsyncAccessor::$pizza']->getVisibility()->getReadVisibility(),
        );
        self::assertEquals(
            'private',
            $class->getProperties()['\AsyncAccessor::$pizza']->getVisibility()->getWriteVisibility(),
        );
    }

    public function testAsyncPropertyPromotion(): void
    {
        $file = __DIR__ . '/data/PHP84/AsyncPropertyPromotion.php';
        $projectFactory = ProjectFactory::createInstance();
        $project = $projectFactory->create('My project', [new LocalFile($file)]);

        $class = $project->getFiles()[$file]->getClasses()['\AsyncPropertyPromotion'];

        self::assertEquals(
            'public',
            $class->getProperties()['\AsyncPropertyPromotion::$pizza']->getVisibility()->getReadVisibility(),
        );
        self::assertEquals(
            'protected',
            $class->getProperties()['\AsyncPropertyPromotion::$pizza']->getVisibility()->getWriteVisibility(),
        );
    }
}
