<?php

declare(strict_types=1);

namespace integration;

use phpDocumentor\Reflection\File\LocalFile;
use phpDocumentor\Reflection\Php\Enum_;
use phpDocumentor\Reflection\Php\Project;
use phpDocumentor\Reflection\Php\ProjectFactory;
use PHPUnit\Framework\TestCase;

final class EnumTest extends TestCase
{
    const FILE = __DIR__ . '/data/Enums/base.php';
    /** @var ProjectFactory */
    private $fixture;

    /** @var Project */
    private $project;

    protected function setUp() : void
    {
        $this->fixture = ProjectFactory::createInstance();
        $this->project = $this->fixture->create(
            'Enums',
            [
                new LocalFile(self::FILE),
            ]
        );
    }

    public function testFileHasEnum(): void
    {
        $file = $this->project->getFiles()[self::FILE];

        $enum = $file->getEnums()['\MyNamespace\MyEnum'];
        self::assertInstanceOf(Enum_::class, $enum);
        self::assertCount(2, $enum->getCases());
        self::assertArrayHasKey('\MyNamespace\MyEnum::VALUE1', $enum->getCases());
        self::assertArrayHasKey('\MyNamespace\MyEnum::VALUE2', $enum->getCases());
    }
}
