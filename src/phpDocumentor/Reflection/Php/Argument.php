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

namespace phpDocumentor\Reflection\Php;

/**
 * Descriptor representing a single Argument of a method or function.
 */
final class Argument
{
    /**
     * @var string name of the Argument
     */
    private $name = null;

    /** @var string[] $type an array of normalized types that should be in this Argument */
    private $types = [];

    /** @var string|null $default the default value for an argument or null if none is provided */
    private $default = null;

    /** @var bool $byReference whether the argument passes the parameter by reference instead of by value */
    private $byReference = false;

    /** @var boolean Determines if this Argument represents a variadic argument */
    private $isVariadic = false;

    /**
     * Initializes the object.
     *
     * @param string $name
     * @param string $default
     * @param bool $byReference
     * @param bool $isVariadic
     */
    public function __construct(string $name, string $default = null, bool $byReference = false, bool $isVariadic = false)
    {
        $this->name = $name;
        $this->default = $default;
        $this->byReference = $byReference;
        $this->isVariadic = $isVariadic;
    }

    /**
     * Returns the name of this argument.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * {@inheritDoc}
     */
    public function getTypes(): array
    {
        return $this->types;
    }

    /**
     * Add a type.
     * @param mixed $type
     */
    public function addType($type): void
    {
        $this->types[] = $type;
    }

    /**
     * {@inheritDoc}
     */
    public function getDefault(): ?string
    {
        return $this->default;
    }

    /**
     * {@inheritDoc}
     */
    public function isByReference(): bool
    {
        return $this->byReference;
    }

    /**
     * Returns whether this argument represents a variadic argument.
     *
     * @return boolean
     */
    public function isVariadic(): bool
    {
        return $this->isVariadic;
    }
}
