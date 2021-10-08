<?php

declare(strict_types=1);

namespace integration;

use phpDocumentor\Reflection\File\LocalFile;
use phpDocumentor\Reflection\Php\Enum_;
use phpDocumentor\Reflection\Php\Project;
use phpDocumentor\Reflection\Php\ProjectFactory;
use PHPUnit\Framework\TestCase;
use phpDocumentor\Reflection\Types\String_;

final class EnumTest extends TestCase
{
    const FILE = __DIR__ . '/data/Enums/base.php';
    const BACKED_ENUM = __DIR__ . '/data/Enums/backedEnum.php';
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
                new LocalFile(self::BACKED_ENUM),
            ]
        );
    }

    public function testFileHasEnum(): void
    {
        $file = $this->project->getFiles()[self::FILE];

        $enum = $file->getEnums()['\MyNamespace\MyEnum'];
        self::assertInstanceOf(Enum_::class, $enum);
        self::assertCount(2, $enum->getCases());
        self::assertNull($enum->getBackedType());
        self::assertArrayHasKey('\MyNamespace\MyEnum::VALUE1', $enum->getCases());
        self::assertArrayHasKey('\MyNamespace\MyEnum::VALUE2', $enum->getCases());
    }

    public function testBackedEnum(): void
    {
        $file = $this->project->getFiles()[self::BACKED_ENUM];

        $enum = $file->getEnums()['\MyNamespace\MyBackedEnum'];
        self::assertInstanceOf(Enum_::class, $enum);
        self::assertCount(2, $enum->getCases());
        self::assertEquals(new String_(), $enum->getBackedType());
        self::assertArrayHasKey('\MyNamespace\MyBackedEnum::VALUE1', $enum->getCases());
        self::assertArrayHasKey('\MyNamespace\MyBackedEnum::VALUE2', $enum->getCases());

        self::assertSame("'this is value1'", $enum->getCases()['\MyNamespace\MyBackedEnum::VALUE1']->getValue());
        self::assertSame("'this is value2'", $enum->getCases()['\MyNamespace\MyBackedEnum::VALUE2']->getValue());
    }
}
