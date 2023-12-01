<?php

declare(strict_types=1);

namespace integration;

use phpDocumentor\Reflection\File\LocalFile;
use phpDocumentor\Reflection\Php\ProjectFactory;
use PHPUnit\Framework\TestCase;

/**
 * Integration tests to check the correct working of processing a namespace into a project.
 *
 * @coversNothing
 */
final class FileDocblockTest extends TestCase
{
    /** @var ProjectFactory */
    private $fixture;

    protected function setUp() : void
    {
        $this->fixture = ProjectFactory::createInstance();
    }

    /**
     * @dataProvider fileProvider
     */
    public function testFileDocblock(string  $fileName) : void
    {
        $project = $this->fixture->create(
            'MyProject',
            [new LocalFile($fileName)]
        );

        $this->assertEquals(
            'This file is part of phpDocumentor.',
            $project->getFiles()[$fileName]->getDocBlock()->getSummary()
        );
    }

    public function fileProvider() : array
    {
        return [
            [ __DIR__ . '/data/GlobalFiles/empty.php' ],
            [ __DIR__ . '/data/GlobalFiles/empty_with_declare.php' ],
            [ __DIR__ . '/data/GlobalFiles/empty_shebang.php' ],
            [ __DIR__ . '/data/GlobalFiles/psr12.php' ],
            [ __DIR__ . '/data/GlobalFiles/docblock_followed_by_html.php' ],
        ];
    }

    /**
     * @covers \phpDocumentor\Reflection\Php\Factory\File::create
     * @covers \phpDocumentor\Reflection\Php\Factory\File::<private>
     */
    public function testConditionalFunctionDefine() : void
    {
        $fileName =  __DIR__ . '/data/GlobalFiles/conditional_function.php';
        $project = $this->fixture->create(
            'MyProject',
            [new LocalFile($fileName)]
        );

        $this->assertCount(
            4,
            $project->getFiles()[$fileName]->getFunctions()
        );
    }

    /**
     * @covers \phpDocumentor\Reflection\Php\Factory\File::create
     * @covers \phpDocumentor\Reflection\Php\Factory\File::<private>
     */
    public function testGlobalNamespacedFunctionDefine() : void
    {
        $fileName =  __DIR__ . '/data/GlobalFiles/global_namspaced_function.php';
        $project = $this->fixture->create(
            'MyProject',
            [new LocalFile($fileName)]
        );

        $this->assertCount(
            1,
            $project->getFiles()[$fileName]->getFunctions()
        );
    }

    /**
     * @covers \phpDocumentor\Reflection\Php\Factory\File::create
     * @covers \phpDocumentor\Reflection\Php\Factory\File::<private>
     */
    public function testFileWithInlineFunction() : void
    {
        $fileName =  __DIR__ . '/data/GlobalFiles/inline_function.php';
        $project = $this->fixture->create(
            'MyProject',
            [new LocalFile($fileName)]
        );

        $this->assertCount(
            1,
            $project->getFiles()[$fileName]->getClasses()
        );
    }
}
