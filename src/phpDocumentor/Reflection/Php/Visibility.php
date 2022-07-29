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

use InvalidArgumentException;

use function sprintf;
use function strtolower;

/**
 * Value object for visibility values of classes, properties, ect.
 */
final class Visibility
{
    /**
     * constant for protected visibility
     */
    public const PUBLIC_ = 'public';

    /**
     * constant for protected visibility
     */
    public const PROTECTED_ = 'protected';

    /**
     * constant for private visibility
     */
    public const PRIVATE_ = 'private';

    /** @var string value can be public, protected or private */
    private string $visibility;

    /**
     * Initializes the object.
     *
     * @throws InvalidArgumentException When visibility does not match public|protected|private.
     */
    public function __construct(string $visibility)
    {
        $visibility = strtolower($visibility);

        if ($visibility !== self::PUBLIC_ && $visibility !== self::PROTECTED_ && $visibility !== self::PRIVATE_) {
            throw new InvalidArgumentException(
                sprintf('""%s" is not a valid visibility value.', $visibility)
            );
        }

        $this->visibility = $visibility;
    }

    /**
     * Will return a string representation of visibility.
     */
    public function __toString(): string
    {
        return $this->visibility;
    }
}
