<?php

declare(strict_types=1);

namespace integration;

use phpDocumentor\Reflection\File\LocalFile;
use phpDocumentor\Reflection\Php\ProjectFactory;
use phpDocumentor\Reflection\Types\Compound;
use phpDocumentor\Reflection\Types\Integer;
use phpDocumentor\Reflection\Types\Nullable;
use phpDocumentor\Reflection\Types\String_;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\TestCase;

#[CoversNothing]
final class TypedConstantTest extends TestCase
{
    public function testTypedClassConstantsHaveType(): void
    {
        $file = __DIR__ . '/data/PHP83/TypedConstants.php';
        $projectFactory = ProjectFactory::createInstance();
        $project = $projectFactory->create('My project', [new LocalFile($file)]);

        $class = $project->getFiles()[$file]->getClasses()['\PHP83\TypedConstants'];
        $constants = $class->getConstants();

        $this->assertEquals(new String_(), $constants['\PHP83\TypedConstants::NAME']->getType());
        $this->assertEquals(new Integer(), $constants['\PHP83\TypedConstants::COUNT']->getType());
        $this->assertEquals(new Compound([new String_(), new Integer()]), $constants['\PHP83\TypedConstants::UNION']->getType());
        $this->assertEquals(new Nullable(new String_()), $constants['\PHP83\TypedConstants::NULLABLE']->getType());
        $this->assertNull($constants['\PHP83\TypedConstants::UNTYPED']->getType());
    }
}
