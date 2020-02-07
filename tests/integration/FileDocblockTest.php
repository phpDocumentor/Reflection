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


    public function fileProvider(string $file)
    {
        return [
            [ __DIR__ . '/data/GlobalFiles/empty.php' ],
            [ __DIR__ . '/data/GlobalFiles/empty_with_declare.php' ],
            [ __DIR__ . '/data/GlobalFiles/empty_shebang.php' ],
        ];
    }
}
