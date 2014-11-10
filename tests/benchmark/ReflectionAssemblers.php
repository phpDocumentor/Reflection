<?php
include __DIR__ . '/../../vendor/autoload.php';

use phpDocumentor\Descriptor\Analyzer;

$start = microtime(true);
$analyzer = Analyzer::create();

/**
 * @param $filename
 */
function process(Analyzer $analyzer, $filename)
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
function runSimpleTest($analyzer, $iterations)
{
    $filename = __DIR__ . '/../example.file.php';
    echo 'Performing ' . $iterations . ' iterations using the PhpParser Assemblers' . PHP_EOL;
    for ($i = 0; $i < $iterations; $i++) {
        process($analyzer, $filename);

        // run once first to lazy load all classes
        if ($i == 0) {
            $memoryStart = xdebug_peak_memory_usage();
            echo 'Start peak memory usage: ' . round($memoryStart / 1024 / 1024, 2) . ' MB' . PHP_EOL;
        }
    }
    return $memoryStart;
}

if ($argc > 1) {
    $files = `find ${argv[1]} -name "*.php" -type f -print`;
    $files = explode(PHP_EOL, $files);
    $iterations = count($files);
    echo 'Performing ' . $iterations . ' iterations using the PhpParser Assemblers on ' . $argv[1] . PHP_EOL;

    foreach ($files as $key => $file) {
        process($analyzer, $file);

        // run once first to lazy load all classes
        if ($key == 0) {
            $memoryStart = xdebug_peak_memory_usage();
            echo 'Start peak memory usage: ' . round($memoryStart / 1024 / 1024, 2) . ' MB' . PHP_EOL;
        }
    }
} else {
    $iterations = 100;
    $memoryStart = runSimpleTest($analyzer, $iterations);
}

$memoryUsage = xdebug_peak_memory_usage();
$executionTime = microtime(true) - $start;
$memoryIncrease = $memoryUsage - $memoryStart;
echo 'Execution time: ' . round($executionTime, 2) . ' seconds' . PHP_EOL;
echo 'Peak memory usage: ' . round($memoryUsage / 1024 / 1024, 2) . ' MB' . PHP_EOL;
echo 'Memory increase due to process: ' . round($memoryIncrease / 1024 / 1024, 2) . ' MB' . PHP_EOL;
echo 'Avg execution time per file: ' . round($executionTime / $iterations, 2) . ' MB' . PHP_EOL;
echo 'Avg memory increase per file: ' . round($memoryIncrease  / 1024 / 1024 / $iterations, 2) . ' MB' . PHP_EOL;
echo 'Expected execution time for 10.000 files: ' . round(($executionTime / $iterations) * 10000, 2) . ' seconds' . PHP_EOL;
echo 'Expected memory increase for 10.000 files: ' . round(($memoryIncrease / $iterations) / 1024 / 1024 * 10000, 2) . ' MB' . PHP_EOL;
echo PHP_EOL;
