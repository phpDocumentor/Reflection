<?php

namespace phpDocumentor\Descriptor\ProjectDescriptor\InitializerCommand;

use phpDocumentor\Descriptor\Filter\StripIgnore;
use phpDocumentor\Descriptor\Filter\StripInternal;
use phpDocumentor\Descriptor\Filter\StripOnVisibility;
use phpDocumentor\Descriptor\Analyzer;

class DefaultFilters
{
    public function __invoke(Analyzer $analyzer)
    {
        $filterManager = $analyzer->getFilterManager();

        $stripOnVisibility = new StripOnVisibility($analyzer);
        $filtersOnAllDescriptors = array(
            new StripInternal($analyzer),
            new StripIgnore($analyzer)
        );

        foreach ($filtersOnAllDescriptors as $filter) {
            $filterManager->attach('phpDocumentor\Descriptor\ClassDescriptor', $filter);
            $filterManager->attach('phpDocumentor\Descriptor\InterfaceDescriptor', $filter);
            $filterManager->attach('phpDocumentor\Descriptor\TraitDescriptor', $filter);
            $filterManager->attach('phpDocumentor\Descriptor\ConstantDescriptor', $filter);
            $filterManager->attach('phpDocumentor\Descriptor\FunctionDescriptor', $filter);
            $filterManager->attach('phpDocumentor\Descriptor\PropertyDescriptor', $filter);
            $filterManager->attach('phpDocumentor\Descriptor\MethodDescriptor', $filter);
        }

        $filterManager->attach('phpDocumentor\Descriptor\PropertyDescriptor', $stripOnVisibility);
        $filterManager->attach('phpDocumentor\Descriptor\MethodDescriptor', $stripOnVisibility);
    }
} 