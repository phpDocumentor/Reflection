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
use phpDocumentor\Descriptor\Analyzer;

use phpDocumentor\Reflection\ClassReflector\ConstantReflector as ClassConstant;
use phpDocumentor\Reflection\ClassReflector;
use phpDocumentor\Reflection\ConstantReflector;
use phpDocumentor\Reflection\DocBlock\Tag\AuthorTag;
use phpDocumentor\Reflection\DocBlock\Tag\DeprecatedTag;
use phpDocumentor\Reflection\DocBlock\Tag\ExampleTag;
use phpDocumentor\Reflection\DocBlock\Tag\LinkTag;
use phpDocumentor\Reflection\DocBlock\Tag\MethodTag;
use phpDocumentor\Reflection\DocBlock\Tag\ParamTag;
use phpDocumentor\Reflection\DocBlock\Tag\PropertyTag;
use phpDocumentor\Reflection\DocBlock\Tag\ReturnTag;
use phpDocumentor\Reflection\DocBlock\Tag\SeeTag;
use phpDocumentor\Reflection\DocBlock\Tag\SinceTag;
use phpDocumentor\Reflection\DocBlock\Tag\ThrowsTag;
use phpDocumentor\Reflection\DocBlock\Tag\UsesTag;
use phpDocumentor\Reflection\DocBlock\Tag\VarTag;
use phpDocumentor\Reflection\DocBlock\Tag;
use phpDocumentor\Reflection\DocBlock\Type\Collection as TypeCollection;
use phpDocumentor\Reflection\FileReflector;
use phpDocumentor\Reflection\FunctionReflector;
use phpDocumentor\Reflection\InterfaceReflector;
use phpDocumentor\Reflection\TraitReflector;

use phpDocumentor\Descriptor\Builder\Reflector\ArgumentAssembler;
use phpDocumentor\Descriptor\Builder\Reflector\ClassAssembler;
use phpDocumentor\Descriptor\Builder\Reflector\ConstantAssembler;
use phpDocumentor\Descriptor\Builder\Reflector\FileAssembler;
use phpDocumentor\Descriptor\Builder\Reflector\FunctionAssembler;
use phpDocumentor\Descriptor\Builder\Reflector\InterfaceAssembler;
use phpDocumentor\Descriptor\Builder\Reflector\MethodAssembler;
use phpDocumentor\Descriptor\Builder\Reflector\PropertyAssembler;
use phpDocumentor\Descriptor\Builder\Reflector\Tags\AuthorAssembler;
use phpDocumentor\Descriptor\Builder\Reflector\Tags\DeprecatedAssembler;
use phpDocumentor\Descriptor\Builder\Reflector\Tags\ExampleAssembler;
use phpDocumentor\Descriptor\Builder\Reflector\Tags\GenericTagAssembler;
use phpDocumentor\Descriptor\Builder\Reflector\Tags\LinkAssembler;
use phpDocumentor\Descriptor\Builder\Reflector\Tags\MethodAssembler as MethodTagAssembler;
use phpDocumentor\Descriptor\Builder\Reflector\Tags\ParamAssembler;
use phpDocumentor\Descriptor\Builder\Reflector\Tags\PropertyAssembler as PropertyTagAssembler;
use phpDocumentor\Descriptor\Builder\Reflector\Tags\ReturnAssembler;
use phpDocumentor\Descriptor\Builder\Reflector\Tags\SeeAssembler;
use phpDocumentor\Descriptor\Builder\Reflector\Tags\SinceAssembler;
use phpDocumentor\Descriptor\Builder\Reflector\Tags\ThrowsAssembler;
use phpDocumentor\Descriptor\Builder\Reflector\Tags\TypeCollectionAssembler;
use phpDocumentor\Descriptor\Builder\Reflector\Tags\UsesAssembler;
use phpDocumentor\Descriptor\Builder\Reflector\Tags\VarAssembler;
use phpDocumentor\Descriptor\Builder\Reflector\Tags\VersionAssembler;
use phpDocumentor\Descriptor\Builder\Reflector\TraitAssembler;

