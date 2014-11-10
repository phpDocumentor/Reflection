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
use phpDocumentor\Descriptor\Interfaces\VisibilityInterface;
use phpDocumentor\Descriptor\Analyzer;
use Zend\Filter\AbstractFilter;

/**
 * Strips any Descriptor if their visibility is allowed according to the Analyzer.
 */
class StripOnVisibility extends AbstractFilter
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
     * Filter Descriptor with based on visibility.
     *
     * @param DescriptorAbstract $value
     *
     * @return DescriptorAbstract|null
     */
    public function filter($value)
    {
        if ($value instanceof VisibilityInterface
            && !$this->analyzer->isVisibilityAllowed($value->getVisibility())
        ) {
            return null;
        }

        return $value;
    }
}
