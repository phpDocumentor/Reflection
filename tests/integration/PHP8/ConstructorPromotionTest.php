<?php

declare(strict_types=1);

namespace integration\PHP8;

use DateTimeImmutable;
use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlock\Tags\Param;
use phpDocumentor\Reflection\DocBlock\Tags\Var_;
use phpDocumentor\Reflection\File\LocalFile;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Location;
use phpDocumentor\Reflection\Php\Argument;
use phpDocumentor\Reflection\Php\Expression;
use phpDocumentor\Reflection\Php\Method;
use phpDocumentor\Reflection\Php\ProjectFactory;
use phpDocumentor\Reflection\Php\Project;
use phpDocumentor\Reflection\Php\Property;
use phpDocumentor\Reflection\Php\Visibility;
use phpDocumentor\Reflection\Types\Array_;
use phpDocumentor\Reflection\Types\Context;
use phpDocumentor\Reflection\Types\Object_;
use phpDocumentor\Reflection\Types\String_;
use PHPUnit\Framework\TestCase;

/**
 * @coversNothing
 */
class ConstructorPromotionTest extends TestCase
{
    private const FILE = __DIR__ . '/../data/PHP8/ConstructorPromotion.php';

    private ProjectFactory $fixture;
    private Project $project;

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

    public function testArgumentsAreReadCorrectly() : void
    {
        $file = $this->project->getFiles()[self::FILE];
        $class = $file->getClasses()['\\PHP8\\ConstructorPromotion'];

        $constructor = $this->expectedConstructorMethod();
        $constructor->addArgument(new Argument('name', new String_()));
        $constructor->addArgument(new Argument('email', new String_(), '\'test@example.com\''));
        $constructor->addArgument(new Argument('birth_date', new Object_(new Fqsen('\\' . DateTimeImmutable::class))));
        $constructor->addArgument(
            new Argument(
                'created_at',
                new Object_(new Fqsen('\\' . DateTimeImmutable::class)),
                new Expression(
                    'new {{ PHPDOC6ffacd918e2f70478d2fd33dcb58c4d4 }}(\'now\')',
                    [
                        '{{ PHPDOC6ffacd918e2f70478d2fd33dcb58c4d4 }}' => new Fqsen('\\DateTimeImmutable')
                    ]
                )
            )
        );
        $constructor->addArgument(
            new Argument(
                'uses_constants',
                new Array_(),
                new Expression(
                    '[{{ PHPDOC590f53e8699817c6fa498cc11a4cbe63 }}]',
                    [
                        '{{ PHPDOC590f53e8699817c6fa498cc11a4cbe63 }}' => new Fqsen('\PHP8\ConstructorPromotion::DEFAULT_VALUE')
                    ]
                )
            )
        );

        self::assertEquals($constructor, $class->getMethods()['\PHP8\ConstructorPromotion::__construct()']);
    }

    public function testPropertiesAreCreated() : void
    {
        $file = $this->project->getFiles()[self::FILE];
        $class = $file->getClasses()['\\PHP8\\ConstructorPromotion'];

        self::assertEquals(
            [
                '\PHP8\ConstructorPromotion::$name' => $this->expectedNameProperty(),
                '\PHP8\ConstructorPromotion::$email' => $this->expectedEmailProperty(),
                '\PHP8\ConstructorPromotion::$birth_date' => $this->expectedBirthDateProperty(),
                '\PHP8\ConstructorPromotion::$created_at' => $this->expectedCreatedAtProperty(),
            ],
            $class->getProperties()
        );
    }

    private function expectedConstructorMethod(): Method
    {
        return new Method(
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
            new Location(18, 264),
            new Location(31, 709)
        );
    }

    private function expectedNameProperty(): Property
    {
        return new Property(
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
            new Location(26),
            new Location(26),
            new String_()
        );
    }

    private function expectedEmailProperty(): Property
    {
        return new Property(
            new Fqsen('\PHP8\ConstructorPromotion::$email'),
            new Visibility(Visibility::PROTECTED_),
            null,
            '\'test@example.com\'',
            false,
            new Location(27),
            new Location(27),
            new String_()
        );
    }

    private function expectedBirthDateProperty(): Property
    {
        return new Property(
            new Fqsen('\PHP8\ConstructorPromotion::$birth_date'),
            new Visibility(Visibility::PRIVATE_),
            null,
            null,
            false,
            new Location(28),
            new Location(28),
            new Object_(new Fqsen('\\' . DateTimeImmutable::class))
        );
    }

    private function expectedCreatedAtProperty(): Property
    {
        return new Property(
            new Fqsen('\PHP8\ConstructorPromotion::$created_at'),
            new Visibility(Visibility::PRIVATE_),
            null,
            new Expression(
                'new {{ PHPDOC6ffacd918e2f70478d2fd33dcb58c4d4 }}(\'now\')',
                [
                    '{{ PHPDOC6ffacd918e2f70478d2fd33dcb58c4d4 }}' => new Fqsen('\\DateTimeImmutable')
                ]
            ),
            false,
            new Location(29),
            new Location(29),
            new Object_(new Fqsen('\\' . DateTimeImmutable::class))
        );
    }
}
