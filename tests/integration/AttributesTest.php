<?php

declare(strict_types=1);

namespace integration;

use phpDocumentor\Reflection\File\LocalFile;
use phpDocumentor\Reflection\Php\Class_;
use phpDocumentor\Reflection\Php\ProjectFactory;
use phpDocumentor\Reflection\Php\Project;
use PHPUnit\Framework\TestCase;

final class AttributesTest extends TestCase
{
    const FILE = __DIR__ . '/data/Attributes/AttributeConsumer.php';

    /** @var Project */
    private $project;

    protected function setUp() : void
    {
        $fixture = ProjectFactory::createInstance();
        $this->project = $fixture->create(
            'MyProject',
            [
                new LocalFile(self::FILE),
            ]
        );
    }

    public function testClassHasDocblock(): void
    {
        $file = $this->project->getFiles()[self::FILE];
        /** @var Class_ $class */
        $class = $file->getClasses()['\AttributeConsumer'];

        self::assertNotNull($class->getDocBlock());
        self::assertSame('Class docblock', $class->getDocBlock()->getSummary());
    }
}
