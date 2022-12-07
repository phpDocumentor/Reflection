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
use PhpParser\Node\Stmt\ClassConst;

/**
 * This class acts like a combination of a ClassConst and Const_
 * to be able to create constant descriptors using a normal strategy.
 *
 * @implements Iterator<int, ClassConstantIterator>
 */
final class ClassConstantIterator implements Iterator
{
    private ClassConst $classConstants;

    /** @var int index of the current ClassConst to use */
    private int $index = 0;

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
    public function getLine(): int
    {
        return $this->classConstants->getLine();
    }

    /**
     * Gets line the node ended in.
     *
     * @return int Line
     */
    public function getEndLine(): int
    {
        return $this->classConstants->getEndLine();
    }

    /**
     * Returns the name of the current constant.
     */
    public function getName(): string
    {
        return (string) $this->classConstants->consts[$this->index]->name;
    }

    /**
     * Returns the fqsen of the current constant.
     */
    public function getFqsen(): Fqsen
    {
        return $this->classConstants->consts[$this->index]->getAttribute('fqsen');
    }

    /**
     * returns true when the current property is public.
     */
    public function isPublic(): bool
    {
        return $this->classConstants->isPublic();
    }

    /**
     * returns true when the current property is protected.
     */
    public function isProtected(): bool
    {
        return $this->classConstants->isProtected();
    }

    /**
     * returns true when the current property is private.
     */
    public function isPrivate(): bool
    {
        return $this->classConstants->isPrivate();
    }

    /**
     * Gets the doc comment of the node.
     *
     * The doc comment has to be the last comment associated with the node.
     */
    public function getDocComment(): ?Doc
    {
        $docComment = $this->classConstants->consts[$this->index]->getDocComment();
        if ($docComment === null) {
            $docComment = $this->classConstants->getDocComment();
        }

        return $docComment;
    }

    public function getValue(): Expr
    {
        return $this->classConstants->consts[$this->index]->value;
    }

    public function isFinal(): bool
    {
        return $this->classConstants->isFinal();
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
        return isset($this->classConstants->consts[$this->index]);
    }

    /**
     * @link http://php.net/manual/en/iterator.rewind.php
     */
    public function rewind(): void
    {
        $this->index = 0;
    }
}
