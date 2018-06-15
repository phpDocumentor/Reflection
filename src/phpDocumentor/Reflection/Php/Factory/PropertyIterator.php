<?php
declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright 2010-2018 Mike van Riel<mike@phpdoc.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Reflection\Php\Factory;

use Iterator;
use phpDocumentor\Reflection\Fqsen;
use PhpParser\Comment\Doc;
use PhpParser\Node\Expr;
use PhpParser\Node\Stmt\Property as PropertyNode;

/**
 * This class acts like a combination of a PropertyNode and PropertyProperty to
 * be able to create property descriptors using a normal strategy.
 */
final class PropertyIterator implements Iterator
{
    /**
     * @var PropertyNode
     */
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
     * Gets line the node started in.
     *
     * @return int Line
     */
    public function getLine(): int
    {
        return $this->property->getLine();
    }

    /**
     * Gets the doc comment of the node.
     *
     * The doc comment has to be the last comment associated with the node.
     *
     * @return null|Doc Doc comment object or null
     */
    public function getDocComment()
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
        return $this->property->props[$this->index]->name;
    }

    /**
     * returns the default value of the current property.
     *
     * @return null|string|Expr
     */
    public function getDefault()
    {
        return $this->property->props[$this->index]->default;
    }

    /**
     * Returns the fqsen of the current property.
     */
    public function getFqsen(): Fqsen
    {
        return $this->property->props[$this->index]->fqsen;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the current element
     * @link http://php.net/manual/en/iterator.current.php
     * @return PropertyIterator Can return any type.
     */
    public function current(): self
    {
        return $this;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Move forward to next element
     * @link http://php.net/manual/en/iterator.next.php
     */
    public function next(): void
    {
        ++$this->index;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the key of the current element
     * @link http://php.net/manual/en/iterator.key.php
     * @return integer scalar on success, or null on failure.
     */
    public function key(): ?int
    {
        return $this->index;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Checks if current position is valid
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     */
    public function valid(): bool
    {
        return isset($this->property->props[$this->index]);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Rewind the Iterator to the first element
     * @link http://php.net/manual/en/iterator.rewind.php
     */
    public function rewind(): void
    {
        $this->index = 0;
    }
}
