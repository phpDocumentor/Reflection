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

use phpDocumentor\Descriptor\Argument;
use phpDocumentor\Reflection\DocBlock\Type\Collection;
use phpDocumentor\Descriptor\Tag\ParamDescriptor;
use phpDocumentor\Reflection\FunctionReflector\ArgumentReflector;
use PhpParser\Node\Name;
use PhpParser\Node\Param;

/**
 * Assembles an Argument using an ArgumentReflector and ParamDescriptors.
 */
class ArgumentAssembler extends AssemblerAbstract
{
    /**
     * Creates a Descriptor from the provided data.
     *
     * @param Param|string|null $data
     * @param ParamDescriptor[] $params
     *
     * @return Argument
     */
    public function create($data, $params = array())
    {
        $argumentDescriptor = new Argument();
        $argumentDescriptor->setName('$' . $data->name);
        $argumentDescriptor->setTypes(
            $this->analyzer->analyze(
                $data->type
                ? new Collection(array($data->type instanceof Name ? $data->type->toString() : $data->type))
                : new Collection()
            )
        );

        foreach ($params as $paramDescriptor) {
            $this->overwriteTypeAndDescriptionFromParamTag($data, $paramDescriptor, $argumentDescriptor);
        }

        $argumentDescriptor->setDefault($data->default);
        $argumentDescriptor->setByReference($data->byRef);

        return $argumentDescriptor;
    }

    /**
     * Overwrites the type and description in the Argument Descriptor with that from the tag if the names match.
     *
     * @param Param              $argument
     * @param ParamDescriptor    $paramDescriptor
     * @param Argument $argumentDescriptor
     *
     * @return void
     */
    protected function overwriteTypeAndDescriptionFromParamTag(
        Param              $argument,
        ParamDescriptor    $paramDescriptor,
        Argument $argumentDescriptor
    ) {
        if ($paramDescriptor->getVariableName() != '$' . $argument->name) {
            return;
        }

        $argumentDescriptor->setDescription($paramDescriptor->getDescription());
        $argumentDescriptor->setTypes(
            $paramDescriptor->getTypes() ?: $this->analyzer->analyze(
                new Collection(array($argument->getType() ?: 'mixed'))
            )
        );
    }
}
