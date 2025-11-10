<?php

declare(strict_types=1);

namespace phpDocumentor\Reflection\Php\Factory;

use phpDocumentor\Reflection\DocBlockFactoryInterface;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Location;
use phpDocumentor\Reflection\NodeVisitor\FindingVisitor;
use phpDocumentor\Reflection\Php\AsymmetricVisibility;
use phpDocumentor\Reflection\Php\Expression;
use phpDocumentor\Reflection\Php\Expression\ExpressionPrinter;
use phpDocumentor\Reflection\Php\Factory\Reducer\Reducer;
use phpDocumentor\Reflection\Php\Property as PropertyElement;
use phpDocumentor\Reflection\Php\PropertyHook;
use phpDocumentor\Reflection\Php\StrategyContainer;
use phpDocumentor\Reflection\Php\Visibility;
use phpDocumentor\Reflection\Types\Context;
use PhpParser\Comment\Doc;
use PhpParser\Modifiers;
use PhpParser\Node;
use PhpParser\Node\ComplexType;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Param;
use PhpParser\Node\PropertyHook as PropertyHookNode;
use PhpParser\NodeTraverser;
use PhpParser\PrettyPrinter;
use PhpParser\PrettyPrinterAbstract;

use function array_filter;
use function array_map;
use function count;
use function is_string;
use function method_exists;

/**
 * This class is responsible for building a property element from a PhpParser node.
 *
 * @internal
 */
final class PropertyBuilder
{
    private Fqsen $fqsen;
    private Visibility $visibility;
    private bool $readOnly = false;
    private Identifier|Name|ComplexType|null $type;
    private Doc|null $docblock = null;

    private Expr|null $default = null;
    private bool $static = false;
    private Location $startLocation;
    private Location $endLocation;

    /** @var PropertyHookNode[] */
    private array $hooks = [];

    /** @param iterable<Reducer> $reducers */
    private function __construct(
        private PrettyPrinter|PrettyPrinterAbstract $valueConverter,
        private DocBlockFactoryInterface $docBlockFactory,
        private StrategyContainer $strategies,
        private iterable $reducers,
    ) {
        $this->visibility = new Visibility(Visibility::PUBLIC_);
    }

    /** @param iterable<Reducer> $reducers */
    public static function create(
        PrettyPrinter|PrettyPrinterAbstract $valueConverter,
        DocBlockFactoryInterface $docBlockFactory,
        StrategyContainer $strategies,
        iterable $reducers,
    ): self {
        return new self($valueConverter, $docBlockFactory, $strategies, $reducers);
    }

    public function fqsen(Fqsen $fqsen): self
    {
        $this->fqsen = $fqsen;

        return $this;
    }

    public function visibility(Param|PropertyIterator $node): self
    {
        $this->visibility = $this->buildVisibility($node);

        return $this;
    }

