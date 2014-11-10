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

use phpDocumentor\Descriptor\ArgumentDescriptor;
use phpDocumentor\Reflection\DocBlock\Type\Collection;
use phpDocumentor\Descriptor\MethodDescriptor;
use phpDocumentor\Reflection\ClassReflector\MethodReflector;
use phpDocumentor\Reflection\DocBlock\Tag\ParamTag;
use phpDocumentor\Reflection\FunctionReflector\ArgumentReflector;
use PhpParser\Node\Param;
use PhpParser\Node\Stmt\ClassMethod;

/**
 * Assembles a MethodDescriptor from a PHP-Parser ClassMethod.
 */
class MethodAssembler extends AssemblerAbstract
{
    /** @var ArgumentAssembler */
    protected $argumentAssembler;

    /**
     * Initializes this assembler with its dependencies.
     *
     * @param ArgumentAssembler $argumentAssembler
     */
    public function __construct(ArgumentAssembler $argumentAssembler)
    {
        $this->argumentAssembler = $argumentAssembler;
    }

    /**
     * Creates a Descriptor from the provided data.
     *
     * @param ClassMethod $data
     *
     * @return MethodDescriptor
     */
    public function create($data)
    {
        $methodDescriptor = new MethodDescriptor();
        $this->assembleDocBlock($data->docBlock, $methodDescriptor);

        $this->mapNodeToDescriptor($data, $methodDescriptor);

        $this->addArguments($data, $methodDescriptor);
//        $this->addVariadicArgument($data, $methodDescriptor);

        return $methodDescriptor;
    }

    /**
     * Maps the fields to the reflector to the descriptor.
     *
     * @param ClassMethod      $node
     * @param MethodDescriptor $descriptor
     *
     * @return void
     */
    protected function mapNodeToDescriptor($node, $descriptor)
    {
        $descriptor->setFullyQualifiedStructuralElementName($node->name . '()');
        $descriptor->setName($node->name);
        $descriptor->setLine($node->getLine());

        if ($node->isPrivate()) {
            $descriptor->setVisibility('private');
        } elseif ($node->isProtected()) {
            $descriptor->setVisibility('protected');
        } else {
            $descriptor->setVisibility('public');
        }

        $descriptor->setFinal($node->isFinal());
        $descriptor->setAbstract($node->isAbstract());
        $descriptor->setStatic($node->isStatic());
    }

    /**
     * Adds the reflected Arguments to the Descriptor.
     *
     * @param ClassMethod      $data
     * @param MethodDescriptor $descriptor
     *
     * @return void
     */
    protected function addArguments(ClassMethod $data, $descriptor)
    {
        foreach ($data->params as $argument) {
            $this->addArgument($argument, $descriptor);
        }
    }

    /**
     * Adds a single reflected Argument to the Method Descriptor.
     *
     * @param Param             $argument
     * @param MethodDescriptor  $descriptor
     *
     * @return void
     */
    protected function addArgument(Param $argument, MethodDescriptor $descriptor)
    {
        $params = $descriptor->getTags()->get('param', array());

        if (!$this->argumentAssembler->getAnalyzer()) {
            $this->argumentAssembler->setAnalyzer($this->analyzer);
        }
        $argumentDescriptor = $this->argumentAssembler->create($argument, $params);

        $descriptor->getArguments()->set($argumentDescriptor->getName(), $argumentDescriptor);
    }

    /**
     * Checks if there is a variadic argument in the `@param` tags and adds it to the list of Arguments in
     * the Descriptor unless there is already one present.
     *
     * @param MethodReflector  $data
     * @param MethodDescriptor $methodDescriptor
     *
     * @return void
     */
    protected function addVariadicArgument($data, $methodDescriptor)
    {
        if (!$data->getDocBlock()) {
            return;
        }

        $paramTags = $data->getDocBlock()->getTagsByName('param');

        /** @var ParamTag $lastParamTag */
        $lastParamTag = end($paramTags);
        if (!$lastParamTag) {
            return;
        }

        if ($lastParamTag->isVariadic()
            && !in_array($lastParamTag->getVariableName(), array_keys($methodDescriptor->getArguments()->getAll()))
        ) {
            $types = $this->analyzer->analyze(new Collection($lastParamTag->getTypes()));

            $argument = new ArgumentDescriptor();
            $argument->setName($lastParamTag->getVariableName());
            $argument->setTypes($types);
            $argument->setDescription($lastParamTag->getDescription());
            $argument->setLine($methodDescriptor->getLine());
            $argument->setVariadic(true);

            $methodDescriptor->getArguments()->set($argument->getName(), $argument);
        }
    }
}
