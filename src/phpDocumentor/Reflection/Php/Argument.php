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
 *
 * @api
 */
final class Argument
{
    /** @var Type a normalized type that should be in this Argument */
    private readonly Type $type;

    /**
     * Initializes the object.
     */
    public function __construct(
        /** @var string name of the Argument */
        private readonly string $name,
        Type|null $type = null,
        /** @var Expression|string|null the default value for an argument or null if none is provided */
        private Expression|string|null $default = null,
        /** @var bool whether the argument passes the parameter by reference instead of by value */
        private readonly bool $byReference = false,
        /** @var bool Determines if this Argument represents a variadic argument */
        private readonly bool $isVariadic = false,
    ) {
        if ($type === null) {
            $type = new Mixed_();
        }

        if (is_string($this->default)) {
            trigger_error(
                'Default values for arguments should be of type Expression, support for strings will be '
                . 'removed in 7.x',
                E_USER_DEPRECATED,
            );
            $this->default = new Expression($this->default, []);
        }

        $this->type = $type;
    }

    /**
     * Returns the name of this argument.
     */
    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): Type|null
    {
        return $this->type;
    }

    public function getDefault(bool $asString = true): Expression|string|null
    {
        if ($this->default === null) {
            return null;
        }

        if ($asString) {
            trigger_error(
                'The Default value will become of type Expression by default',
                E_USER_DEPRECATED,
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
