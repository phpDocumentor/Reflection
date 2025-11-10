Reflection
==========

Using this library it is possible to statically reflect one or more files and create an object graph representing
your application's structure, including accompanying in-source documentation using DocBlocks.

The information that this library provides is similar to what the (built-in) Reflection extension of PHP provides; there
are however several advantages to using this library:

- Due to its Static nature it does not execute procedural code in your reflected files where Dynamic Reflection does.
- Because the none of the code is interpreted by PHP (and executed) Static Reflection uses less memory.
- Can reflect complete files
- Can reflect a whole project by reflecting multiple files.
- Reflects the contents of a DocBlock instead of just mentioning there is one.
- Is capable of analyzing code written for any PHP version (starting at 5.2) up to the lastest version, even if your installed
    PHP version is lower than the code you are reflecting.

.. note::
    As this library focuses on reflecting the structure of the codebase, it does not provide any options to manipulate
    the output. If you want to collect more information from the codebase you can read about :ref:`extending the library <extending>`.

.. toctree::
   :hidden:
   :maxdepth: 2

   getting-started
   reflection-structure
   expressions
   extending/index
