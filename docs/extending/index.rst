.. _extending:

Extend the library
==================

The model exposed by this library is closed for inheritance. We did this to ensure the model is stable and does not
change via external factors. The complexity of this project makes it hard to keep all the internal classes stable.
The model is designed to be cached and constructed very carefully to ensure performance and memory usage are optimal.

Metadata
--------

Metadata is a way to extend the model with additional information. We call this metadata, as all first class
elements in the reflected codebase are part of the model. Extra data can be added to these elements using metadata.

Elements supporting metadata are:

.. phpdoc:class-list:: [?(@.interfaces contains "\phpDocumentor\Reflection\Metadata\MetaDataContainer")]

   .. phpdoc:name::

.. warning::

    Adding metadata might break the posibilty to cache the model. Be carefull with circular references and large
    objects. We do recommend to keep the metadata small and simple.

Continue reading :doc:`Creating your first metadata <meta-data>`_ to learn how to create your own metadata.

.. toctree::
   :maxdepth: 1
   :titlesonly:
   :hidden:

   meta-data
