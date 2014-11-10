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

use phpDocumentor\Descriptor\ConstantDescriptor;
use phpDocumentor\Descriptor\Tag\VarDescriptor;
use phpDocumentor\Reflection\ConstantReflector;
use phpDocumentor\Reflection\DocBlock;
use PhpParser\Node\Const_;
use PhpParser\Node\Name;
use PhpParser\Node;

/**
 * Assembles a ConstantDescriptor from a ConstantReflector.
 */
class ConstantAssembler extends AssemblerAbstract
{
    /**
     * Creates a Descriptor from the provided data.
     *
     * @param Const_ $data
     *
     * @return ConstantDescriptor
     */
    public function create($data)
    {
        $constantDescriptor = new ConstantDescriptor();
        $this->assembleDocBlock($data->docBlock, $constantDescriptor);
        $value = $this->getRepresentationOfValue($data->value);
        $constantDescriptor->setName((string)$data->name);
        $constantDescriptor->setValue($value);
        $constantDescriptor->setNamespace('\\' . $this->extractNamespace($data));
        $constantDescriptor->setFullyQualifiedStructuralElementName(
            isset($data->namespacedName)
                ? '\\' . $data->namespacedName->toString()
                : $data->name
        );
        $constantDescriptor->setLine($data->getLine());

        if ($constantDescriptor->getSummary() === '') {
            $this->extractSummaryAndDescriptionFromVarTag($constantDescriptor);
        }

        return $constantDescriptor;
    }

    /**
     * @param ConstantDescriptor $constantDescriptor
     */
    private function extractSummaryAndDescriptionFromVarTag($constantDescriptor)
    {
        /** @var VarDescriptor $var */
        foreach ($constantDescriptor->getVar() as $var) {
            // check if the first part of the description matches the constant name; an additional character is
            // extracted to see if it is followed by a space.
            $name = substr($var->getDescription(), 0, strlen($constantDescriptor->getName()) + 1);

            if ($name === $constantDescriptor->getName() . ' ') {
                $docBlock = new DocBlock(substr($var->getDescription(), strlen($constantDescriptor->getName()) + 1));
                $constantDescriptor->setSummary($docBlock->getShortDescription());
                $constantDescriptor->setDescription($docBlock->getLongDescription());
                break;
            }
        }
    }
}
