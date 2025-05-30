<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link http://phpdoc.org
 */

namespace phpDocumentor\Reflection\Php\Factory;

use Iterator;
use Override;
use phpDocumentor\Reflection\Fqsen;
use PhpParser\Comment\Doc;
use PhpParser\Node\ComplexType;
use PhpParser\Node\Expr;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\PropertyHook;
use PhpParser\Node\Stmt\Property as PropertyNode;

use function method_exists;
use function property_exists;

/**
 * This class acts like a combination of a PropertyNode and PropertyProperty to
 * be able to create property descriptors using a normal strategy.
 *
 * @implements Iterator<int, PropertyIterator>
 */
final class PropertyIterator implements Iterator
{
    /** @var int index of the current propertyProperty to use */
    private int $index = 0;

    /**
     * Instantiates this iterator with the propertyNode to iterate.
     */
    public function __construct(private readonly PropertyNode $property)
    {
    }

    /**
     * returns true when the current property is public.
     */
    public function isPublic(): bool
    {
        return $this->property->isPublic();
    }

    /**
     * Returns asymmetric accessor value for current property.
     *
     * This method will return the same value as {@see self::isPublic()} when your phpparser version is < 5.2
     */
    public function isPublicSet(): bool
    {
        if ($this->isAsymmetric() === false) {
            return $this->isPublic();
        }

        return $this->property->isPublic();
    }

    /**
     * returns true when the current property is protected.
     */
    public function isProtected(): bool
    {
        return $this->property->isProtected();
    }

    /**
     * Returns asymetric accessor value for current property.
     *
     * This method will return the same value as {@see self::isProtected()} when your phpparser version is < 5.2
     */
    public function isProtectedSet(): bool
    {
        if ($this->isAsymmetric() === false) {
            return $this->isProtected();
        }

        return $this->property->isProtectedSet();
    }

    /**
     * returns true when the current property is private.
     */
    public function isPrivate(): bool
    {
        return $this->property->isPrivate();
    }

    /**
     * Returns asymetric accessor value for current property.
     *
     * This method will return the same value as {@see self::isPrivate()} when your phpparser version is < 5.2
     */
    public function isPrivateSet(): bool
    {
        if ($this->isAsymmetric() === false) {
            return $this->isPrivate();
        }

        return $this->property->isPrivateSet();
    }

    /**
     * Returns true when current property has asymetric accessors.
     *
     * This method will always return false when your phpparser version is < 5.2
     */
    public function isAsymmetric(): bool
    {
        if (method_exists($this->property, 'isPrivateSet') === false) {
            return false;
        }

        return $this->property->isPublicSet() || $this->property->isProtectedSet() || $this->property->isPrivateSet();
    }

    /**
     * returns true when the current property is static.
     */
    public function isStatic(): bool
    {
        return $this->property->isStatic();
    }

    /**
     * returns true when the current property is readonly.
     */
    public function isReadOnly(): bool
    {
        return $this->property->isReadOnly();
    }

    /**
     * Gets line the node started in.
     */
    public function getLine(): int
    {
        return $this->property->getLine();
    }

    /**
     * Gets line the node started in.
     */
    public function getEndLine(): int
    {
        return $this->property->getEndLine();
    }

    /**
     * Gets the type of the property.
     */
    public function getType(): Identifier|Name|ComplexType|null
    {
        return $this->property->type;
    }

    /**
     * Gets the doc comment of the node.
     *
     * The doc comment has to be the last comment associated with the node.
     */
    public function getDocComment(): Doc|null
    {
        $docComment = $this->property->props[$this->index]->getDocComment();
        if ($docComment === null) {
            $docComment = $this->property->getDocComment();
        }

        return $docComment;
    }

    /**
     * returns the name of the current property.
     */
    public function getName(): string
    {
        return (string) $this->property->props[$this->index]->name;
    }

    /**
     * returns the default value of the current property.
     */
    public function getDefault(): Expr|null
    {
        return $this->property->props[$this->index]->default;
    }

    /**
     * Returns the fqsen of the current property.
     */
    public function getFqsen(): Fqsen
    {
        return $this->property->props[$this->index]->getAttribute('fqsen');
    }

    /** @return PropertyHook[] */
    public function getHooks(): array
    {
        if (property_exists($this->property, 'hooks') === false) {
            return [];
        }

        return $this->property->hooks;
    }

    /** @link http://php.net/manual/en/iterator.current.php */
    #[Override]
    public function current(): self
    {
        return $this;
    }

    /** @link http://php.net/manual/en/iterator.next.php */
    #[Override]
    public function next(): void
    {
        ++$this->index;
    }

    /** @link http://php.net/manual/en/iterator.key.php */
    #[Override]
    public function key(): int|null
    {
        return $this->index;
    }

    /** @link http://php.net/manual/en/iterator.valid.php */
    #[Override]
    public function valid(): bool
    {
        return isset($this->property->props[$this->index]);
    }

    /** @link http://php.net/manual/en/iterator.rewind.php */
    #[Override]
    public function rewind(): void
    {
        $this->index = 0;
    }
}
