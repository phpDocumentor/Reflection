Expressions
===========

Starting with version 5.4, we now support parsing expressions and extracting types and references to elements from them.

.. info::

   An expression is, for example, the default value for a property or argument, the definition of an enum case or a
   constant value. These are called expressions and can contain more complex combinations of operators and values.

As this library revolves around reflecting Static information, most parts of an expression are considered irrelevant;
except for type information -such as type hints- and references to other elements, such as constants. As such, whenever
an expression is interpreted, it will result in a string containing placeholders and an array containing the reflected
parts -such as FQSENs-.

This means that the getters like ``getDefault()`` will return a string or when you provide the optional argument
$isString as being false, it will return an Expression object; which, when cast to string, will provide the same result.

.. warning::

   Deprecation: In version 6, we will remove the optional argument and always return an Expression object. When the
   result was used as a string nothing will change, but code that checks if the output is a string will no longer
   function starting from that version.

This will allow consumers to be able to extract types and links to elements from expressions. This allows consumers to,
for example, interpret the default value for a constructor promoted properties when it directly instantiates an object.
