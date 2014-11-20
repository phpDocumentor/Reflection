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

class CreateArgumentTest extends BaseComponentTestCase
{
    /**
     * @dataProvider switchBetweenStrategies
     */
    public function testArgumentCanHaveName($projectDescriptor, $filename)
    {
        $this->projectDescriptor = $projectDescriptor;
        $this->filename          = $filename;

        $method = $this->getMethodWithName('\Luigi\Pizza\PizzaComponentFactory', 'calculatePrice');

        $firstArgument = current($method->getArguments()->getAll());
        $this->assertSame('$additionalPrices', $firstArgument->getName());
    }

    /**
     * @dataProvider switchBetweenStrategies
     */
    public function testArgumentCanHaveDescription($projectDescriptor, $filename)
    {
        $this->projectDescriptor = $projectDescriptor;
        $this->filename          = $filename;

        $method = $this->getMethodWithName('\Luigi\Pizza\PizzaComponentFactory', 'calculatePrice');

        $firstArgument = current($method->getArguments()->getAll());
        $this->assertSame('Additional costs may be passed', $firstArgument->getDescription());
    }

    /**
     * @dataProvider switchBetweenStrategies
     */
    public function testArgumentCanHaveType($projectDescriptor, $filename)
    {
        $this->markTestIncomplete();
    }

    /**
     * @dataProvider switchBetweenStrategies
     */
    public function testArgumentCanHaveDefault($projectDescriptor, $filename)
    {
        $this->projectDescriptor = $projectDescriptor;
        $this->filename          = $filename;

        $methodWithArgWithDefault = $this->getMethodWithName('\Luigi\Pizza', 'setSize');
        $firstArgument            = current($methodWithArgWithDefault->getArguments()->getAll());
        $this->assertSame('\Luigi\Pizza\SIZE_20CM', $firstArgument->getDefault());

        $methodWithArgWithoutDefault = $this->getMethodWithName('\Luigi\Pizza\PizzaComponentFactory', 'calculatePrice');
        $firstArgument               = current($methodWithArgWithoutDefault->getArguments()->getAll());
        $this->assertNull($firstArgument->getDefault());
    }

    /**
     * @dataProvider switchBetweenStrategies
     */
    public function testArgumentCanBeVariadic($projectDescriptor, $filename)
    {
        $this->projectDescriptor = $projectDescriptor;
        $this->filename          = $filename;

        $methodWithVariadic = $this->getMethodWithName('\Luigi\Pizza\PizzaComponentFactory', 'calculatePrice');
        $firstArgument      = current($methodWithVariadic->getArguments()->getAll());
        $this->assertTrue($firstArgument->isVariadic());

        $methodWithoutVariadic = $this->getMethodWithName('\Luigi\Pizza', 'setSize');
        $firstArgument         = current($methodWithoutVariadic->getArguments()->getAll());
        $this->assertFalse($firstArgument->isVariadic());
    }

    /**
     * @dataProvider switchBetweenStrategies
     */
    public function testArgumentCanBeByReference($projectDescriptor, $filename)
    {
        $this->projectDescriptor = $projectDescriptor;
        $this->filename          = $filename;

        $methodWithArgByRef    = $this->getMethodWithName('\Luigi\Pizza', 'setSize');
        $firstArgument         = current($methodWithArgByRef->getArguments()->getAll());
        $this->assertTrue($firstArgument->isByReference());

        $methodWithArgNotByRef = $this->getMethodWithName('\Luigi\Pizza\PizzaComponentFactory', 'calculatePrice');
        $firstArgument         = current($methodWithArgNotByRef->getArguments()->getAll());
        $this->assertFalse($firstArgument->isByReference());
    }
}
