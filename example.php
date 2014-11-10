<?php

// use Composer's autoloader to load all classes
include 'vendor/autoload.php';

use phpDocumentor\Descriptor\ProjectDescriptorBuilder;

// Load a file's contents using the SplFileObject class
$splFileObject = new \SplFileObject('tests/example.file.php');

// Create a new object that constructs a Project for us
$builder = ProjectDescriptorBuilder::create();

// Feed the SplFileObject containing the contents of the file to
// the builder
$builder->buildFileUsingSourceData($splFileObject);

// Fetch the constructed project
$project = $builder->getProjectDescriptor();

