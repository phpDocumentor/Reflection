<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2013 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Descriptor;

use phpDocumentor\Descriptor\FileDescriptor;
use phpDocumentor\Descriptor\PropertyDescriptor;

class CreatePropertyTest extends BaseComponentTestCase
{
    /**
     * @dataProvider switchBetweenStrategies
     */
    public function testPropertyDefinedUsingPhp4FormatIsRecognized($projectDescriptor, $filename)
    {
        $this->projectDescriptor = $projectDescriptor;
        $this->filename          = $filename;

        $property = $this->getPropertyWithName('\Luigi\Pizza', 'legacy');

        $this->assertInstanceOf('phpDocumentor\Descriptor\PropertyDescriptor', $property);
        $this->assertSame('\Luigi\Pizza::legacy', $property->getFullyQualifiedStructuralElementName());
    }

    /**
     * @dataProvider switchBetweenStrategies
     */
    public function testClassCanHaveProperty($projectDescriptor, $filename)
    {
        $this->projectDescriptor = $projectDescriptor;
        $this->filename          = $filename;

        $property = $this->getPropertyWithName('\Luigi\Pizza', 'packaging');

        $this->assertInstanceOf('phpDocumentor\Descriptor\PropertyDescriptor', $property);
    }

    /**
     * @dataProvider switchBetweenStrategies
     */
    public function testTraitCanHaveProperty($projectDescriptor, $filename)
    {
        $this->projectDescriptor = $projectDescriptor;
        $this->filename          = $filename;

        $property = $this->getPropertyWithNameFromTrait('\Luigi\Pizza\HasPrice', 'temporaryPrice');

        $this->assertInstanceOf('phpDocumentor\Descriptor\PropertyDescriptor', $property);
    }

    /**
     * @dataProvider switchBetweenStrategies
     */
    public function testPropertyHasSummary($projectDescriptor, $filename)
    {
        $this->projectDescriptor = $projectDescriptor;
        $this->filename          = $filename;

        $property = $this->getPropertyWithName('\Luigi\Pizza', 'size');

        $this->assertSame('The size of the pizza in centimeters, defaults to 20cm.', $property->getSummary());
    }

    /**
     * @dataProvider switchBetweenStrategies
     */
    public function testPropertyHasSummaryFromVarTag($projectDescriptor, $filename)
    {
        $this->projectDescriptor = $projectDescriptor;
        $this->filename          = $filename;

        $property = $this->getPropertyWithName('\Luigi\Pizza', 'instance');

        $this->assertSame('contains the active instance for this Pizza.', $property->getSummary());
    }

    /**
     * @dataProvider switchBetweenStrategies
     */
    public function testPropertyHasDescription($projectDescriptor, $filename)
    {
        $this->projectDescriptor = $projectDescriptor;
        $this->filename          = $filename;

        $property = $this->getPropertyWithName('\Luigi\Pizza', 'instance');

        $this->assertSame('', $property->getDescription());
    }

    /**
     * @dataProvider switchBetweenStrategies
     */
    public function testPropertyHasEmptySummaryAndDescriptionWithoutDocBlock($projectDescriptor, $filename)
    {
        $this->projectDescriptor = $projectDescriptor;
        $this->filename          = $filename;

        $property = $this->getPropertyWithName('\Luigi\Pizza', 'legacy');

        $this->assertSame('', $property->getSummary());
        $this->assertSame('', $property->getDescription());
    }

    /**
     * @dataProvider switchBetweenStrategies
     */
    public function testPropertyHasName($projectDescriptor, $filename)
    {
        $this->projectDescriptor = $projectDescriptor;
        $this->filename          = $filename;

        $property = $this->getPropertyWithName('\Luigi\Pizza', 'instance');

        $this->assertSame('instance', $property->getName());
        $this->assertSame('\Luigi\Pizza::instance', $property->getFullyQualifiedStructuralElementName());
    }

    /**
     * @dataProvider switchBetweenStrategies
     */
    public function testPropertyCanBeStatic($projectDescriptor, $filename)
    {
        $this->projectDescriptor = $projectDescriptor;
        $this->filename          = $filename;

        $staticProperty    = $this->getPropertyWithName('\Luigi\Pizza', 'instance');
        $nonStaticProperty = $this->getPropertyWithName('\Luigi\Pizza', 'packaging');

        $this->assertTrue($staticProperty->isStatic());
        $this->assertFalse($nonStaticProperty->isStatic());
    }

    /**
     * @dataProvider switchBetweenStrategies
     */
    public function testPropertyCanBePrivate($projectDescriptor, $filename)
    {
        $this->projectDescriptor = $projectDescriptor;
        $this->filename          = $filename;

        $property = $this->getPropertyWithName('\Luigi\Pizza', 'instance');

        $this->assertSame('private', $property->getVisibility());
    }

    /**
     * @dataProvider switchBetweenStrategies
     */
    public function testPropertyCanBeProtected($projectDescriptor, $filename)
    {
        $this->projectDescriptor = $projectDescriptor;
        $this->filename          = $filename;

        $property = $this->getPropertyWithName('\Luigi\Pizza', 'packaging');

        $this->assertSame('protected', $property->getVisibility());
    }

    /**
     * @dataProvider switchBetweenStrategies
     */
    public function testPropertyCanBePublic($projectDescriptor, $filename)
    {
        $this->projectDescriptor = $projectDescriptor;
        $this->filename          = $filename;

        $property = $this->getPropertyWithName('\Luigi\Pizza', 'size');

        $this->assertSame('public', $property->getVisibility());
    }

    /**
     * @dataProvider switchBetweenStrategies
     */
    public function testPropertyIsPublicWhenDefinedWithoutVisibility($projectDescriptor, $filename)
    {
        $this->projectDescriptor = $projectDescriptor;
        $this->filename          = $filename;

        $property = $this->getPropertyWithName('\Luigi\Pizza', 'legacy');

        $this->assertSame('public', $property->getVisibility());
    }

    /**
     * @dataProvider switchBetweenStrategies
     */
    public function testPropertyHasDefault($projectDescriptor, $filename)
    {
        $this->projectDescriptor = $projectDescriptor;
        $this->filename          = $filename;

        $property = $this->getPropertyWithName('\Luigi\Pizza', 'packaging');

        // TODO: self should actually be resolved to the FQCN, or even better, its value?
        $this->assertSame('self::PACKAGING', $property->getDefault());
    }

    /**
     * @dataProvider switchBetweenStrategies
     */
    public function testWithMultiplePropertiesHasFirstASummaryAndDescription($projectDescriptor, $filename)
    {
        $this->projectDescriptor = $projectDescriptor;
        $this->filename          = $filename;

        $property = $this->getPropertyWithName('\Luigi\Pizza', 'packaging');

        $this->assertSame(
            'The type of packaging for this Pizza',
            $property->getSummary()
        );
        $this->assertSame('', $property->getDescription());
    }

    /**
     * @dataProvider switchBetweenStrategies
     */
    public function testWithMultiplePropertiesHasSecondASummaryAndDescription($projectDescriptor, $filename)
    {
        $this->projectDescriptor = $projectDescriptor;
        $this->filename          = $filename;

        $property = $this->getPropertyWithName('\Luigi\Pizza', 'deliveryMethod');

        $this->assertSame(
            'Is the customer picking this pizza up or must it be delivered?',
            $property->getSummary()
        );
        $this->assertSame('', $property->getDescription());
    }
}
