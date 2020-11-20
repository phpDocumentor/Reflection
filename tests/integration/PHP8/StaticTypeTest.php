<?php

declare(strict_types=1);

namespace integration\PHP8;

use phpDocumentor\Reflection\File\LocalFile;
use phpDocumentor\Reflection\Php\Project;
use phpDocumentor\Reflection\Php\ProjectFactory;
use phpDocumentor\Reflection\Types\Mixed_;
use phpDocumentor\Reflection\Types\Static_;
use PHPUnit\Framework\TestCase;

/**
 * @coversNothing
 */
final class StaticTypeTest extends TestCase
{
    const FILE = __DIR__ . '/../data/PHP8/StaticType.php';
    /** @var ProjectFactory */
    private $fixture;

    public function testSupportStaticType() : void
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

        $class = $file->getClasses()['\PHP8\StaticType'];

        self::assertEquals(new Static_(), $class->getMethods()['\PHP8\StaticType::getProperty()']->getReturnType());
        self::assertEquals(new Mixed_(), $class->getMethods()['\PHP8\StaticType::setProperty()']->getArguments()[0]->getType());
        self::assertEquals(null, $class->getProperties()['\PHP8\StaticType::$property']->getType());
        self::assertTrue($class->getProperties()['\PHP8\StaticType::$property']->isStatic());
    }
}
