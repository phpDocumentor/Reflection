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

use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\Element;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Location;

/**
 * Descriptor representing a constant
 */
final class Constant implements Element
{
    /**
     * @var Fqsen
     */
    private $fqsen;

    /**
     * @var null|DocBlock
     */
    private $docBlock;

    /** @var null|string $value */
    private $value;

    /**
     * @var Location
     */
    private $location;

    /**
     * Initializes the object.
     *
     * @param DocBlock|null $docBlock
     * @param null|string $value
     * @param Location|null $location
     */
    public function __construct(Fqsen $fqsen, DocBlock $docBlock = null, string $value = null, Location $location = null)
    {
        $this->fqsen = $fqsen;
        $this->docBlock = $docBlock;
        $this->value = $value;

        if ($location === null) {
            $location = new Location(-1);
        }

        $this->location = $location;
    }

    /**
     * Returns the value of this constant.
     */
    public function getValue(): ?string
    {
        return $this->value;
    }

    /**
     * Returns the Fqsen of the element.
     */
    public function getFqsen(): Fqsen
    {
        return $this->fqsen;
    }

    /**
     * Returns the name of the element.
     */
    public function getName(): string
    {
        return $this->fqsen->getName();
    }

    /**
     * Returns DocBlock of this constant if available.
     */
    public function getDocBlock(): ?DocBlock
    {
        return $this->docBlock;
    }

    public function getLocation(): Location
    {
        return $this->location;
    }
}
