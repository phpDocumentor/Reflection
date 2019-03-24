<?php

namespace phpDocumentor\Reflection;

use phpDocumentor\Reflection\File\LocalFile;

/**
 * @BeforeMethods({"init"})
 */
final class ProjectFactoryBench
{
    /**
     * @var ProjectFactory
     */
    private $factory;

    public function init()
    {
        $this->factory = \phpDocumentor\Reflection\Php\ProjectFactory::createInstance();
    }

    /**
     * @Revs({1, 8, 64, 1024})
     */
    public function benchCreateSingleFileProject()
    {
        $this->factory->create('myProject', [new LocalFile(__DIR__ . '/../assets/phpunit_assert.php')]);
    }
}
