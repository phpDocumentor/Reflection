Getting started
===============

This page will give you a quick introduction to the `phpdocumentor/reflection` package and how to get started with it.

Installation
------------

In order to inspect a codebase you need to tell composer to include the `phpdocumentor/reflection` package. This
can easily be done using the following command in your command line terminal:

.. code-block:: bash

    composer require phpdocumentor/reflection:~6.0

In order to use the library you need to include the autoloader of composer in your code.
 This can be done by adding the following line to your code:

.. code-block:: php

    <?php
    require_once 'vendor/autoload.php';

After the installation is complete no further configuration is necessary and you can immediately start using it.

Basic usage
------------

The :php:class:`\phpDocumentor\Reflection\Php\ProjectFactory` class is the entry point to the library and is used to create a new
project object that contains all the information about your codebase. It is configured with sensible defaults. And for most
usecases you can just use it as is.

.. code-block:: php

    $projectFactory = \phpDocumentor\Reflection\Php\ProjectFactory::createInstance();

At this point we are ready to analyze your complete project or just one file at the time. Just pass an array of file paths to the `create` method of the project factory.

.. code-block:: php

    $projectFiles = [new \phpDocumentor\Reflection\File\LocalFile('tests/example.file.php')];
    $project = $projectFactory->create('My Project', $projectFiles);

When the process is ready a new object of type :php:class:`phpDocumentor\Reflection\Php\Project` will be returned that
contains a complete hierarchy of all files with their classes, traits and interfaces (and everything in there), but also
all namespaces and packages as a hierarchical tree.
This library does not provide a way to access the structure of the codebase in a searchable way.
This is up to the consumer of the library to implement.

