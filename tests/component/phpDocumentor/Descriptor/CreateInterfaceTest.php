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

class CreateInterfaceTest extends BaseComponentTestCase
{
    /**
     * @dataProvider switchBetweenStrategies
     */
    public function testInterfaceHasName($projectDescriptor, $filename)
    {
        $this->projectDescriptor = $projectDescriptor;
        $this->filename          = $filename;

        $interface = $this->fetchInterface('\Luigi\Pizza\Valued');

        $this->assertName($interface, 'Valued');
        $this->assertFullyQualifiedStructuralElementName($interface, '\Luigi\Pizza\Valued');
    }

    /**
     * @dataProvider switchBetweenStrategies
     */
    public function testInterfaceHasNamespace($projectDescriptor, $filename)
    {
        $this->projectDescriptor = $projectDescriptor;
        $this->filename          = $filename;

        $interface = $this->fetchInterface('\Luigi\Pizza\Valued');

        $this->assertNamespace($interface, '\Luigi\Pizza');
    }

    /**
     * @dataProvider switchBetweenStrategies
     */
    public function testInterfaceHasNoParent($projectDescriptor, $filename)
    {
        $this->projectDescriptor = $projectDescriptor;
        $this->filename          = $filename;

        $interface = $this->fetchInterface('\Luigi\Pizza\Valued');

        $this->assertSame(0, $interface->getParents()->count());
    }

    /**
     * @dataProvider switchBetweenStrategies
     */
    public function testInterfaceHasParent($projectDescriptor, $filename)
    {
        $this->projectDescriptor = $projectDescriptor;
        $this->filename          = $filename;

        $interface = $this->fetchInterface('\Luigi\Pizza\Topping');

        $this->assertGreaterThan(0, $interface->getParent()->count());
        $this->assertSame('\Luigi\Pizza\Valued', $interface->getParent()->get('\Luigi\Pizza\Valued'));
        $this->assertSame('\Serializable', $interface->getParent()->get('\Serializable'));
    }

    /**
     * @dataProvider switchBetweenStrategies
     */
    public function testInterfaceHasPackage($projectDescriptor, $filename)
    {
        $this->projectDescriptor = $projectDescriptor;
        $this->filename          = $filename;

        $interface = $this->fetchInterface('\Luigi\Pizza\Valued');

        $this->assertPackageName($interface, 'Luigi\Pizza');
    }

    /**
     * @dataProvider switchBetweenStrategies
     */
    public function testInterfaceHasSummary($projectDescriptor, $filename)
    {
        $this->projectDescriptor = $projectDescriptor;
        $this->filename          = $filename;

        $interface = $this->fetchInterface('\Luigi\Pizza\Valued');

        $this->assertSummary($interface, 'Any class implementing this interface has an associated price.');
    }

    /**
     * @dataProvider switchBetweenStrategies
     */
    public function testInterfaceHasDescription($projectDescriptor, $filename)
    {
        $this->projectDescriptor = $projectDescriptor;
        $this->filename          = $filename;

        $interface = $this->fetchInterface('\Luigi\Pizza\Valued');

        $this->assertDescription(
            $interface,
            'Using this interface we can easily add the price of all components in a pizza by checking for this '
            . 'interface and' . "\n" . 'adding the prices together for all components.'
        );
    }

    /**
     * @dataProvider switchBetweenStrategies
     */
    public function testInterfaceHasAllMethods($projectDescriptor, $filename)
    {
        $this->projectDescriptor = $projectDescriptor;
        $this->filename          = $filename;

        $interface = $this->fetchInterface('\Luigi\Pizza\Valued');

        $this->assertHasMethod($interface, 'getPrice', '\Luigi\Pizza\Valued::getPrice()');
    }

    /**
     * @dataProvider switchBetweenStrategies
     */
    public function testInterfaceHasAllConstants($projectDescriptor, $filename)
    {
        $this->projectDescriptor = $projectDescriptor;
        $this->filename          = $filename;

        $interface = $this->fetchInterface('\Luigi\Pizza\Valued');

        $this->assertFileHasConstant($interface, 'BASE_PRICE', '\Luigi\Pizza\Valued::BASE_PRICE');
    }
}
