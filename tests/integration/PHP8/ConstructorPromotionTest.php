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
use phpDocumentor\Reflection\Php\Project;
use phpDocumentor\Reflection\Php\ProjectFactory;
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
        $constructor->addArgument(new Argument('name', new String_(), "'default name'"));
        $constructor->addArgument(
            new Argument(
                'email',
                new Object_(new Fqsen('\\PHP8\\Email')),
                new Expression(
                    'new {{ PHPDOCc27b34d4d91bc4d52190708db8447e09 }}()',
                    [
                        '{{ PHPDOCc27b34d4d91bc4d52190708db8447e09 }}' => new Fqsen('\\PHP8\\Email'),
                    ],
                )
            )
        );
        $constructor->addArgument(new Argument('birth_date', new Object_(new Fqsen('\\' . DateTimeImmutable::class))));
        $constructor->addArgument(
            new Argument(
                'created_at',
                new Object_(new Fqsen('\\' . DateTimeImmutable::class)),
                new Expression(
                    'new {{ PHPDOCf854926c6ee2b49d3385c30295984295 }}(\'now\')',
                    [
                        '{{ PHPDOCf854926c6ee2b49d3385c30295984295 }}' => new Fqsen('\\DateTimeImmutable')
                    ]
                )
            )
        );
        $constructor->addArgument(
            new Argument(
                'uses_constants',
                new Array_(),
                new Expression(
                    '[{{ PHPDOC19b72d1f430d952a8dfe2384dd4e93dc }}]',
                    [
                        '{{ PHPDOC19b72d1f430d952a8dfe2384dd4e93dc }}' => new Fqsen('\self::DEFAULT_VALUE'),
                    ],
                ),
            ),
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
                '\PHP8\ConstructorPromotion::$uses_constants' => $this->expectedUsesConstantsProperty(),
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
            new Location(31, 718)
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
            "'default name'",
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
            new Expression(
                'new {{ PHPDOCc27b34d4d91bc4d52190708db8447e09 }}()',
                [
                    '{{ PHPDOCc27b34d4d91bc4d52190708db8447e09 }}' => new Fqsen('\\PHP8\\Email'),
                ],
            ),
            false,
            new Location(27),
            new Location(27),
            New Object_(new Fqsen('\\PHP8\\Email')),
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
                'new {{ PHPDOCf854926c6ee2b49d3385c30295984295 }}(\'now\')',
                [
                    '{{ PHPDOCf854926c6ee2b49d3385c30295984295 }}' => new Fqsen('\\DateTimeImmutable'),
                ],
            ),
            false,
            new Location(29),
            new Location(29),
            new Object_(new Fqsen('\\' . DateTimeImmutable::class)),
        );
    }

    private function expectedUsesConstantsProperty()
    {
        return new Property(
            new Fqsen('\PHP8\ConstructorPromotion::$uses_constants'),
            new Visibility(Visibility::PRIVATE_),
            null,
            new Expression(
                '[{{ PHPDOC19b72d1f430d952a8dfe2384dd4e93dc }}]',
                [
                    '{{ PHPDOC19b72d1f430d952a8dfe2384dd4e93dc }}' => new Fqsen('\self::DEFAULT_VALUE'),
                ],
            ),
            false,
            new Location(30),
            new Location(30),
            new Array_(),
        );
    }
}
