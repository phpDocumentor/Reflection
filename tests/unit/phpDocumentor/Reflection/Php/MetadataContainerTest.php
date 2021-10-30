<?php

declare(strict_types=1);

namespace phpDocumentor\Reflection\Php;

use phpDocumentor\Reflection\Exception;
use phpDocumentor\Reflection\Metadata\MetaDataContainer as MetaDataContainerInterface;

trait MetadataContainerTest
{
    /**
     * @covers ::addMetadata
     * @covers ::getMetadata
     */
    public function testSetMetaDataForNonExistingKey(): void
    {
        $stub = new MetadataStub('stub');

        $this->getFixture()->addMetadata($stub);

        self::assertSame(['stub' => $stub], $this->getFixture()->getMetadata());
    }

    /**
     * @covers ::addMetadata
     */
    public function testSetMetaDataWithExistingKeyThrows(): void
    {
        self::expectException(Exception::class);

        $stub = new MetadataStub('stub');

        $this->getFixture()->addMetadata($stub);
        $this->getFixture()->addMetadata($stub);
    }

    abstract public function getFixture(): MetaDataContainerInterface;
}
