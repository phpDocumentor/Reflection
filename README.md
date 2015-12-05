# phpDocumentor/Reflection

[![Build Status]](http://travis-ci.org/phpDocumentor/Reflection)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/phpDocumentor/Reflection/badges/quality-score.png?b=develop)](https://scrutinizer-ci.com/g/phpDocumentor/Reflection/?branch=develop)
[![Code Coverage](https://scrutinizer-ci.com/g/phpDocumentor/Reflection/badges/coverage.png?b=develop)](https://scrutinizer-ci.com/g/phpDocumentor/Reflection/?branch=develop)

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
* Can read and interpret code of any PHP version starting with 5.2 up to and including your currently installed version 
  of PHP.
* Due it's clean interface it can be in any application without a complex setup.

## Installation

In order to inspect a codebase you need to tell composer to include the `phpdocumentor/reflection` package. This
can easily be done using the following command in your command line terminal:

    composer require "phpdocumentor/reflection: ~2.0"

After the installation is complete no further configuration is necessary and you can immediately start using it.

## Basic Usage

This Reflection library uses [PSR-4] and it is recommended to use a PSR-4 compatible autoloader to load all the 
files containing the classes for this library. 

An easy way to do this is by including the [composer] autoloader as shown here:

    include 'vendor/autoload.php';

Once that is done you can use the `createInstance()` method of the `\phpDocumentor\Reflection\Php\ProjectFactory` class to instantiate a new project factory and 
pre-configure it with sensible defaults. Optional you can specify the parser version that shall be used as an argument of `createInstance()`.
By default the php7 parser is prefered. And php5 is used as a fallback. See the [documentation of phpparser] for more info.
    
    $projectFactory = \phpDocumentor\Reflection\Php\ProjectFactory::createInstance();

At this point we are ready to analyze your complete project or just one file at the time. Just pass an array of file paths to the `create` method of the project factory.

    $projectFiles = ['tests/example.file.php'];
    $project = $projectFactory->create('My Project', $projectFiles);

When the process is ready a new object of type `phpDocumentor\Reflection\Php\Project` will be returned that
contains a complete hierarchy of all files with their classes, traits and interfaces (and everything in there), but also
all namespaces and packages as a hierarchical tree.

> See the [example] script for a detailed and commented example

[Build Status]:            https://secure.travis-ci.org/phpDocumentor/Reflection.png
[PSR-4]:                   http://php-fig.com
[example]:                 example.php
[composer]:                http://getcomposer.org
[documentation of phpparser]: https://github.com/nikic/PHP-Parser/blob/master/UPGRADE-2.0.md#creating-a-parser-instance 
