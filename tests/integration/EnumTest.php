<?php

declare(strict_types=1);

namespace integration;

use phpDocumentor\Reflection\File\LocalFile;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Php\Enum_;
use phpDocumentor\Reflection\Php\Project;
use phpDocumentor\Reflection\Php\ProjectFactory;
use phpDocumentor\Reflection\Types\Object_;
use PHPUnit\Framework\TestCase;
use phpDocumentor\Reflection\Types\String_;

/**
 * @coversNothing
 */
final class EnumTest extends TestCase
{
    const FILE = __DIR__ . '/data/Enums/base.php';
    const BACKED_ENUM = __DIR__ . '/data/Enums/backedEnum.php';
    const ENUM_WITH_CONSTANT = __DIR__ . '/data/Enums/enumWithConstant.php';
    const ENUM_CONSUMER = __DIR__ . '/data/Enums/EnumConsumer.php';

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
                new LocalFile(self::ENUM_WITH_CONSTANT),
                new LocalFile(self::ENUM_CONSUMER),
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

    public function testEnumWithConstant(): void
    {
        $file = $this->project->getFiles()[self::ENUM_WITH_CONSTANT];

        $enum = $file->getEnums()['\MyNamespace\MyEnumWithConstant'];
        self::assertInstanceOf(Enum_::class, $enum);
        self::assertCount(1, $enum->getConstants());
        self::assertArrayHasKey('\MyNamespace\MyEnumWithConstant::MYCONST', $enum->getConstants());
        self::assertSame("'MyConstValue'", $enum->getConstants()['\MyNamespace\MyEnumWithConstant::MYCONST']->getValue());
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

    public function testEnumSupportInProperty(): void
    {
        $file = $this->project->getFiles()[self::ENUM_CONSUMER];

        $class = $file->getClasses()['\MyNamespace\EnumConsumer'];

        self::assertEquals(
            '\MyNamespace\MyEnum::VALUE1',
            $class->getProperties()['\MyNamespace\EnumConsumer::$myEnum']->getDefault()
        );

        self::assertEquals(
            new Object_(new Fqsen('\MyNamespace\MyEnum')),
            $class->getProperties()['\MyNamespace\EnumConsumer::$myEnum']->getType()
        );
    }

    public function testEnumSupportInMethod(): void
    {
        $file = $this->project->getFiles()[self::ENUM_CONSUMER];

        $class = $file->getClasses()['\MyNamespace\EnumConsumer'];
        $method = $class->getMethods()['\MyNamespace\EnumConsumer::consume()'];

        self::assertEquals(
            new Object_(new Fqsen('\MyNamespace\MyEnum')),
            $method->getReturnType()
        );

        self::assertEquals(
            new Object_(new Fqsen('\MyNamespace\MyEnum')),
            $method->getArguments()[0]->getType()
        );

        //This should be fixed in #219
//        self::assertEquals(
//            '\MyNamespace\MyEnum::VALUE1',
//            $method->getArguments()[0]->getDefault()
//        );
    }
}