class ReflectionAssemblers
{
    /** @var Finder */
    private $exampleFinder;

    public function __construct(Finder $exampleFinder)
    {
        $this->exampleFinder = $exampleFinder;
    }

    public function __invoke(Analyzer $analyzer)
    {
        $factory = $analyzer->getAssemblerFactory();

        // @codingStandardsIgnoreStart because we limit the verbosity by making all closures single-line
        $fileMatcher      = function ($criteria) { return $criteria instanceof FileReflector; };
        $constantMatcher  = function ($criteria) {
            return $criteria instanceof ConstantReflector || $criteria instanceof ClassConstant;
        };
        $traitMatcher     = function ($criteria) { return $criteria instanceof TraitReflector; };
        $classMatcher     = function ($criteria) { return $criteria instanceof ClassReflector; };
        $interfaceMatcher = function ($criteria) { return $criteria instanceof InterfaceReflector; };
        $propertyMatcher  = function ($criteria) { return $criteria instanceof ClassReflector\PropertyReflector; };
        $methodMatcher    = function ($criteria) { return $criteria instanceof ClassReflector\MethodReflector; };
        $argumentMatcher  = function ($criteria) { return $criteria instanceof FunctionReflector\ArgumentReflector; };
        $functionMatcher  = function ($criteria) { return $criteria instanceof FunctionReflector; };

        $authorMatcher      = function ($criteria) { return $criteria instanceof AuthorTag; };
        $deprecatedMatcher  = function ($criteria) { return $criteria instanceof DeprecatedTag; };
        $exampleMatcher     = function ($criteria) { return $criteria instanceof ExampleTag; };
        $linkMatcher        = function ($criteria) { return $criteria instanceof LinkTag; };
        $methodTagMatcher   = function ($criteria) { return $criteria instanceof MethodTag; };
        $propertyTagMatcher = function ($criteria) { return $criteria instanceof PropertyTag; };
        $paramMatcher       = function ($criteria) { return $criteria instanceof ParamTag; };
        $throwsMatcher      = function ($criteria) { return $criteria instanceof ThrowsTag; };
        $returnMatcher      = function ($criteria) { return $criteria instanceof ReturnTag; };
        $usesMatcher        = function ($criteria) { return $criteria instanceof UsesTag; };
        $seeMatcher         = function ($criteria) { return $criteria instanceof SeeTag; };
        $sinceMatcher       = function ($criteria) { return $criteria instanceof SinceTag; };
        $varMatcher         = function ($criteria) { return $criteria instanceof VarTag; };
        $versionMatcher     = function ($criteria) { return $criteria instanceof Tag\VersionTag; };

        $typeCollectionMatcher = function ($criteria) { return $criteria instanceof TypeCollection; };

        $tagFallbackMatcher = function ($criteria) { return $criteria instanceof Tag; };
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

        $factory->register($authorMatcher, new AuthorAssembler());
        $factory->register($deprecatedMatcher, new DeprecatedAssembler());
        $factory->register($exampleMatcher, new ExampleAssembler($this->exampleFinder));
        $factory->register($linkMatcher, new LinkAssembler());
        $factory->register($methodTagMatcher, new MethodTagAssembler());
        $factory->register($propertyTagMatcher, new PropertyTagAssembler());
        $factory->register($varMatcher, new VarAssembler());
        $factory->register($paramMatcher, new ParamAssembler());
        $factory->register($throwsMatcher, new ThrowsAssembler());
        $factory->register($returnMatcher, new ReturnAssembler());
        $factory->register($usesMatcher, new UsesAssembler());
        $factory->register($seeMatcher, new SeeAssembler());
        $factory->register($sinceMatcher, new SinceAssembler());
        $factory->register($versionMatcher, new VersionAssembler());

        $factory->register($typeCollectionMatcher, new TypeCollectionAssembler());

        $factory->registerFallback($tagFallbackMatcher, new GenericTagAssembler());
    }

} 