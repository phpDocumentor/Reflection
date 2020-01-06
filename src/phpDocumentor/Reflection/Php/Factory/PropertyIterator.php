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
use PhpParser\Node\Expr;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\NullableType;
use PhpParser\Node\Stmt\Property as PropertyNode;
use PhpParser\Node\UnionType;

/**
 * This class acts like a combination of a PropertyNode and PropertyProperty to
 * be able to create property descriptors using a normal strategy.
 */
final class PropertyIterator implements Iterator
{
    /** @var PropertyNode */
    private $property;

    /** @var int index of the current propertyProperty to use */
    private $index = 0;

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
    public function isPublic() : bool
    {
        return $this->property->isPublic();
    }

    /**
     * returns true when the current property is protected.
     */
    public function isProtected() : bool
    {
        return $this->property->isProtected();
    }

    /**
     * returns true when the current property is private.
     */
    public function isPrivate() : bool
    {
        return $this->property->isPrivate();
    }

    /**
     * returns true when the current property is static.
     */
    public function isStatic() : bool
    {
        return $this->property->isStatic();
    }

    /**
     * Gets line the node started in.
     */
    public function getLine() : int
    {
        return $this->property->getLine();
    }

    /**
     * Gets the type of the property.
     *
     * @return Identifier|Name|NullableType|UnionType|null
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
    public function getDocComment() : ?Doc
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
    public function getName() : string
    {
        return (string) $this->property->props[$this->index]->name;
    }

    /**
     * returns the default value of the current property.
     *
     * @return string|Expr|null
     */
    public function getDefault()
    {
        return $this->property->props[$this->index]->default;
    }

    /**
     * Returns the fqsen of the current property.
     */
    public function getFqsen() : Fqsen
    {
        return $this->property->props[$this->index]->fqsen;
    }

    /**
     * @link http://php.net/manual/en/iterator.current.php
     */
    public function current() : self
    {
        return $this;
    }

    /**
     * @link http://php.net/manual/en/iterator.next.php
     */
    public function next() : void
    {
        ++$this->index;
    }

    /**
     * @link http://php.net/manual/en/iterator.key.php
     */
    public function key() : ?int
    {
        return $this->index;
    }

    /**
     * @link http://php.net/manual/en/iterator.valid.php
     */
    public function valid() : bool
    {
        return isset($this->property->props[$this->index]);
    }

    /**
     * @link http://php.net/manual/en/iterator.rewind.php
     */
    public function rewind() : void
    {
        $this->index = 0;
    }
}
