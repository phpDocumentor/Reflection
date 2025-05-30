<?php

declare(strict_types=1);

namespace phpDocumentor\Reflection;

use EliasHaeussler\PHPUnitAttributes\Attribute\RequiresPackage;
use phpDocumentor\Reflection\File\LocalFile;
use phpDocumentor\Reflection\Php\ProjectFactory;
use PHPUnit\Framework\TestCase;

/** @coversNothing */
#[RequiresPackage('nikic/php-parser', '>= 5.2')]
final class AsymmetricAccessorTest extends TestCase
{
    public function testAsymmetricAccessor(): void
    {
        $file = __DIR__ . '/data/PHP84/AsymmetricAccessor.php';
        $projectFactory = ProjectFactory::createInstance();
        $project = $projectFactory->create('My project', [new LocalFile($file)]);

        $class = $project->getFiles()[$file]->getClasses()['\AsymmetricAccessor'];

        self::assertEquals(
            'public',
            $class->getProperties()['\AsymmetricAccessor::$pizza']->getVisibility()->getReadVisibility(),
        );
        self::assertEquals(
            'private',
            $class->getProperties()['\AsymmetricAccessor::$pizza']->getVisibility()->getWriteVisibility(),
        );
    }

    public function testAsyncPropertyPromotion(): void
    {
        $file = __DIR__ . '/data/PHP84/AsymmetricPropertyPromotion.php';
        $projectFactory = ProjectFactory::createInstance();
        $project = $projectFactory->create('My project', [new LocalFile($file)]);


        $class = $project->getFiles()[$file]->getClasses()['\AsymmetricPropertyPromotion'];

        self::assertEquals(
            'public',
            $class->getProperties()['\AsymmetricPropertyPromotion::$pizza']->getVisibility()->getReadVisibility(),
        );
        self::assertEquals(
            'protected',
            $class->getProperties()['\AsymmetricPropertyPromotion::$pizza']->getVisibility()->getWriteVisibility(),
        );
    }
}
