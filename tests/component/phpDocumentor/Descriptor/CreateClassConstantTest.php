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

class CreateClassConstantTest extends BaseComponentTestCase
{
    /**
     * @dataProvider switchBetweenStrategies
     */
    public function testConstHasSummary($projectDescriptor, $filename)
    {
        $this->projectDescriptor = $projectDescriptor;
        $this->filename          = $filename;

        $constant = $this->getClassConstantWithName('\Luigi\Pizza', 'PACKAGING');

        $this->assertSame('The packaging method used to transport the pizza.', $constant->getSummary());
    }

    /**
     * @dataProvider switchBetweenStrategies
     */
    public function testConstHasDescription($projectDescriptor, $filename)
    {
        $this->projectDescriptor = $projectDescriptor;
        $this->filename          = $filename;

        $constant = $this->getClassConstantWithName('\Luigi\Pizza', 'PACKAGING');

        $this->assertSame('', $constant->getDescription());
    }

    /**
     * @dataProvider switchBetweenStrategies
     */
    public function testConstHasName($projectDescriptor, $filename)
    {
        $this->projectDescriptor = $projectDescriptor;
        $this->filename          = $filename;

        $constant = $this->getClassConstantWithName('\Luigi\Pizza', 'PACKAGING');

        $this->assertSame('PACKAGING', $constant->getName());
        $this->assertSame('\Luigi\Pizza::PACKAGING', $constant->getFullyQualifiedStructuralElementName());
    }

    /**
     * @dataProvider switchBetweenStrategies
     */
    public function testConstHasPackage($projectDescriptor, $filename)
    {
        $this->projectDescriptor = $projectDescriptor;
        $this->filename          = $filename;

        $constant = $this->getClassConstantWithName('\Luigi\Pizza', 'PACKAGING');

        $this->assertPackageName($constant, 'Luigi\Pizza');
    }

    /**
     * @dataProvider switchBetweenStrategies
     */
    public function testConstHasValue($projectDescriptor, $filename)
    {
        $this->projectDescriptor = $projectDescriptor;
        $this->filename          = $filename;

        $constant = $this->getClassConstantWithName('\Luigi\Pizza', 'PACKAGING');

        $this->assertSame('\'box\'', $constant->getValue());
    }

    /**
     * @dataProvider switchBetweenStrategies
     */
    public function testDefinedWithMultipleConstantsHasFirstASummaryAndDescription($projectDescriptor, $filename)
    {
        $this->projectDescriptor = $projectDescriptor;
        $this->filename          = $filename;

        $constant = $this->getClassConstantWithName('\Luigi\Pizza', 'DELIVERY');
        $this->assertSame(
            'designates that the delivery method is to deliver the pizza to the customer.',
            $constant->getSummary()
        );
        $this->assertSame('', $constant->getDescription());
    }

    /**
     * @dataProvider switchBetweenStrategies
     */
    public function testDefinedWithMultipleConstantsHasSecondASummaryAndDescription($projectDescriptor, $filename)
    {
        $this->projectDescriptor = $projectDescriptor;
        $this->filename          = $filename;

        $constant = $this->getClassConstantWithName('\Luigi\Pizza', 'PICKUP');
        $this->assertSame(
            'designates that the delivery method is that the customer picks the pizza up.',
            $constant->getSummary()
        );
        $this->assertSame('', $constant->getDescription());
    }
}
