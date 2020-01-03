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

namespace phpDocumentor\Reflection\Php;

use phpDocumentor\Reflection\Type;
use phpDocumentor\Reflection\Types\Mixed_;

/**
 * Descriptor representing a single Argument of a method or function.
 */
final class Argument
{
    /** @var string name of the Argument */
    private $name;

    /** @var Type a normalized type that should be in this Argument */
    private $type;

    /** @var string|null the default value for an argument or null if none is provided */
    private $default;

    /** @var bool whether the argument passes the parameter by reference instead of by value */
    private $byReference;

    /** @var bool Determines if this Argument represents a variadic argument */
    private $isVariadic;

    /**
     * Initializes the object.
     */
    public function __construct(
        string $name,
        ?Type $type = null,
        ?string $default = null,
        bool $byReference = false,
        bool $isVariadic = false
    ) {
        $this->name = $name;
        $this->default = $default;
        $this->byReference = $byReference;
        $this->isVariadic = $isVariadic;
        if ($type === null) {
            $type = new Mixed_();
        }

        $this->type = $type;
    }

    /**
     * Returns the name of this argument.
     */
    public function getName() : string
    {
        return $this->name;
    }

    public function getType() : ?Type
    {
        return $this->type;
    }

    public function getDefault() : ?string
    {
        return $this->default;
    }

    public function isByReference() : bool
    {
        return $this->byReference;
    }

    public function isVariadic() : bool
    {
        return $this->isVariadic;
    }
}
