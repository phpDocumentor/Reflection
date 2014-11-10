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

namespace phpDocumentor\Descriptor\ProjectDescriptor\InitializerCommand;

use phpDocumentor\Descriptor\Example\Finder;
use phpDocumentor\Descriptor\ProjectDescriptorBuilder;

use phpDocumentor\Descriptor\Builder\PhpParser\ArgumentAssembler;
use phpDocumentor\Descriptor\Builder\PhpParser\ClassAssembler;
use phpDocumentor\Descriptor\Builder\PhpParser\ConstantAssembler;
use phpDocumentor\Descriptor\Builder\PhpParser\FileAssembler;
use phpDocumentor\Descriptor\Builder\PhpParser\FunctionAssembler;
use phpDocumentor\Descriptor\Builder\PhpParser\InterfaceAssembler;
use phpDocumentor\Descriptor\Builder\PhpParser\MethodAssembler;
use phpDocumentor\Descriptor\Builder\PhpParser\PropertyAssembler;
use phpDocumentor\Descriptor\Builder\PhpParser\TraitAssembler;

use PhpParser\Node\Const_;
use PhpParser\Node\Param;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassConst;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Function_;
use PhpParser\Node\Stmt\Interface_;
use PhpParser\Node\Stmt\PropertyProperty;
use PhpParser\Node\Stmt\Trait_;

class PhpParserAssemblers
{
    /** @var Finder */
    private $exampleFinder;

    public function __construct(Finder $exampleFinder)
    {
        $this->exampleFinder = $exampleFinder;
    }

    public function __invoke(ProjectDescriptorBuilder $projectDescriptorBuilder)
    {
        $factory = $projectDescriptorBuilder->getAssemblerFactory();

        // @codingStandardsIgnoreStart because we limit the verbosity by making all closures single-line
        $fileMatcher      = function ($criteria) { return $criteria instanceof \SplFileObject; };
        $constantMatcher  = function ($criteria) {
            return $criteria instanceof Const_ || $criteria instanceof ClassConst;
        };
        $traitMatcher     = function ($criteria) { return $criteria instanceof Trait_; };
        $classMatcher     = function ($criteria) { return $criteria instanceof Class_; };
        $interfaceMatcher = function ($criteria) { return $criteria instanceof Interface_; };
        $propertyMatcher  = function ($criteria) { return $criteria instanceof PropertyProperty; };
        $methodMatcher    = function ($criteria) { return $criteria instanceof ClassMethod; };
        $argumentMatcher  = function ($criteria) { return $criteria instanceof Param; };
        $functionMatcher  = function ($criteria) { return $criteria instanceof Function_; };
        // @codingStandardsIgnoreEnd

        $argumentAssembler = new ArgumentAssembler();
        $factory->register($fileMatcher, new FileAssembler());
        $factory->register($constantMatcher, new ConstantAssembler());
        $factory->register($traitMatcher, new TraitAssembler());
        $factory->register($classMatcher, new ClassAssembler());
        $factory->register($interfaceMatcher, new InterfaceAssembler());
        $factory->register($propertyMatcher, new PropertyAssembler());
        $factory->register($argumentMatcher, $argumentAssembler);
        $factory->register($methodMatcher, new MethodAssembler($argumentAssembler));
        $factory->register($functionMatcher, new FunctionAssembler($argumentAssembler));
    }
}
