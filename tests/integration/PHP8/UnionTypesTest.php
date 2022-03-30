<?php

declare(strict_types=1);

namespace integration\PHP8;

use phpDocumentor\Reflection\File\LocalFile;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Php\Project;
use phpDocumentor\Reflection\Php\ProjectFactory;
use phpDocumentor\Reflection\Types\False_;
use phpDocumentor\Reflection\Types\Compound;
use phpDocumentor\Reflection\Types\Integer;
use phpDocumentor\Reflection\Types\Mixed_;
use phpDocumentor\Reflection\Types\Null_;
use phpDocumentor\Reflection\Types\Object_;
use phpDocumentor\Reflection\Types\Static_;
use phpDocumentor\Reflection\Types\String_;
use PHPUnit\Framework\TestCase;

/**
 * @coversNothing
 */
final class UnionTypesTest extends TestCase
{
    const FILE = __DIR__ . '/../data/PHP8/UnionTypes.php';
    /** @var ProjectFactory */
    private $fixture;

    public function testUnionTypes() : void
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

        $class = $file->getClasses()['\PHP8\UnionTypes'];

        self::assertEquals(new Compound([new String_(), new Null_(), new Object_(new Fqsen('\Foo\Date'))]), $class->getMethods()['\PHP8\UnionTypes::union()']->getReturnType());
        self::assertEquals(new Compound([new Integer(), new False_()]), $class->getMethods()['\PHP8\UnionTypes::union()']->getArguments()[0]->getType());
        self::assertEquals(new Compound([new String_(), new Null_(), new False_()]), $class->getProperties()['\PHP8\UnionTypes::$property']->getType());
    }
}
