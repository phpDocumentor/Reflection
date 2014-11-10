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

class CreateFileTest extends BaseComponentTestCase
{
    /**
     * @dataProvider switchBetweenStrategies
     */
    public function testFileHasName($projectDescriptor, $filename)
    {
        $this->projectDescriptor = $projectDescriptor;
        $this->filename          = $filename;

        $file = $this->getFileDescriptor();

        $this->assertFullyQualifiedStructuralElementName($file, '');
        $this->assertName($file, 'example.file.php');
    }

    /**
     * @dataProvider switchBetweenStrategies
     */
    public function testFileHasPath($projectDescriptor, $filename)
    {
        $this->projectDescriptor = $projectDescriptor;
        $this->filename          = $filename;

        $file = $this->getFileDescriptor();

        $this->assertSame($this->filename, $file->getPath());
    }

    /**
     * @dataProvider switchBetweenStrategies
     */
    public function testFileHasSourceCode($projectDescriptor, $filename)
    {
        $this->projectDescriptor = $projectDescriptor;
        $this->filename          = $filename;

        $file = $this->getFileDescriptor();

        $this->assertSame(file_get_contents($this->filename), $file->getSource());
    }

    /**
     * @dataProvider switchBetweenStrategies
     */
    public function testFileHasSummary($projectDescriptor, $filename)
    {
        $this->projectDescriptor = $projectDescriptor;
        $this->filename          = $filename;

        $file = $this->getFileDescriptor();

        $this->assertSummary($file, 'Summary of the File DocBlock.');
    }

    /**
     * @dataProvider switchBetweenStrategies
     */
    public function testFileHasDescription($projectDescriptor, $filename)
    {
        $this->projectDescriptor = $projectDescriptor;
        $this->filename          = $filename;

        $file = $this->getFileDescriptor();

        $this->assertDescription($file, 'Description of the File.');
    }

    /**
     * @dataProvider switchBetweenStrategies
     */
    public function testFileHasPackage($projectDescriptor, $filename)
    {
        $this->projectDescriptor = $projectDescriptor;
        $this->filename          = $filename;

        $file = $this->getFileDescriptor();

        $this->assertPackageName($file, 'Luigi\Pizza');
    }

    /**
     * @dataProvider switchBetweenStrategies
     */
    public function testFileHasAuthorTag($projectDescriptor, $filename)
    {
        $this->projectDescriptor = $projectDescriptor;
        $this->filename          = $filename;

        $file = $this->getFileDescriptor();

        $this->assertSame('Mike van Riel <me@mikevanriel.com>', $file->getAuthor()->get(0)->getDescription());
    }

    /**
     * @dataProvider switchBetweenStrategies
     */
    public function testFileHasAllConstants($projectDescriptor, $filename)
    {
        $this->projectDescriptor = $projectDescriptor;
        $this->filename          = $filename;

        $file = $this->getFileDescriptor();

        $this->assertFileHasConstant($file, '\Luigi\Pizza\VAT_HIGH');
        $this->assertFileHasConstant($file, '\Luigi\Pizza\VAT_LOW');
        $this->assertFileHasConstant($file, '\Luigi\Pizza\SIZE_5CM');
        $this->assertFileHasConstant($file, '\Luigi\Pizza\SIZE_10CM');
        $this->assertFileHasConstant($file, '\Luigi\Pizza\SIZE_15CM');
        $this->assertFileHasConstant($file, '\Luigi\Pizza\SIZE_20CM');
        $this->assertFileHasConstant($file, '\Luigi\Pizza\DEFAULT_SIZE');
    }

    /**
     * @dataProvider switchBetweenStrategies
     */
    public function testFileHasAllInterfaces($projectDescriptor, $filename)
    {
        $this->projectDescriptor = $projectDescriptor;
        $this->filename          = $filename;

        $file = $this->getFileDescriptor();

        $this->assertFileHasInterface($file, '\Luigi\Pizza\Valued');
        $this->assertFileHasInterface($file, '\Luigi\Pizza\Style');
        $this->assertFileHasInterface($file, '\Luigi\Pizza\Sauce');
        $this->assertFileHasInterface($file, '\Luigi\Pizza\Topping');
    }

    /**
     * @dataProvider switchBetweenStrategies
     */
    public function testFileHasAllTraits($projectDescriptor, $filename)
    {
        $this->projectDescriptor = $projectDescriptor;
        $this->filename          = $filename;

        $file = $this->getFileDescriptor();

        $this->assertFileHasTrait($file, '\Luigi\Pizza\HasPrice');
    }

    /**
     * @dataProvider switchBetweenStrategies
     */
    public function testFileHasAllClassDefinitions($projectDescriptor, $filename)
    {
        $this->projectDescriptor = $projectDescriptor;
        $this->filename          = $filename;

        $file = $this->getFileDescriptor();

        $this->assertFileHasClass($file, '\Luigi\Pizza');
        $this->assertFileHasClass($file, '\Luigi\Pizza\PizzaComponentFactory');
        $this->assertFileHasClass($file, '\Luigi\Pizza\StyleFactory');
        $this->assertFileHasClass($file, '\Luigi\Pizza\SauceFactory');
        $this->assertFileHasClass($file, '\Luigi\Pizza\ToppingFactory');
        $this->assertFileHasClass($file, '\Luigi\Pizza\ItalianStyle');
        $this->assertFileHasClass($file, '\Luigi\Pizza\AmericanStyle');
        $this->assertFileHasClass($file, '\Luigi\Pizza\TomatoSauce');
        $this->assertFileHasClass($file, '\Luigi\Pizza\CheeseTopping');
    }
}
