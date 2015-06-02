<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.5
 *
 * @copyright 2010-2015 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Reflection\Php;


final class Visibility
{
    /**
     * @var string value can be public, protected or private
     */
    private $visibility;

    public function __construct($visibility)
    {
        $visibility = strtolower($visibility);

        if ($visibility !== 'public' && $visibility !== 'protected' && $visibility !== 'private') {
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