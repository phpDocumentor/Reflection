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

use function is_string;
use function trigger_error;

use const E_USER_DEPRECATED;

/**
 * Descriptor representing a single Argument of a method or function.
 */
final class Argument
{
    /** @var string name of the Argument */
    private string $name;

    /** @var Type a normalized type that should be in this Argument */
    private Type $type;

    /** @var Expression|null the default value for an argument or null if none is provided */
    private ?Expression $default;

    /** @var bool whether the argument passes the parameter by reference instead of by value */
    private bool $byReference;

    /** @var bool Determines if this Argument represents a variadic argument */
    private bool $isVariadic;

    /**
     * Initializes the object.
     *
     * @param string|Expression|null $default
     */
    public function __construct(
        string $name,
        ?Type $type = null,
        $default = null,
        bool $byReference = false,
        bool $isVariadic = false
    ) {
        $this->name = $name;
        $this->byReference = $byReference;
        $this->isVariadic = $isVariadic;
        if ($type === null) {
            $type = new Mixed_();
        }

        if (is_string($default)) {
            trigger_error(
                'Default values for arguments should be of type Expression, support for strings will be '
                . 'removed in 6.x',
                E_USER_DEPRECATED
            );
            $default = new Expression($default, []);
        }

        $this->default = $default;

        $this->type = $type;
    }

    /**
     * Returns the name of this argument.
     */
    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): ?Type
    {
        return $this->type;
    }

    /**
     * @return Expression|string|null
     */
    public function getDefault(bool $asString = true)
    {
        if ($this->default === null) {
            return null;
        }

        if ($asString) {
            trigger_error(
                'The Default value will become of type Expression by default',
                E_USER_DEPRECATED
            );

            return (string) $this->default;
        }

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
