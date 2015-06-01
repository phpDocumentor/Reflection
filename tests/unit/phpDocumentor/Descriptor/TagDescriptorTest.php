<?php

namespace phpDocumentor\Descriptor;

class TagDescriptorTest extends \PHPUnit_Framework_TestCase
{
    const TAG_NAME = 'test';

    /** @var TagDescriptor */
    private $fixture;

    /**
     * Instantiates the fixture with its dependencies.
     */
    protected function setUp()
    {
        $this->fixture = new TagDescriptor(self::TAG_NAME);
    }

    /**
     * @covers phpDocumentor\Descriptor\TagDescriptor::__construct
     * @covers phpDocumentor\Descriptor\TagDescriptor::getName
     * @covers phpDocumentor\Descriptor\TagDescriptor::setName
     */
    public function testNameIsRegisteredOnInstantiationAndReturned()
    {
        $this->assertSame(self::TAG_NAME, $this->fixture->getName());
    }

    /**
     * @covers phpDocumentor\Descriptor\TagDescriptor::setDescription
     * @covers phpDocumentor\Descriptor\TagDescriptor::getDescription
     */
    public function testSettingAndReturningADescription()
    {
        // Arrange
        $description = 'Description';
        $this->assertSame('', $this->fixture->getDescription());

        // Act
        $this->fixture->setDescription($description);

        // Assert
        $this->assertSame($description, $this->fixture->getDescription());
    }
}
