<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2014 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Descriptor\Builder\PhpParser;

use phpDocumentor\Descriptor\Property;
use phpDocumentor\Descriptor\Tag\VarDescriptor;
use phpDocumentor\Reflection\ClassReflector\PropertyReflector;
use phpDocumentor\Reflection\DocBlock;
use PhpParser\Node\Stmt\PropertyProperty;

/**
 * Assembles a Property from a PropertyReflector.
 */
class PropertyAssembler extends AssemblerAbstract
{
    /**
     * Creates a Descriptor from the provided data.
     *
     * @param PropertyProperty $data
     *
     * @return Property
     */
    public function create($data)
    {
        $propertyDescriptor = new Property();
        $this->assembleDocBlock($data->docBlock, $propertyDescriptor);

        $propertyDescriptor->setFullyQualifiedStructuralElementName($data->name);
        $propertyDescriptor->setName($data->name);
        $propertyDescriptor->setVisibility($data->visibility);

        $propertyDescriptor->setStatic($data->static);
        $propertyDescriptor->setDefault($this->getRepresentationOfValue($data->default));

        $propertyDescriptor->setLine($data->getLine());

        if ($propertyDescriptor->getSummary() === '') {
            $this->extractSummaryAndDescriptionFromVarTag($propertyDescriptor);
        }

        return $propertyDescriptor;
    }


    /**
     * @param Property $propertyDescriptor
     *
     * @return void
     */
    private function extractSummaryAndDescriptionFromVarTag($propertyDescriptor)
    {
        /** @var VarDescriptor $var */
        foreach ($propertyDescriptor->getVar() as $var) {
            if ($this->varTagHasMatchingVariableWithActiveDescription($propertyDescriptor, $var)) {
                $this->overwriteSummaryAndDescriptionWithDocComment($propertyDescriptor, $var->getDescription());
                break;
            }
        }
    }

    /**
     * @param Property  $propertyDescriptor
     * @param DocBlock\Tag\VarTag $var
     *
     * @return bool
     */
    private function varTagHasMatchingVariableWithActiveDescription($propertyDescriptor, $var)
    {
        return (!$var->getVariableName() || $this->varTagNameMatchesPropertyDescriptorName($propertyDescriptor, $var))
            && $var->getDescription();
    }

    /**
     * @param Property  $propertyDescriptor
     * @param DocBlock\Tag\VarTag $var
     *
     * @return bool
     */
    private function varTagNameMatchesPropertyDescriptorName($propertyDescriptor, $var)
    {
        return $var->getVariableName() === '$' . $propertyDescriptor->getName();
    }

    /**
     * @param Property $propertyDescriptor
     * @param string             $docblock
     *
     * @return void
     */
    private function overwriteSummaryAndDescriptionWithDocComment($propertyDescriptor, $docblock)
    {
        $docBlock = new DocBlock($docblock);
        $propertyDescriptor->setSummary($docBlock->getShortDescription());
        $propertyDescriptor->setDescription($docBlock->getLongDescription());
    }
}
