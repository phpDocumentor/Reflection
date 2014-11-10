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

namespace phpDocumentor\Descriptor\TransformFileTo;

use phpDocumentor\Descriptor\FileDescriptor;
use phpDocumentor\Descriptor\BaseComponentTestCase;
use phpDocumentor\Descriptor\Analyzer;

class CreateTraitTest extends BaseComponentTestCase
{
    /**
     * @dataProvider switchBetweenStrategies
     */
    public function testTraitHasName($projectDescriptor, $filename)
    {
        $this->projectDescriptor = $projectDescriptor;
        $this->filename          = $filename;

        $trait = $this->fetchTrait();

        $this->assertName($trait, 'HasPrice');
        $this->assertFullyQualifiedStructuralElementName($trait, '\Luigi\Pizza\HasPrice');
    }

    /**
     * @dataProvider switchBetweenStrategies
     */
    public function testTraitHasNamespace($projectDescriptor, $filename)
    {
        $this->projectDescriptor = $projectDescriptor;
        $this->filename          = $filename;

        $trait = $this->fetchTrait();

        $this->assertNamespace($trait, '\Luigi\Pizza');
    }

    /**
     * @dataProvider switchBetweenStrategies
     */
    public function testTraitHasSummary($projectDescriptor, $filename)
    {
        $this->projectDescriptor = $projectDescriptor;
        $this->filename          = $filename;

        $trait = $this->fetchTrait();

        $this->assertSummary($trait, 'A single item with a value');
    }

    /**
     * @dataProvider switchBetweenStrategies
     */
    public function testTraitHasDescription($projectDescriptor, $filename)
    {
        $this->projectDescriptor = $projectDescriptor;
        $this->filename          = $filename;

        $trait = $this->fetchTrait();

        $this->assertDescription($trait, 'Represents something with a price.');
    }

    /**
     * @dataProvider switchBetweenStrategies
     */
    public function testTraitHasPackage($projectDescriptor, $filename)
    {
        $this->projectDescriptor = $projectDescriptor;
        $this->filename          = $filename;

        $trait = $this->fetchTrait();

        $this->assertPackageName($trait, 'Luigi\Pizza');
    }

    /**
     * @dataProvider switchBetweenStrategies
     */
    public function testTraitHasAllMethods($projectDescriptor, $filename)
    {
        $this->projectDescriptor = $projectDescriptor;
        $this->filename          = $filename;

        $trait = $this->fetchTrait();

        $this->assertHasMethod($trait, 'getPrice', '\Luigi\Pizza\HasPrice::getPrice()');
    }

    /**
     * @dataProvider switchBetweenStrategies
     */
    public function testTraitHasAllProperties($projectDescriptor, $filename)
    {
        $this->projectDescriptor = $projectDescriptor;
        $this->filename          = $filename;

        $trait = $this->fetchTrait();

        $this->assertHasProperty($trait, 'temporaryPrice', '\Luigi\Pizza\HasPrice::temporaryPrice');
    }

    /**
     * @dataProvider switchBetweenStrategies
     */
    public function testTraitUsesOtherTraits($projectDescriptor, $filename)
    {
        $this->projectDescriptor = $projectDescriptor;
        $this->filename          = $filename;

        $trait = $this->fetchTrait();

        $this->assertGreaterThan(0, $trait->getUsedTraits()->count());
        $this->assertSame('\Luigi\Pizza\ExampleNestedTrait', $trait->getUsedTraits()->get(0));
    }

    /**
     * @dataProvider switchBetweenStrategies
     */
    public function testTraitHasPropertyWithPrivateVisibility($projectDescriptor, $filename)
    {
        $this->projectDescriptor = $projectDescriptor;
        $this->filename          = $filename;

        $trait = $this->fetchTrait();

        $this->assertHasProperty($trait, 'temporaryPrice', '\Luigi\Pizza\HasPrice::temporaryPrice');
        $this->assertSame('private', $trait->getProperties()->get('temporaryPrice')->getVisibility());
    }
}
