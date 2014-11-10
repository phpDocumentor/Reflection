<?php
include __DIR__ . '/../../vendor/autoload.php';

use phpDocumentor\Descriptor\ProjectDescriptorBuilder;

$start = microtime(true);
$builder = ProjectDescriptorBuilder::create();
$builder2 = ProjectDescriptorBuilder::create();

/**
 * @param $filename
 */
function process($builder, $filename)
{
    if (!$filename) {
        return;
    }
    $splFileObject = new \SplFileObject($filename);
    $builder->buildFileUsingSourceData($splFileObject);
}

/**
 * @param $filename
 */
function process2($builder, $filename)
{
    if (!$filename) {
        return;
    }

    $fileReflector = new \phpDocumentor\Reflection\FileReflector($filename);
    $fileReflector->process();
    $builder->buildFileUsingSourceData($fileReflector);
}

/**
 * @return int
 */
function runSimpleTest($builder, $builder2, $iterations)
{
    $filename = __DIR__ . '/../example.file.php';
    for ($i = 0; $i < $iterations; $i++) {
        process($builder, $filename);
        process2($builder2, $filename);
    }
}

if ($argc > 1) {
    $files = `find ${argv[1]} -name "*.php" -type f -print`;
    $files = explode(PHP_EOL, $files);

    foreach ($files as $key => $file) {
        process($builder, $file);
        process2($builder2, $file);
    }
} else {
    runSimpleTest($builder, $builder2, 1);
}

$project = $builder->getProjectDescriptor();
$project2 = $builder2->getProjectDescriptor();

if ($project->getFiles() != $project2->getFiles()) {
    var_dump('mismatch!');
}

//$constant = current($project->getFiles()->getAll())->getConstants()->get('\Luigi\Pizza\VAT_HIGH');
//$constant2 = current($project2->getFiles()->getAll())->getConstants()->get('\Luigi\Pizza\VAT_HIGH');
//var_dump($constant->getTags()->getAll());
//var_dump($constant2->getTags()->getAll());

file_put_contents('phpparser.log', str_replace(';', "\n", serialize($project->getFiles()->getAll())));
file_put_contents('reflector.log', str_replace(';', "\n", serialize($project2->getFiles()->getAll())));

echo PHP_EOL;
