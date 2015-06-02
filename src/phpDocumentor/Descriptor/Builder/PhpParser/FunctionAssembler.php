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
use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Descriptor\FunctionDescriptor;
use phpDocumentor\Reflection\FunctionReflector;
use PhpParser\Node;
use PhpParser\Node\Param;
use PhpParser\Node\Stmt\Function_;

/**
 * Assembles a FunctionDescriptor from a FunctionReflector.
 */
class FunctionAssembler extends AssemblerAbstract
{
    /** @var ArgumentAssembler */
    protected $argumentAssembler;

    /**
     * Initializes this assembler and its dependencies.
     */
    public function __construct(ArgumentAssembler $argumentAssembler)
    {
        $this->argumentAssembler = $argumentAssembler;
    }

    /**
     * Creates a Descriptor from the provided data.
     *
     * @param Function_ $data
     *
     * @return FunctionDescriptor
     */
    public function create($data)
    {
        $functionDescriptor = new FunctionDescriptor();

        $this->mapPropertiesOntoDescriptor($data, $functionDescriptor);
        $this->assembleDocBlock($data->getDocComment(), $functionDescriptor);
        $this->addArgumentsToFunctionDescriptor($data->params, $functionDescriptor);

        return $functionDescriptor;
    }

    /**
     * Maps the properties of the Function reflector onto the Descriptor.
     *
     * @param Function_          $node
     * @param FunctionDescriptor $descriptor
     *
     * @return void
     */
    protected function mapPropertiesOntoDescriptor($node, $descriptor)
    {
        $packages = new Collection();
//        $package = $this->extractPackageFromDocBlock($node->getDocComment());
//        if ($package) {
//            $tag = new TagDescriptor('package');
//            $tag->setDescription($package);
//            $packages->add($tag);
//        }
//        $descriptor->getTags()->set('package', $packages);

        $descriptor->setFullyQualifiedStructuralElementName($node->name . '()');
        $descriptor->setName((string)$node->name . '()');
        $descriptor->setLine($node->getLine());
        $descriptor->setNamespace('\\' . $this->extractNamespace($node));
        $descriptor->setFullyQualifiedStructuralElementName('\\' . $node->namespacedName->toString() . '()');
    }

    /**
     * Converts each argument to an argument descriptor and adds it to the function descriptor.
     *
     * @param Param[]            $arguments
     * @param FunctionDescriptor $functionDescriptor
     *
     * @return void
     */
    protected function addArgumentsToFunctionDescriptor(array $arguments, $functionDescriptor)
    {
        foreach ($arguments as $argument) {
            $this->addArgumentDescriptorToFunction(
                $functionDescriptor,
                $this->createArgumentDescriptor($functionDescriptor, $argument)
            );
        }
    }

    /**
     * Adds the given argument to the function.
     *
     * @param FunctionDescriptor $functionDescriptor
     * @param Argument $argumentDescriptor
     *
     * @return void
     */
    protected function addArgumentDescriptorToFunction($functionDescriptor, $argumentDescriptor)
    {
        $functionDescriptor->getArguments()->set($argumentDescriptor->getName(), $argumentDescriptor);
    }

    /**
     * Creates a new Argument from the given Reflector and Param.
     *
     * @param FunctionDescriptor $functionDescriptor
     * @param Param              $argument
     *
     * @return Argument
     */
    protected function createArgumentDescriptor($functionDescriptor, $argument)
    {
        $params = $functionDescriptor->getTags()->get('param', array());

        if (!$this->argumentAssembler->getAnalyzer()) {
            $this->argumentAssembler->setAnalyzer($this->analyzer);
        }

        return $this->argumentAssembler->create($argument, $params);
    }
}
