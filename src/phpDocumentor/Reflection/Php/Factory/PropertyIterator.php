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
use phpDocumentor\Reflection\Fqsen;
use PhpParser\Comment\Doc;
use PhpParser\Node\ComplexType;
use PhpParser\Node\Expr;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Property as PropertyNode;

/**
 * This class acts like a combination of a PropertyNode and PropertyProperty to
 * be able to create property descriptors using a normal strategy.
 *
 * @implements Iterator<int, PropertyIterator>
 */
final class PropertyIterator implements Iterator
{
    private PropertyNode $property;

    /** @var int index of the current propertyProperty to use */
    private int $index = 0;

    /**
     * Instantiates this iterator with the propertyNode to iterate.
     */
    public function __construct(PropertyNode $property)
    {
        $this->property = $property;
    }

    /**
     * returns true when the current property is public.
     */
    public function isPublic(): bool
    {
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
     * returns true when the current property is private.
     */
    public function isPrivate(): bool
    {
        return $this->property->isPrivate();
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
     *
     * @return Identifier|Name|ComplexType|null
     */
    public function getType()
    {
        return $this->property->type;
    }

    /**
     * Gets the doc comment of the node.
     *
     * The doc comment has to be the last comment associated with the node.
     */
    public function getDocComment(): ?Doc
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
     * Returns the default value of the current property.
     */
    public function getDefault(): ?Expr
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

    /**
     * @link http://php.net/manual/en/iterator.current.php
     */
    public function current(): self
    {
        return $this;
    }

    /**
     * @link http://php.net/manual/en/iterator.next.php
     */
    public function next(): void
    {
        ++$this->index;
    }

    /**
     * @link http://php.net/manual/en/iterator.key.php
     */
    public function key(): ?int
    {
        return $this->index;
    }

    /**
     * @link http://php.net/manual/en/iterator.valid.php
     */
    public function valid(): bool
    {
        return isset($this->property->props[$this->index]);
    }

    /**
     * @link http://php.net/manual/en/iterator.rewind.php
     */
    public function rewind(): void
    {
        $this->index = 0;
    }
}
