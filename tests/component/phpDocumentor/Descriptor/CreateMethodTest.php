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

class CreateMethodTest extends BaseComponentTestCase
{
    /**
     * @dataProvider switchBetweenStrategies
     */
    public function testClassCanHaveMethod($projectDescriptor, $filename)
    {
        $this->projectDescriptor = $projectDescriptor;
        $this->filename          = $filename;

        $method = $this->getMethodWithName('\Luigi\Pizza', 'createInstance');

        $this->assertInstanceOf('phpDocumentor\Descriptor\MethodDescriptor', $method);
    }

    /**
     * @dataProvider switchBetweenStrategies
     */
    public function testInterfaceCanHaveMethod($projectDescriptor, $filename)
    {
        $this->projectDescriptor = $projectDescriptor;
        $this->filename          = $filename;

        $method = $this->getMethodWithNameFromInterface('\Luigi\Pizza\Valued', 'getPrice');

        $this->assertInstanceOf('phpDocumentor\Descriptor\MethodDescriptor', $method);
    }

    /**
     * @dataProvider switchBetweenStrategies
     */
    public function testTraitCanHaveMethod($projectDescriptor, $filename)
    {
        $this->projectDescriptor = $projectDescriptor;
        $this->filename          = $filename;

        $method = $this->getMethodWithNameFromTrait('\Luigi\Pizza\HasPrice', 'getPrice');

        $this->assertInstanceOf('phpDocumentor\Descriptor\MethodDescriptor', $method);
    }

    /**
     * @dataProvider switchBetweenStrategies
     */
    public function testMethodHasSummary($projectDescriptor, $filename)
    {
        $this->projectDescriptor = $projectDescriptor;
        $this->filename          = $filename;

        $method = $this->getMethodWithName('\Luigi\Pizza', 'createInstance');

        $this->assertSame('Creates a new instance of a Pizza.', $method->getSummary());
    }

    /**
     * @dataProvider switchBetweenStrategies
     */
    public function testMethodHasDescription($projectDescriptor, $filename)
    {
        $this->projectDescriptor = $projectDescriptor;
        $this->filename          = $filename;

        $method = $this->getMethodWithName('\Luigi\Pizza', 'createInstance');

        $this->assertSame(
            "This method can be used to instantiate a new object of this class which can then be retrieved "
            . "using\n{@see self::getInstance()}.",
            $method->getDescription()
        );
    }

    /**
     * @dataProvider switchBetweenStrategies
     */
    public function testMethodHasEmptySummaryAndDescriptionWithoutDocBlock($projectDescriptor, $filename)
    {
        $this->projectDescriptor = $projectDescriptor;
        $this->filename          = $filename;

        $method = $this->getMethodWithName('\Luigi\Pizza', 'getPrice');

        $this->assertSame('', $method->getSummary());
        $this->assertSame('', $method->getDescription());
    }

    /**
     * @dataProvider switchBetweenStrategies
     */
    public function testMethodHasName($projectDescriptor, $filename)
    {
        $this->projectDescriptor = $projectDescriptor;
        $this->filename          = $filename;

        $method = $this->getMethodWithName('\Luigi\Pizza', 'createInstance');

        $this->assertSame('createInstance', $method->getName());
        $this->assertSame('\Luigi\Pizza::createInstance()', $method->getFullyQualifiedStructuralElementName());
    }

    /**
     * @dataProvider switchBetweenStrategies
     */
    public function testMethodHasReturnValue($projectDescriptor, $filename)
    {
        $this->projectDescriptor = $projectDescriptor;
        $this->filename          = $filename;

        $method = $this->getMethodWithName('\Luigi\Pizza', 'getInstance');

        $this->assertSame('self', $method->getResponse()->getTypes()->get(0)->getName());
    }

    /**
     * @dataProvider switchBetweenStrategies
     */
    public function testMethodCanBeStatic($projectDescriptor, $filename)
    {
        $this->projectDescriptor = $projectDescriptor;
        $this->filename          = $filename;

        $staticMethod    = $this->getMethodWithName('\Luigi\Pizza', 'createInstance');
        $nonStaticMethod = $this->getMethodWithName('\Luigi\Pizza', '__construct');

        $this->assertTrue($staticMethod->isStatic());
        $this->assertFalse($nonStaticMethod->isStatic());
    }

    /**
     * @dataProvider switchBetweenStrategies
     */
    public function testMethodCanBeFinal($projectDescriptor, $filename)
    {
        $this->projectDescriptor = $projectDescriptor;
        $this->filename          = $filename;

        $finalMethod    = $this->getMethodWithName('\Luigi\Pizza', 'setSauce');
        $nonFinalMethod = $this->getMethodWithName('\Luigi\Pizza', '__construct');

        $this->assertTrue($finalMethod->isFinal());
        $this->assertFalse($nonFinalMethod->isFinal());
    }

    /**
     * @dataProvider switchBetweenStrategies
     */
    public function testMethodCanBeAbstract($projectDescriptor, $filename)
    {
        $this->projectDescriptor = $projectDescriptor;
        $this->filename          = $filename;

        $abstractMethod    = $this->getMethodWithName('\Luigi\Pizza\PizzaComponentFactory', 'calculatePrice');
        $nonAbstractMethod = $this->getMethodWithName('\Luigi\Pizza', '__construct');

        $this->assertTrue($abstractMethod->isAbstract());
        $this->assertFalse($nonAbstractMethod->isAbstract());
    }

    /**
     * @dataProvider switchBetweenStrategies
     */
    public function testMethodCanBePrivate($projectDescriptor, $filename)
    {
        $this->projectDescriptor = $projectDescriptor;
        $this->filename          = $filename;

        $method = $this->getMethodWithName('\Luigi\Pizza', '__construct');

        $this->assertSame('private', $method->getVisibility());
    }

    /**
     * @dataProvider switchBetweenStrategies
     */
    public function testMethodCanBeProtected($projectDescriptor, $filename)
    {
        $this->projectDescriptor = $projectDescriptor;
        $this->filename          = $filename;

        $method = $this->getMethodWithName('\Luigi\Pizza\PizzaComponentFactory', 'calculatePrice');

        $this->assertSame('protected', $method->getVisibility());
    }

    /**
     * @dataProvider switchBetweenStrategies
     */
    public function testMethodCanBePublic($projectDescriptor, $filename)
    {
        $this->projectDescriptor = $projectDescriptor;
        $this->filename          = $filename;

        $method = $this->getMethodWithName('\Luigi\Pizza', 'createInstance');

        $this->assertSame('public', $method->getVisibility());
    }

    /**
     * @dataProvider switchBetweenStrategies
     */
    public function testMethodIsPublicWhenDefinedWithoutVisibility($projectDescriptor, $filename)
    {
        $this->projectDescriptor = $projectDescriptor;
        $this->filename          = $filename;

        $method = $this->getMethodWithName('\Luigi\Pizza', 'getInstance');

        $this->assertSame('public', $method->getVisibility());
    }
}
