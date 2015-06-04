# phpDocumentor/Reflection

[![Build Status]](http://travis-ci.org/phpDocumentor/Reflection)

Using this library it is possible to statically reflect one or more files and create an object graph representing
your application's structure, including accompanying in-source documentation using DocBlocks.

The information that this library provides is similar to what the (built-in) Reflection extension of PHP provides; there 
are however several advantages to using this library:

- Due to its Static nature it does not execute procedural code in your reflected files where Dynamic Reflection does.
- Because the none of the code is interpreted by PHP (and executed) Static Reflection uses less memory.
- Can reflect complete files 
- Can reflect a whole project by reflecting multiple files.
- Reflects the contents of a DocBlock instead of just mentioning there is one.
- Is capable of analyzing code written for any PHP version (starting at 5.2) up to and including your installed
  PHP version.

## Features

* [Creates an object graph] containing the structure of your application much like a site map shows the 
  structure of a website.
* Supports [incrementally updating] a previously analyzed codebase by caching the results 
  and checking if any of the files have changed.
* Can [filter] the object graph, for example to hide specific elements.
* You can inspect your object graph, analyze it and report any errors and inconsistencies found using [validators].
* Can read and interpret code of any PHP version starting with 5.2 up to and including your currently installed version 
  of PHP.
* Can be integrated into Silex and Cilex using a [Service Provider].

## Installation

In order to inspect a codebase you need to tell composer to include the `phpdocumentor/reflection` package. This
can easily be done using the following command in your command line terminal:

    composer require "phpdocumentor/reflection: ~2.0"

After the installation is complete no further configuration is necessary and you can immediately start using it.

## Basic Usage

This Reflection library uses [PSR-0] and it is recommended to use a PSR-0 compatible autoloader to load all the 
files containing the classes for this library. 

An easy way to do this is by including the [composer] autoloader as shown here:

    include 'vendor/autoload.php';

Once that is done you can use the `create()` method of the `Analyzer` class to instantiate your source Analyzer and 
pre-configure it with sensible defaults.
    
    $analyzer = phpDocumentor\Descriptor\Analyzer::create();

At this point we are ready to analyze files, one at a time. By loading the file using an `SplFileObject` class and 
feeding that to the `analyze` of the `Analyzer` method we convert the PHP code in that file into an object of type 
`phpDocumentor\Descriptor\File`.

This object describing a file is returned to us but also added to another object that describes your entire project.

    $splFileObject = new \SplFileObject('tests/example.file.php');
    $analyzer->analyze($splFileObject);
    
The step above can be repeated for as many files as you have. When you are done you will have to call the finalize 
method of the analyzer. This method will do another analysis pass. This pass will connect the dots and do any processing
that relies the structure to be complete, such as adding linkage between all elements.

When the finalization is ready a new object of type `phpDocumentor\Descriptor\ProjectDescriptor` will be returned that
contains a complete hierarchy of all files with their classes, traits and interfaces (and everything in there), but also
all namespaces and packages as a hierarchical tree.

    $project = $analyzer->finalize();
    
When the finalization is ready a new object of type `phpDocumentor\Descriptor\ProjectDescriptor` will be returned that
contains a complete hierarchy of all files with their classes, traits and interfaces (and everything in there), but also
all namespaces and packages as a hierarchical tree.

> See the [example] script for a detailed and commented example

[Build Status]:            https://secure.travis-ci.org/phpDocumentor/Reflection.png
[PSR-0]:                   http://php-fig.com
[Creates an object graph]: docs/usage.rst
[incrementally updating]:  docs/incremental-updates.rst
[filter]:                  docs/filtering.rst
[validators]:              docs/inspecting.rst
[Service Provider]:        docs/integrating-with-silex-and-cilex.rst
[example]:                 example.php
[composer]:                http://getcomposer.org