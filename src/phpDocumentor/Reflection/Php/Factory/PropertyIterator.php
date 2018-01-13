<?php
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
use PhpParser\Comment;
use PhpParser\Node;
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
     *
     * @return bool
     */
    public function isPublic()
    {
        return $this->property->isPublic();
    }

    /**
     * returns true when the current property is protected.
     *
     * @return bool
     */
    public function isProtected()
    {
        return $this->property->isProtected();
    }

    /**
     * returns true when the current property is private.
     *
     * @return bool
     */
    public function isPrivate()
    {
        return $this->property->isPrivate();
    }

    /**
     * returns true when the current property is static.
     *
     * @return bool
     */
    public function isStatic()
    {
        return $this->property->isStatic();
    }

    /**
     * Gets line the node started in.
     *
     * @return int Line
     */
    public function getLine()
    {
        return $this->property->getLine();
    }

    /**
     * Gets the doc comment of the node.
     *
     * The doc comment has to be the last comment associated with the node.
     *
     * @return null|Comment\Doc Doc comment object or null
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
     *
     * @return string
     */
    public function getName()
    {
        return $this->property->props[$this->index]->name;
    }

    /**
     * returns the default value of the current property.
     *
     * @return null|Node\Expr
     */
    public function getDefault()
    {
        return $this->property->props[$this->index]->default;
    }

    /**
     * Returns the fqsen of the current property.
     *
     * @return Fqsen
     */
    public function getFqsen()
    {
        return $this->property->props[$this->index]->fqsen;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the current element
     * @link http://php.net/manual/en/iterator.current.php
     * @return PropertyIterator Can return any type.
     */
    public function current()
    {
        return $this;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Move forward to next element
     * @link http://php.net/manual/en/iterator.next.php
     */
    public function next()
    {
        ++$this->index;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the key of the current element
     * @link http://php.net/manual/en/iterator.key.php
     * @return integer scalar on success, or null on failure.
     */
    public function key()
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
    public function valid()
    {
        return isset($this->property->props[$this->index]);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Rewind the Iterator to the first element
     * @link http://php.net/manual/en/iterator.rewind.php
     */
    public function rewind()
    {
        $this->index = 0;
    }
}
