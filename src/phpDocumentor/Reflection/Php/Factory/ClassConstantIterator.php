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
use PhpParser\Comment\Doc;
use PhpParser\Node\Stmt\ClassConst;

/**
 * This class acts like a combination of a ClassConst and Const_
 * to be able to create constant descriptors using a normal strategy.
 */
final class ClassConstantIterator implements Iterator
{
    /**
     * @var ClassConst
     */
    private $classConstants;

    /** @var int index of the current ClassConst to use */
    private $index = 0;

    /**
     * Initializes the class with source data.
     */
    public function __construct(ClassConst $classConst)
    {
        $this->classConstants = $classConst;
    }

    /**
     * Gets line the node started in.
     *
     * @return int Line
     */
    public function getLine()
    {
        return $this->classConstants->getLine();
    }

    /**
     * Returns the name of the current constant.
     *
     * @return string
     */
    public function getName()
    {
        return $this->classConstants->consts[$this->index]->name;
    }

    /**
     * Returns the fqsen of the current constant.
     *
     * @return Fqsen
     */
    public function getFqsen()
    {
        return $this->classConstants->consts[$this->index]->fqsen;
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
        $docComment = $this->classConstants->consts[$this->index]->getDocComment();
        if ($docComment === null) {
            $docComment = $this->classConstants->getDocComment();
        }

        return $docComment;
    }

    public function getValue()
    {
        return $this->classConstants->consts[$this->index]->value;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the current element
     * @link http://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
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
     * @return mixed scalar on success, or null on failure.
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
        return isset($this->classConstants->consts[$this->index]);
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
