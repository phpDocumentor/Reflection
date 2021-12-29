<?php

declare(strict_types=1);

namespace integration\PHP8;

use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlock\Tags\Param;
use phpDocumentor\Reflection\DocBlock\Tags\Var_;
use phpDocumentor\Reflection\File\LocalFile;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Location;
use phpDocumentor\Reflection\Php\Argument;
use phpDocumentor\Reflection\Php\Method;
use phpDocumentor\Reflection\Php\ProjectFactory;
use phpDocumentor\Reflection\Php\Project;
use phpDocumentor\Reflection\Php\Property;
use phpDocumentor\Reflection\Php\Visibility;
use phpDocumentor\Reflection\Types\Context;
use phpDocumentor\Reflection\Types\Object_;
use phpDocumentor\Reflection\Types\String_;
use PHPUnit\Framework\TestCase;

/**
 * @coversNothing
 */
class ConstructorPromotionTest extends TestCase
{
    const FILE = __DIR__ . '/../data/PHP8/ConstructorPromotion.php';
    /** @var ProjectFactory */
    private $fixture;

    /** @var Project */
    private $project;

    protected function setUp() : void
    {
        $this->fixture = ProjectFactory::createInstance();
        $this->project = $this->fixture->create(
            'PHP8',
            [
                new LocalFile(self::FILE),
            ]
        );
    }

    public function testPropertiesAreCreated() : void
    {
        $file = $this->project->getFiles()[self::FILE];
        $class = $file->getClasses()['\\PHP8\\ConstructorPromotion'];

        $constructor = $this->expectedContructorMethod();
        $constructor->addArgument(new Argument('name', new String_()));
        $constructor->addArgument(new Argument('email', new String_(), '\'test@example.com\''));
        $constructor->addArgument(new Argument('birth_date', new Object_(new Fqsen('\\' . \DateTimeImmutable::class))));

        self::assertEquals($constructor, $class->getMethods()['\PHP8\ConstructorPromotion::__construct()']);
        self::assertEquals(
            [
                '\PHP8\ConstructorPromotion::$name' => $this->expectedNameProperty(),
                '\PHP8\ConstructorPromotion::$email' => $this->expectedEmailProperty(),
                '\PHP8\ConstructorPromotion::$birth_date' => $this->expectedBirthDateProperty()
            ],
            $class->getProperties()
        );
    }

    private function expectedContructorMethod(): Method
    {
        $constructor = new Method(
            new Fqsen('\PHP8\ConstructorPromotion::__construct()'),
            new Visibility(Visibility::PUBLIC_),
            new DocBlock(
                'Constructor with promoted properties',
                null,
                [
                    new Param(
                        'name',
                        new String_(),
                        false,
                        new DocBlock\Description('my docblock name')
                    )
                ],
                new Context('PHP8', ['DateTimeImmutable' => 'DateTimeImmutable'])
            ),
            false,
            false,
            false,
            new Location(16),
            new Location(27)
        );
        return $constructor;
    }

    private function expectedNameProperty(): Property
    {
        $name = new Property(
            new Fqsen('\PHP8\ConstructorPromotion::$name'),
            new Visibility(Visibility::PUBLIC_),
            new DocBlock(
                'Summary',
                new DocBlock\Description('Description'),
                [
                    new Var_('name', new String_(), new DocBlock\Description('property description'))
                ],
                new Context('PHP8', ['DateTimeImmutable' => 'DateTimeImmutable'])
            ),
            null,
            false,
            new Location(24),
            new Location(24),
            new String_()
        );
        return $name;
    }

    private function expectedEmailProperty(): Property
    {
        $email = new Property(
            new Fqsen('\PHP8\ConstructorPromotion::$email'),
            new Visibility(Visibility::PROTECTED_),
            null,
            '\'test@example.com\'',
            false,
            new Location(25),
            new Location(25),
            new String_()
        );
        return $email;
    }

    private function expectedBirthDateProperty(): Property
    {
        $birthDate = new Property(
            new Fqsen('\PHP8\ConstructorPromotion::$birth_date'),
            new Visibility(Visibility::PRIVATE_),
            null,
            null,
            false,
            new Location(26),
            new Location(26),
            new Object_(new Fqsen('\\' . \DateTimeImmutable::class))
        );
        return $birthDate;
    }
}
