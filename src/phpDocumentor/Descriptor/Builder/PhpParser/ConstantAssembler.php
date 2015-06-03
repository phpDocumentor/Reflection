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

use phpDocumentor\Descriptor\Constant;
use phpDocumentor\Descriptor\Tag\VarDescriptor;
use phpDocumentor\Reflection\ConstantReflector;
use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\Fqsen;
use PhpParser\Node\Const_;
use PhpParser\Node\Name;
use PhpParser\Node;

/**
 * Assembles a Constant from a ConstantReflector.
 */
class ConstantAssembler extends AssemblerAbstract
{
    /**
     * Creates a Descriptor from the provided data.
     *
     * @param Const_ $data
     *
     * @return Constant
     */
    public function create($data)
    {
        $constantDescriptor = new Constant(new Fqsen(isset($data->namespacedName)
            ? '\\' . $data->namespacedName->toString()
            : $data->name), $data->docBlock, $this->getRepresentationOfValue($data->value));

        return $constantDescriptor;
    }
}
