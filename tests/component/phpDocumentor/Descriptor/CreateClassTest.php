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

class CreateClassTest extends BaseComponentTestCase
{
    /**
     * @dataProvider switchBetweenStrategies
     */
    public function testClassHasName($projectDescriptor, $filename)
    {
        $this->projectDescriptor = $projectDescriptor;
        $this->filename          = $filename;

        $class = $this->fetchClass('\Luigi\Pizza');

        $this->assertName($class, 'Pizza');
        $this->assertFullyQualifiedStructuralElementName($class, '\Luigi\Pizza');
    }

    /**
     * @dataProvider switchBetweenStrategies
     */
    public function testClassHasNamespace($projectDescriptor, $filename)
    {
        $this->projectDescriptor = $projectDescriptor;
        $this->filename          = $filename;

        $class = $this->fetchClass('\Luigi\Pizza');

        $this->assertNamespace($class, '\Luigi');
    }

    /**
     * @dataProvider switchBetweenStrategies
     */
    public function testClassHasNoParent($projectDescriptor, $filename)
    {
        $this->projectDescriptor = $projectDescriptor;
        $this->filename          = $filename;

        $class = $this->fetchClass('\Luigi\Pizza');

        $this->assertParentElement($class, null);
    }

    /**
     * @dataProvider switchBetweenStrategies
     */
    public function testClassHasParent($projectDescriptor, $filename)
    {
        $this->projectDescriptor = $projectDescriptor;
        $this->filename          = $filename;

        $class = $this->fetchClass('\Luigi\Pizza\StyleFactory');

        $this->assertParentElement($class, '\Luigi\Pizza\PizzaComponentFactory');
    }

    /**
     * @dataProvider switchBetweenStrategies
     */
    public function testClassThatImplementsNoInterfacesHasNoInterfaces($projectDescriptor, $filename)
    {
        $this->projectDescriptor = $projectDescriptor;
        $this->filename          = $filename;

        $class = $this->fetchClass('\Luigi\Pizza\StyleFactory');

        $this->assertEmpty($class->getInterfaces()->getAll());
    }

    /**
     * @dataProvider switchBetweenStrategies
     */
    public function testClassWhichImplementsInterfaceLinksToThatInterface($projectDescriptor, $filename)
    {
        $this->projectDescriptor = $projectDescriptor;
        $this->filename          = $filename;

        $class = $this->fetchClass('\Luigi\Pizza');

        $this->assertHasInterface($class, '\Luigi\Pizza\Valued');
    }

    /**
     * @dataProvider switchBetweenStrategies
     */
    public function testClassWhichImplementsMultipleInterfacesLinkToAllInterfaces($projectDescriptor, $filename)
    {
        $this->projectDescriptor = $projectDescriptor;
        $this->filename          = $filename;

        $class = $this->fetchClass('\Luigi\Pizza\PizzaComponentFactory');

        $this->assertHasInterface($class, '\Traversable');
        $this->assertHasInterface($class, '\Luigi\Pizza\Valued');
    }

    /**
     * @dataProvider switchBetweenStrategies
     */
    public function testClassHasPackage($projectDescriptor, $filename)
    {
        $this->projectDescriptor = $projectDescriptor;
        $this->filename          = $filename;

        $class = $this->fetchClass('\Luigi\Pizza');

        $this->assertPackageName($class, 'Luigi\Pizza');
    }

    /**
     * @dataProvider switchBetweenStrategies
     */
    public function testClassCanBeFinal($projectDescriptor, $filename)
    {
        $this->projectDescriptor = $projectDescriptor;
        $this->filename          = $filename;

        $class = $this->fetchClass('\Luigi\Pizza\StyleFactory');

        $this->assertTrue($class->isFinal());
    }

    /**
     * @dataProvider switchBetweenStrategies
     */
    public function testClassCanBeAbstract($projectDescriptor, $filename)
    {
        $this->projectDescriptor = $projectDescriptor;
        $this->filename          = $filename;

        $class = $this->fetchClass('\Luigi\Pizza\PizzaComponentFactory');

        $this->assertTrue($class->isAbstract());
    }

    /**
     * @dataProvider switchBetweenStrategies
     */
    public function testClassHasSummary($projectDescriptor, $filename)
    {
        $this->projectDescriptor = $projectDescriptor;
        $this->filename          = $filename;

        $class = $this->fetchClass('\Luigi\Pizza');

        $this->assertSummary($class, 'Class representing a single Pizza.');
    }

    /**
     * @dataProvider switchBetweenStrategies
     */
    public function testClassHasDescription($projectDescriptor, $filename)
    {
        $this->projectDescriptor = $projectDescriptor;
        $this->filename          = $filename;

        $class = $this->fetchClass('\Luigi\Pizza');

        $this->assertDescription($class, 'This is Luigi\'s famous Pizza.');
    }

    /**
     * @dataProvider switchBetweenStrategies
     */
    public function testClassHasAllMethods($projectDescriptor, $filename)
    {
        $this->projectDescriptor = $projectDescriptor;
        $this->filename          = $filename;

        $class = $this->fetchClass('\Luigi\Pizza');

        $this->assertHasMethod($class, '__construct', '\Luigi\Pizza::__construct()');
        $this->assertHasMethod($class, 'addTopping', '\Luigi\Pizza::addTopping()');
        $this->assertHasMethod($class, 'setSauce', '\Luigi\Pizza::setSauce()');
        $this->assertHasMethod($class, 'getPrice', '\Luigi\Pizza::getPrice()');
        $this->assertHasMethod($class, 'createInstance', '\Luigi\Pizza::createInstance()');
        $this->assertHasMethod($class, 'getInstance', '\Luigi\Pizza::getInstance()');
    }

    /**
     * @dataProvider switchBetweenStrategies
     */
    public function testClassHasAllProperties($projectDescriptor, $filename)
    {
        $this->projectDescriptor = $projectDescriptor;
        $this->filename          = $filename;

        $class = $this->fetchClass('\Luigi\Pizza');

        $this->assertHasProperty($class, 'instance', '\Luigi\Pizza::instance');
        $this->assertHasProperty($class, 'style', '\Luigi\Pizza::style');
        $this->assertHasProperty($class, 'sauce', '\Luigi\Pizza::sauce');
        $this->assertHasProperty($class, 'toppings', '\Luigi\Pizza::toppings');
    }

    /**
     * @dataProvider switchBetweenStrategies
     */
    public function testClassHasAllConstants($projectDescriptor, $filename)
    {
        $this->projectDescriptor = $projectDescriptor;
        $this->filename          = $filename;

        $class = $this->fetchClass('\Luigi\Pizza');

        $this->assertFileHasConstant($class, 'PACKAGING', '\Luigi\Pizza::PACKAGING');
    }

    /**
     * @dataProvider switchBetweenStrategies
     */
    public function testClassUsesTraits($projectDescriptor, $filename)
    {
        $this->projectDescriptor = $projectDescriptor;
        $this->filename          = $filename;

        $class = $this->fetchClass('\Luigi\Pizza\ItalianStyle');

        $this->assertGreaterThan(0, $class->getUsedTraits()->count());
        $this->assertSame('\Luigi\Pizza\HasPrice', $class->getUsedTraits()->get(0));
    }
}