<?php

declare(strict_types=1);

namespace integration\PHP8;

use phpDocumentor\Reflection\File\LocalFile;
use phpDocumentor\Reflection\Php\Project;
use phpDocumentor\Reflection\Php\ProjectFactory;
use phpDocumentor\Reflection\Types\Mixed_;
use PHPUnit\Framework\TestCase;

/**
 * @coversNothing
 */
final class MixedTypeTest extends TestCase
{
    const FILE = __DIR__ . '/../data/PHP8/MixedType.php';
    /** @var ProjectFactory */
    private $fixture;

    public function testSupportMixedType() : void
    {
        $this->fixture = ProjectFactory::createInstance();

        /** @var Project $project */
        $project = $this->fixture->create(
            'PHP8',
            [
                new LocalFile(self::FILE),
            ]
        );

        $file = $project->getFiles()[self::FILE];

        $class = $file->getClasses()['\PHP8\MixedType'];

        self::assertEquals(new Mixed_(), $class->getMethods()['\PHP8\MixedType::getProperty()']->getReturnType());
        self::assertEquals(new Mixed_(), $class->getMethods()['\PHP8\MixedType::setProperty()']->getArguments()[0]->getType());
        self::assertEquals(new Mixed_(), $class->getProperties()['\PHP8\MixedType::$property']->getType());
    }
}
