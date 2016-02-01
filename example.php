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
use phpDocumentor\Reflection\Php\Project;

include 'vendor/autoload.php';

// Create a new Analyzer with which we can analyze a PHP source file
$projectFactory = \phpDocumentor\Reflection\Php\ProjectFactory::createInstance();

// Create an array of files to analize.
$files = [ new \phpDocumentor\Reflection\File\LocalFile('tests/example.file.php') ];

//create a new project 'MyProject' containing all elements in the files.
/** @var Project $project */
$project = $projectFactory->create('MyProject', $files);

// As an example of what you can do, let's list all class names in the file 'tests/example.file.php'.
echo 'List all classes in the example source file: ' . PHP_EOL;

/** @var \phpDocumentor\Reflection\Php\Class_ $class */
foreach ($project->getFiles()['tests/example.file.php']->getClasses() as $class) {
    echo '- ' . $class->getFqsen() . PHP_EOL;
}
