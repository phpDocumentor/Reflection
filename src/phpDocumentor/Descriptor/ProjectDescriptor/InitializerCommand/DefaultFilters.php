<?php

namespace phpDocumentor\Descriptor\ProjectDescriptor\InitializerCommand;

use phpDocumentor\Descriptor\Filter\StripIgnore;
use phpDocumentor\Descriptor\Filter\StripInternal;
use phpDocumentor\Descriptor\Filter\StripOnVisibility;
use phpDocumentor\Descriptor\ProjectDescriptorBuilder;

class DefaultFilters
{
    public function __invoke(ProjectDescriptorBuilder $projectDescriptorBuilder)
    {
        $filterManager = $projectDescriptorBuilder->getFilterManager();

        $stripOnVisibility = new StripOnVisibility($projectDescriptorBuilder);
        $filtersOnAllDescriptors = array(
            new StripInternal($projectDescriptorBuilder),
            new StripIgnore($projectDescriptorBuilder)
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