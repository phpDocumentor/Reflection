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

use phpDocumentor\Descriptor\Builder\AssemblerAbstract as BaseAssembler;
use phpDocumentor\Descriptor\ClassDescriptor;
use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Descriptor\Constant;
use phpDocumentor\Descriptor\DescriptorAbstract;
use phpDocumentor\Descriptor\Interface_;
use phpDocumentor\Descriptor\Method;
use phpDocumentor\Descriptor\Property;
use phpDocumentor\Descriptor\Trait_;
use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\PrettyPrinter;
use PhpParser\Node;

abstract class AssemblerAbstract extends BaseAssembler
{
    /** @var PrettyPrinter */
    private static $prettyPrinter;

    /**
     * Assemble DocBlock.
     *
     * @param DocBlock|null      $docBlock
     * @param DescriptorAbstract $target
     *
     * @return void
     */
    protected function assembleDocBlock($docBlock, $target)
    {
        // TODO: Symfony 2 sometimes sends a PhpParser\Node\Doc object instead of a DocBlock or null; there is a bug
        // there that should be investigated
        if (!$docBlock instanceof DocBlock) {
            return;
        }

        $target->setSummary($docBlock->getShortDescription());
        $target->setDescription($docBlock->getLongDescription()->getContents());

        /** @var DocBlock\Tag $tag */
        foreach ($docBlock->getTags() as $tag) {
            $tagDescriptor = $this->analyzer->analyze($tag);

            // allow filtering of tags
            if (!$tagDescriptor) {
                continue;
            }

            $target->getTags()
                ->get($tag->getName(), new Collection())
                ->add($tagDescriptor);
        }
    }

    /**
     * Extracts the namespace from the given node.
     *
     * @param Node $data
     *
     * @return string
     */
    protected function extractNamespace($data)
    {
        if (! isset($data->namespacedName)) {
            return '';
        }

        /** @var Node\Name $namespaceParts */
        $namespaceParts = clone $data->namespacedName;
        unset($namespaceParts->parts[count($namespaceParts->parts) - 1]);

        return $namespaceParts->toString();
    }

    /**
     * @param ClassDescriptor|Interface_ $descriptor
     * @param Node\Stmt\Const_ $constant
     */
    protected function addClassConstantToDescriptor($descriptor, $constant)
    {
        $constant->docBlock = $constant->getDocComment()
            ? new DocBlock($constant->getDocComment()->getText())
            : null;

        /** @var Constant $constantDescriptor */
        $constantDescriptor = $this->getAnalyzer()->analyze($constant);
        if (! $constantDescriptor) {
            return;
        }

        $constantDescriptor->setParent($descriptor);
        $this->inheritPackageFromParentDescriptor($constantDescriptor, $descriptor);
        $descriptor->getConstants()->set($constantDescriptor->getName(), $constantDescriptor);
    }

    /**
     * @param Trait_|Interface_|ClassDescriptor $descriptor
     * @param Node\Stmt\ClassMethod $stmt
     *
     * @return void
     */
    protected function addMethodToDescriptor($descriptor, $stmt)
    {
        $stmt->docBlock = $stmt->getDocComment()
            ? new DocBlock($stmt->getDocComment()->getText())
            : null;

        /** @var Method $methodDescriptor */
        $methodDescriptor = $this->getAnalyzer()->analyze($stmt);
        if (!$methodDescriptor) {
            return;
        }

        $methodDescriptor->setParent($descriptor);
        $this->inheritPackageFromParentDescriptor($methodDescriptor, $descriptor);
        $descriptor->getMethods()->set($methodDescriptor->getName(), $methodDescriptor);
    }

    /**
     * @param Trait_|ClassDescriptor $descriptor
     * @param Node\Stmt\Property $propertyStatement
     * @param Node\Stmt\PropertyProperty $property
     *
     * @todo make this a trait once we go to PHP 5.4
     * @todo rethink the property hacks in this method; how can we solve this in the assembler?
     *
     * @return void
     */
    protected function addPropertyToDescriptor($descriptor, $propertyStatement, $property)
    {
        $property->docBlock = $property->getDocComment()
            ? new DocBlock($property->getDocComment()->getText())
            : null;

        // we cheat; the stmt object is actually a listing but we need a single object to transform so we copy the
        // generic information to the property object
        $property->static = $propertyStatement->isStatic();
        if ($propertyStatement->isPrivate()) {
            $property->visibility = 'private';
        } elseif ($propertyStatement->isProtected()) {
            $property->visibility = 'protected';
        } else {
            $property->visibility = 'public';
        }

        /** @var Property $propertyDescriptor */
        $propertyDescriptor = $this->getAnalyzer()->analyze($property);
        if (! $propertyDescriptor) {
            return;
        }

        $propertyDescriptor->setParent($descriptor);
        $this->inheritPackageFromParentDescriptor($propertyDescriptor, $descriptor);
        $descriptor->getProperties()->set($propertyDescriptor->getName(), $propertyDescriptor);
    }

    /**
     * Registers the used traits with the generated Descriptor.
     *
     * @param ClassDescriptor|Trait_ $descriptor
     * @param Node\Stmt\TraitUse              $trait
     *
     * @return void
     */
    protected function addTraitUsesToDescriptor($descriptor, Node\Stmt\TraitUse $trait)
    {
        foreach ($trait->traits as $use) {
            $descriptor->getUsedTraits()->add('\\' . $use->toString());
        }
    }

    private function inheritPackageFromParentDescriptor(
        DescriptorAbstract $descriptor,
        DescriptorAbstract $parentDescriptor
    ) {
        if (count($descriptor->getTags()->get('package', new Collection())) == 0) {
            $descriptor->getTags()->set(
                'package',
                $parentDescriptor->getTags()->get('package', new Collection())
            );
        }
    }

    /**
     * Returns a simple human readable output for a value.
     *
     * @param Node\Expr $value The value node as provided by PHP-Parser.
     *
     * @return string
     */
    protected function getRepresentationOfValue(Node\Expr $value = null)
    {
        if (null === $value) {
            return '';
        }

        if (!self::$prettyPrinter) {
            self::$prettyPrinter = new PrettyPrinter();
        }

        return self::$prettyPrinter->prettyPrintExpr($value);
    }
}