    public function type(Identifier|Name|ComplexType|null $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function readOnly(bool $readOnly): self
    {
        $this->readOnly = $readOnly;

        return $this;
    }

    public function docblock(Doc|null $docblock): self
    {
        $this->docblock = $docblock;

        return $this;
    }

    public function default(Expr|null $default): self
    {
        $this->default = $default;

        return $this;
    }

    public function static(bool $static): self
    {
        $this->static = $static;

        return $this;
    }

    public function startLocation(Location $startLocation): self
    {
        $this->startLocation = $startLocation;

        return $this;
    }

    public function endLocation(Location $endLocation): self
    {
        $this->endLocation = $endLocation;

        return $this;
    }

    /** @param PropertyHookNode[] $hooks */
    public function hooks(array $hooks): self
    {
        $this->hooks = $hooks;

        return $this;
    }

    public function build(ContextStack $context): PropertyElement
    {
        $hooks = array_filter(array_map(
            fn (PropertyHookNode $hook) => $this->buildHook($hook, $context, $this->visibility),
            $this->hooks,
        ));

        // Check if this is a virtual property by examining all hooks
        $isVirtual = $this->isVirtualProperty($this->hooks, $this->fqsen->getName());

        return new PropertyElement(
            $this->fqsen,
            $this->visibility,
            $this->docblock !== null ? $this->docBlockFactory->create($this->docblock->getText(), $context->getTypeContext()) : null,
            $this->determineDefault($context->getTypeContext()),
            $this->static,
            $this->startLocation,
            $this->endLocation,
            (new Type())->fromPhpParser($this->type),
            $this->readOnly,
            $hooks,
            $isVirtual,
        );
    }

    /**
     * Returns true when current property has asymmetric accessors.
     *
     * This method will always return false when your phpparser version is < 5.2
     */
    private function isAsymmetric(Param|PropertyIterator $node): bool
    {
        if (method_exists($node, 'isPrivateSet') === false) {
            return false;
        }

        return $node->isPublicSet() || $node->isProtectedSet() || $node->isPrivateSet();
    }

    private function buildVisibility(Param|PropertyIterator $node): Visibility
    {
        if ($this->isAsymmetric($node) === false) {
            return $this->buildReadVisibility($node);
        }

        $readVisibility = $this->buildReadVisibility($node);
        $writeVisibility = $this->buildWriteVisibility($node);

        if ((string) $writeVisibility === (string) $readVisibility) {
            return $readVisibility;
        }

        return new AsymmetricVisibility(
            $readVisibility,
            $writeVisibility,
        );
    }

    private function buildReadVisibility(Param|PropertyIterator $node): Visibility
    {
        if ($node instanceof Param && method_exists($node, 'isPublic') === false) {
            return $this->buildVisibilityFromFlags($node->flags);
        }

        if ($node->isPrivate()) {
            return new Visibility(Visibility::PRIVATE_);
        }

        if ($node->isProtected()) {
            return new Visibility(Visibility::PROTECTED_);
        }

        return new Visibility(Visibility::PUBLIC_);
    }

    private function buildVisibilityFromFlags(int $flags): Visibility
    {
        if ((bool) ($flags & Modifiers::PRIVATE) === true) {
            return new Visibility(Visibility::PRIVATE_);
        }

        if ((bool) ($flags & Modifiers::PROTECTED) === true) {
            return new Visibility(Visibility::PROTECTED_);
        }

        return new Visibility(Visibility::PUBLIC_);
    }

    private function buildWriteVisibility(Param|PropertyIterator $node): Visibility
    {
        if ($node->isPrivateSet()) {
            return new Visibility(Visibility::PRIVATE_);
        }

        if ($node->isProtectedSet()) {
            return new Visibility(Visibility::PROTECTED_);
        }

        return new Visibility(Visibility::PUBLIC_);
    }

    private function buildHook(PropertyHookNode $hook, ContextStack $context, Visibility $propertyVisibility): PropertyHook|null
    {
        $doc = $hook->getDocComment();

        $result = new PropertyHook(
            $hook->name->toString(),
            $this->buildHookVisibility($hook->name->toString(), $propertyVisibility),
            $doc !== null ? $this->docBlockFactory->create($doc->getText(), $context->getTypeContext()) : null,
            $hook->isFinal(),
            new Location($hook->getStartLine()),
            new Location($hook->getEndLine()),
        );

        foreach ($this->reducers as $reducer) {
            $result = $reducer->reduce($context, $hook, $this->strategies, $result);
        }

        if ($result === null) {
            return $result;
        }

        $thisContext = $context->push($result);
        foreach ($hook->getStmts() ?? [] as $stmt) {
            $strategy = $this->strategies->findMatching($thisContext, $stmt);
            $strategy->create($thisContext, $stmt, $this->strategies);
        }

        return $result;
    }

    /**
     * Detects if a property is virtual by checking if any of its hooks reference the property itself.
     *
     * A virtual property is one where no defined hook references the property itself.
     * For example, in the 'get' hook, it doesn't use $this->propertyName.
     *
     * @param PropertyHookNode[] $hooks The property hooks to check
     * @param string $propertyName The name of the property
     *
     * @return bool True if the property is virtual, false otherwise
     */
    private function isVirtualProperty(array $hooks, string $propertyName): bool
    {
        if (empty($hooks)) {
            return false;
        }

        foreach ($hooks as $hook) {
            $stmts = $hook->getStmts();

            if ($stmts === null || count($stmts) === 0) {
                continue;
            }

            $finder = new FindingVisitor(
                static function (Node $node) use ($propertyName) {
                    // Check if the node is a property fetch that references the property
                    return $node instanceof PropertyFetch && $node->name instanceof Identifier &&
                        $node->name->toString() === $propertyName &&
                        $node->var instanceof Variable &&
                        $node->var->name === 'this';
                },
            );

            $traverser = new NodeTraverser($finder);
            $traverser->traverse($stmts);

            if ($finder->getFoundNode() !== null) {
                return false;
            }
        }

        return true;
    }

    /**
     * Builds the hook visibility based on the hook name and property visibility.
     *
     * @param string $hookName The name of the hook ('get' or 'set')
     * @param Visibility $propertyVisibility The visibility of the property
     *
     * @return Visibility The appropriate visibility for the hook
     */
    private function buildHookVisibility(string $hookName, Visibility $propertyVisibility): Visibility
    {
        if ($propertyVisibility instanceof AsymmetricVisibility === false) {
            return $propertyVisibility;
        }

        return match ($hookName) {
            'get' => $propertyVisibility->getReadVisibility(),
            'set' => $propertyVisibility->getWriteVisibility(),
            default => $propertyVisibility,
        };
    }

    private function determineDefault(Context|null $context): Expression|null
    {
        if ($this->valueConverter instanceof ExpressionPrinter) {
            $expression = $this->default !== null ? $this->valueConverter->prettyPrintExpr($this->default, $context) : null;
        } else {
            $expression = $this->default !== null ? $this->valueConverter->prettyPrintExpr($this->default) : null;
        }

        if ($expression === null) {
            return null;
        }

        if ($this->valueConverter instanceof ExpressionPrinter) {
            $expression = new Expression($expression, $this->valueConverter->getParts());
        }

        if (is_string($expression)) {
            $expression = new Expression($expression, []);
        }

        return $expression;
    }
}
