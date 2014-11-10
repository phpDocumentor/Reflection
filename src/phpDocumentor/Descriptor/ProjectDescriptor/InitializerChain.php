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

namespace phpDocumentor\Descriptor\ProjectDescriptor;

use phpDocumentor\Descriptor\ProjectDescriptorBuilder;

final class InitializerChain
{
    private $initializers = array();

    public function addInitializer($callable)
    {
        $this->initializers[] = $callable;
    }

    public function initialize(ProjectDescriptorBuilder $projectDescriptorBuilder)
    {
        foreach ($this->initializers as $initializer) {
            call_user_func($initializer, $projectDescriptorBuilder);
        }
    }
} 