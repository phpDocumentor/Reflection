<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2014 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Descriptor\Filter;

use phpDocumentor\Descriptor\DescriptorAbstract;
use phpDocumentor\Descriptor\Analyzer;
use phpDocumentor\SimpleFilter\FilterInterface;

/**
 * Strips any Descriptor if the ignore tag is present with that element.
 */
class StripIgnore implements FilterInterface
{
    /** @var Analyzer $analyzer */
    protected $analyzer;

    /**
     * Initializes this filter with an instance of the analyzer to retrieve the latest ProjectDescriptor from.
     *
     * @param Analyzer $analyzer
     */
    public function __construct(Analyzer $analyzer)
    {
        $this->analyzer = $analyzer;
    }

    /**
     * Filter Descriptor with ignore tags.
     *
     * @param DescriptorAbstract $value
     *
     * @return DescriptorAbstract|null
     */
    public function filter($value)
    {
        if (!is_null($value) && $value->getTags()->get('ignore')) {
            return null;
        }

        return $value;
    }
}
