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

class CreateGlobalConstantTest extends BaseComponentTestCase
{
    /**
     * @dataProvider switchBetweenStrategies
     */
    public function testDefinedUsingConstHasSummary($projectDescriptor, $filename)
    {
        $this->projectDescriptor = $projectDescriptor;
        $this->filename          = $filename;

        $constant = $this->getFileConstantWithName('\Luigi\Pizza\VAT_HIGH');

        $this->assertSame('The high VAT percentage.', $constant->getSummary());
    }

    /**
     * @dataProvider switchBetweenStrategies
     */
    public function testDefinedUsingConstHasDescription($projectDescriptor, $filename)
    {
        $this->projectDescriptor = $projectDescriptor;
        $this->filename          = $filename;

        $constant = $this->getFileConstantWithName('\Luigi\Pizza\VAT_HIGH');

        $this->assertSame('This describes the VAT percentage for all non-food items.', $constant->getDescription());
    }

    /**
     * @dataProvider switchBetweenStrategies
     */
    public function testDefinedUsingConstHasName($projectDescriptor, $filename)
    {
        $this->projectDescriptor = $projectDescriptor;
        $this->filename          = $filename;

        $constant = $this->getFileConstantWithName('\Luigi\Pizza\VAT_HIGH');

        $this->assertSame('VAT_HIGH', $constant->getName());
        $this->assertSame('\Luigi\Pizza\VAT_HIGH', $constant->getFullyQualifiedStructuralElementName());
        $this->assertSame('21', $constant->getValue());
    }

    /**
     * @dataProvider switchBetweenStrategies
     */
    public function testDefinedUsingConstHasPackage($projectDescriptor, $filename)
    {
        $this->projectDescriptor = $projectDescriptor;
        $this->filename          = $filename;

        $constant = $this->getFileConstantWithName('\Luigi\Pizza\VAT_HIGH');

        $this->assertPackageName($constant, 'Luigi\Pizza');
    }

    /**
     * @dataProvider switchBetweenStrategies
     */
    public function testDefinedUsingConstHasNamespace($projectDescriptor, $filename)
    {
        $this->projectDescriptor = $projectDescriptor;
        $this->filename          = $filename;

        $constant = $this->getFileConstantWithName('\Luigi\Pizza\VAT_HIGH');

        $this->assertSame('\Luigi\Pizza', $constant->getNamespace());
    }

    /**
     * @dataProvider switchBetweenStrategies
     */
    public function testDefinedUsingConstHasValue($projectDescriptor, $filename)
    {
        $this->projectDescriptor = $projectDescriptor;
        $this->filename          = $filename;

        $constant = $this->getFileConstantWithName('\Luigi\Pizza\VAT_HIGH');

        $this->assertSame('21', $constant->getValue());
    }

    /**
     * @dataProvider switchBetweenStrategies
     */
    public function testDefinedWithMultipleConstantsHasFirstASummaryAndDescription($projectDescriptor, $filename)
    {
        $this->projectDescriptor = $projectDescriptor;
        $this->filename          = $filename;

        $constant = $this->getFileConstantWithName('\Luigi\Pizza\SIZE_5CM');

        $this->assertSame('A 5 centimeter pizza size.', $constant->getSummary());
        $this->assertSame('', $constant->getDescription());
    }

    /**
     * @dataProvider switchBetweenStrategies
     */
    public function testDefinedWithMultipleConstantsHasSecondASummaryAndDescription($projectDescriptor, $filename)
    {
        $this->projectDescriptor = $projectDescriptor;
        $this->filename          = $filename;

        $constant = $this->getFileConstantWithName('\Luigi\Pizza\SIZE_10CM');

        $this->assertSame('A 10 centimeter pizza size.', $constant->getSummary());
        $this->assertSame('', $constant->getDescription());
    }

    /**
     * @dataProvider switchBetweenStrategies
     */
    public function testDefinedUsingDefineFunctionHasSummary($projectDescriptor, $filename)
    {
        $this->projectDescriptor = $projectDescriptor;
        $this->filename          = $filename;

        $constant = $this->getFileConstantWithName('\Luigi\Pizza\VAT_LOW');

        $this->assertSame('The low VAT percentage.', $constant->getSummary());
    }

    /**
     * @dataProvider switchBetweenStrategies
     */
    public function testDefinedUsingDefineFunctionHasDescription($projectDescriptor, $filename)
    {
        $this->projectDescriptor = $projectDescriptor;
        $this->filename          = $filename;

        $constant = $this->getFileConstantWithName('\Luigi\Pizza\VAT_LOW');

        $this->assertSame('This describes the VAT percentage for all non-food items.', $constant->getDescription());
    }

    /**
     * @dataProvider switchBetweenStrategies
     */
    public function testDefinedUsingDefineFunctionHasName($projectDescriptor, $filename)
    {
        $this->projectDescriptor = $projectDescriptor;
        $this->filename          = $filename;

        $constant = $this->getFileConstantWithName('\Luigi\Pizza\VAT_LOW');

        $this->assertSame('VAT_LOW', $constant->getName());
        $this->assertSame('\Luigi\Pizza\VAT_LOW', $constant->getFullyQualifiedStructuralElementName());
    }

    /**
     * @dataProvider switchBetweenStrategies
     */
    public function testDefinedUsingDefineFunctionHasValue($projectDescriptor, $filename)
    {
        $this->projectDescriptor = $projectDescriptor;
        $this->filename          = $filename;

        $constant = $this->getFileConstantWithName('\Luigi\Pizza\VAT_LOW');

        $this->assertSame('6', $constant->getValue());
    }

    /**
     * @dataProvider switchBetweenStrategies
     */
    public function testDefinedUsingDefineFunctionHasPackage($projectDescriptor, $filename)
    {
        $this->projectDescriptor = $projectDescriptor;
        $this->filename          = $filename;

        $constant = $this->getFileConstantWithName('\Luigi\Pizza\VAT_LOW');

        $this->assertPackageName($constant, 'Luigi\Pizza');
    }
}
