<?php

declare(strict_types=1);

namespace phpDocumentor\Reflection\Php\Factory;

use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Location;
use phpDocumentor\Reflection\Php\AsyncVisibility;
use phpDocumentor\Reflection\Php\Property as PropertyElement;
use phpDocumentor\Reflection\Php\Visibility;
use PhpParser\Modifiers;
use PhpParser\Node\ComplexType;
use PhpParser\Node\Expr;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Param;
use PhpParser\PrettyPrinter\Standard as PrettyPrinter;

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
    private DocBlock|null $docblock;
    private PrettyPrinter $valueConverter;
    private Expr|null $default;
    private bool $static;
    private Location $startLocation;
    private Location $endLocation;

    private function __construct()
    {
    }

    public static function create(PrettyPrinter $valueConverter): self
    {
        $instance = new self();
        $instance->valueConverter = $valueConverter;

        return $instance;
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

    public function docblock(DocBlock|null $docblock): self
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

    public function build(): PropertyElement
    {
        return new PropertyElement(
            $this->fqsen,
            $this->visibility,
            $this->docblock,
            $this->default !== null ? $this->valueConverter->prettyPrintExpr($this->default) : null,
            $this->static,
            $this->startLocation,
            $this->endLocation,
            (new Type())->fromPhpParser($this->type),
            $this->readOnly,
        );
    }

    /**
     * Returns true when current property has async accessors.
     *
     * This method will always return false when your phpparser version is < 5.2
     */
    private function isAsync(Param|PropertyIterator $node): bool
    {
        if (method_exists($node, 'isPrivateSet') === false) {
            return false;
        }

        return $node->isPublicSet() || $node->isProtectedSet() || $node->isPrivateSet();
    }

    private function buildVisibility(Param|PropertyIterator $node): Visibility
    {
        if ($this->isAsync($node) === false) {
            return $this->buildReadVisibility($node);
        }

        $readVisibility = $this->buildReadVisibility($node);
        $writeVisibility = $this->buildWriteVisibility($node);

        return new AsyncVisibility(
            $readVisibility,
            $writeVisibility,
        );
    }

    private function buildReadVisibility(Param|PropertyIterator $node): Visibility
    {
        if ($node instanceof Param && method_exists($node, 'isPublic') === false) {
            return $this->buildPromotedPropertyReadVisibility($node->flags);
        }

        if ($node->isPrivate()) {
            return new Visibility(Visibility::PRIVATE_);
        }

        if ($node->isProtected()) {
            return new Visibility(Visibility::PROTECTED_);
        }

        return new Visibility(Visibility::PUBLIC_);
    }

    private function buildPromotedPropertyReadVisibility(int $flags): Visibility
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
}
