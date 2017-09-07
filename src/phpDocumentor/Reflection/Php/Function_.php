<?php
/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright 2010-2015 Mike van Riel<mike@phpdoc.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Reflection\Php;

use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\Element;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Location;

/**
 * Descriptor representing a function
 */
// @codingStandardsIgnoreStart
final class Function_ implements Element
// // @codingStandardsIgnoreEnd
{
    /**
     * @var Fqsen Full Qualified Structural Element Name
     */
    private $fqsen;

    /** @var Argument[] $arguments */
    private $arguments = array();

    /**
     * @var DocBlock|null
     */
    private $docBlock;

    /**
     * @var Location
     */
    private $location;

    /**
     * Initializes the object.
     *
     * @param Fqsen $fqsen
     * @param DocBlock|null $docBlock
     */
    public function __construct(Fqsen $fqsen, DocBlock $docBlock = null, Location $location = null)
    {
        if ($location === null) {
            $location = new Location(-1);
        }

        $this->fqsen = $fqsen;
        $this->docBlock = $docBlock;
        $this->location = $location;
    }

    /**
     * Returns the arguments of this function.
     *
     * @return Argument[]
     */
    public function getArguments()
    {
        return $this->arguments;
    }

    /**
     * Add an argument to the function.
     *
     * @param Argument $argument
     */
    public function addArgument(Argument $argument)
    {
        $this->arguments[] = $argument;
    }

    /**
     * Returns the Fqsen of the element.
     *
     * @return Fqsen
     */
    public function getFqsen()
    {
        return $this->fqsen;
    }

    /**
     * Returns the name of the element.
     *
     * @return string
     */
    public function getName()
    {
        return $this->fqsen->getName();
    }

    /**
     * Returns the DocBlock of the element if available
     *
     * @return null|DocBlock
     */
    public function getDocBlock()
    {
        return $this->docBlock;
    }

    /**
     * @return Location
     */
    public function getLocation()
    {
        return $this->location;
    }
}
