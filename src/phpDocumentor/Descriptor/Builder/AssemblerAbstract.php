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

namespace phpDocumentor\Descriptor\Builder;

use phpDocumentor\Descriptor\Analyzer;

/**
 * Base class for all assemblers.
 */
abstract class AssemblerAbstract implements AssemblerInterface
{
    /** @var Analyzer|null $analyzer */
    protected $analyzer;

    /**
     * Returns the analyzer for this Assembler or null if none is set.
     *
     * @return null|Analyzer
     */
    public function getAnalyzer()
    {
        return $this->analyzer;
    }

    /**
     * Registers the Analyzer with this Assembler.
     *
     * The Analyzer may be used to recursively assemble Descriptors using
     * the {@link Analyzer::analyze()} method.
     *
     * @param Analyzer $analyzer
     *
     * @return void
     */
    public function setAnalyzer(Analyzer $analyzer)
    {
        $this->analyzer = $analyzer;
    }
}
