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

    /**
     * @var mixed[] an array of normalized types that should be in this Argument
     */
    private $types = [];

    /**
     * @var string|null the default value for an argument or null if none is provided
     */
    private $default = null;

    /**
     * @var bool whether the argument passes the parameter by reference instead of by value
     */
    private $byReference = false;

    /**
     * @var boolean Determines if this Argument represents a variadic argument
     */
    private $isVariadic = false;

    /**
     * Initializes the object.
     */
    public function __construct(string $name, ?string $default = null, bool $byReference = false, bool $isVariadic = false)
    {
        $this->name = $name;
        $this->default = $default;
        $this->byReference = $byReference;
        $this->isVariadic = $isVariadic;
    }

    /**
     * Returns the name of this argument.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return mixed[]
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

    public function getDefault(): ?string
    {
        return $this->default;
    }

    public function isByReference(): bool
    {
        return $this->byReference;
    }

    public function isVariadic(): bool
    {
        return $this->isVariadic;
    }
}
