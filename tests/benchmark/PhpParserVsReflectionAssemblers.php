<?php
include __DIR__ . '/../../vendor/autoload.php';

use phpDocumentor\Descriptor\Analyzer;

$start = microtime(true);
$analyzer = Analyzer::create();
$analyzer2 = Analyzer::create();

/**
 * @param $filename
 */
function process(Analyzer $analyzer, $filename)
{
    if (!$filename) {
        return;
    }
    $splFileObject = new \SplFileObject($filename);
    $analyzer->buildFileUsingSourceData($splFileObject);
}

/**
 * @param $filename
 */
function process2(Analyzer $analyzer, $filename)
{
    if (!$filename) {
        return;
    }

    $fileReflector = new \phpDocumentor\Reflection\FileReflector($filename);
    $fileReflector->process();
    $analyzer->buildFileUsingSourceData($fileReflector);
}

/**
 * @return int
 */
function runSimpleTest($analyzer, $analyzer2, $iterations)
{
    $filename = __DIR__ . '/../example.file.php';
    for ($i = 0; $i < $iterations; $i++) {
        process($analyzer, $filename);
        process2($analyzer2, $filename);
    }
}

if ($argc > 1) {
    $files = `find ${argv[1]} -name "*.php" -type f -print`;
    $files = explode(PHP_EOL, $files);

    foreach ($files as $key => $file) {
        process($analyzer, $file);
        process2($analyzer2, $file);
    }
} else {
    runSimpleTest($analyzer, $analyzer2, 1);
}

$project = $analyzer->getProjectDescriptor();
$project2 = $analyzer2->getProjectDescriptor();

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
