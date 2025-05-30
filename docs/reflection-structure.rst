Reflection structure
====================

The project created by the :php:class:`\phpDocumentor\Reflection\Php\ProjectFactory` class contains a hierarchy of objects
that represent the structure of your codebase. This hierarchy includes:

Files
  Each file is represented by an object of type :php:class:`\phpDocumentor\Reflection\Php\File` which contains
  information about the file such as its name, path, and contents. But also the elements that are defined in the file.
  Files can be accessed through the :php:method:`\phpDocumentor\Reflection\Php\Project::getFiles()` method of the project object.
  Files are a flat list of all files that were analyzed, regardless of their location in the directory structure.

Namespaces
  Namespaces are represented by objects of type :php:class:`\phpDocumentor\Reflection\Php\Namespace_`. Each namespace
  contains a list of classes, interfaces, traits, and functions that are defined within it. Namespaces can be accessed
  through the :php:method:`\phpDocumentor\Reflection\Php\Project::getNamespaces()` method of the project object.
  Namespaces are hierarchical and can contain sub-namespaces.

Both namespaces and files do contain the other structural elements that are defined in them, such as classes, interfaces, traits, and functions.
This library does not provide a way to access the structure of the codebase in a searchable way. This is up to the consumer of the library to implement.
