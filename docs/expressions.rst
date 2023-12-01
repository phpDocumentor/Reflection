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

Creating expressions
--------------------

.. hint::

   The description below is only for internal usage and to understand how expressions work, this library deals with
   this by default.

In this library, we use the ExpressionPrinter to convert a PHP-Parser node -or expression- into an expression
like this::

     $printer = new ExpressionPrinter();
     $expressionTemplate = $printer->prettyPrintExpr($phpParserNode);
     $expression = new Expression($expressionTemplate, $printer->getParts());

In the example above we assume that there is a PHP-Parser node representing an expression; this node is passed to the
ExpressionPrinter -which is an adapted PrettyPrinter from PHP-Parser- which will render the expression as a readable
template string containing placeholders, and a list of parts that can slot into the placeholders.

Consuming expressions
---------------------

When using this library, you can consume these expression objects either by

1. Directly casting them to a string - this will replace all placeholders with the stringified version of the parts
2. Use the render function - this will do the same as the previous methods but you can specify one or more overrides
   for the placeholders in the expression

The second method can be used to create your own string values from the given parts and render, for example, links in
these locations.

Another way to use these expressions is to interpret the parts array, and through that way know which elements and
types are referred to in that expression.
