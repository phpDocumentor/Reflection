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

namespace phpDocumentor\Reflection\Php;

/**
 * Value object for visibility values of classes, properties, ect.
 */
final class Visibility
{
    /**
     * constant for protected visibility
     */
    const PUBLIC_ = 'public';

    /**
     * constant for protected visibility
     */
    const PROTECTED_ = 'protected';

    /**
     * constant for private visibility
     */
    const PRIVATE_ = 'private';

    /**
     * @var string value can be public, protected or private
     */
    private $visibility;

    /**
     * Initializes the object.
     *
     * @param $visibility
     * @throws \InvalidArgumentException when visibility does not match public|protected|private
     */
    public function __construct($visibility)
    {
        $visibility = strtolower($visibility);

        if ($visibility !== static::PUBLIC_ && $visibility !== static::PROTECTED_ && $visibility !== static::PRIVATE_) {
            throw new \InvalidArgumentException(
                sprintf('""%s" is not a valid visibility value.', $visibility)
            );
        }

        $this->visibility = $visibility;
    }

    /**
     * Will return a string representation of visibility.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->visibility;
    }
}
