<?php
/**
     * Create a project map (similar to a sitemap) of project.
     *
     * The Reflection component is capable of analyzing one or more files into a hierarchy of objects representing the
     * structure of your project. It does this by analyzing the source code of each individual file using the
     * `analyze()` method in the Analyzer class.
     *
     * Because the Analyzer class requires a whole series of objects that interact together a factory method `create()`
     * is available. This method instantiates all objects and provides a reasonable default to start using the Analyzer.
     *
     * There is also a Service Provider (`\phpDocumentor\Descriptor\ServiceProvider`) that can be used with either Silex
     * or Cilex instead of using the factory method; this will make it easier to plug in your own features if you want to.
     */

// use Composer's autoloader to allow the application to automatically load all classes on request.
include 'vendor/autoload.php';

// Create a new Analyzer with which we can analyze a PHP source file
$analyzer = phpDocumentor\Descriptor\Analyzer::create();

// Load a file that is to be analyzed
$splFileObject = new \SplFileObject('tests/example.file.php');

// Analyze the given file, this will return a the structure of a single file as a
// `\phpDocumentor\Descriptor\File` class and populate a project descriptor object in the Analyzer.
$analyzer->analyze($splFileObject);

// The returned Project object is of class `phpDocumentor\Descriptor\Project`, see its DocBlock for more
// information on it.
$project = $analyzer->finalize();

// As an example of what you can do, let's list all class names in the file 'tests/example.file.php'.
echo 'List all classes in the example source file: ' . PHP_EOL;

/** @var \phpDocumentor\Descriptor\Class_ $class */
foreach ($project->getFiles()->get('tests/example.file.php')->getClasses() as $class) {
    echo '- ' . $class->getFullyQualifiedStructuralElementName() . PHP_EOL;
}
