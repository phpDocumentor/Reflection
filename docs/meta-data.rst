Metadata
=====

The model of this library is as closed as possible.
Main reason is because consumers of the library should rely on cache.
A mutable and flexible interface of the model would most likely break the caching.
However after some time the users to this library started requesting for a more flexible format.
This is why metadata was introduced.

Create your first metadata
--------------------------

First step is to create your own metadata implementation.

.. code:: php
   final class Hook implements \phpDocumentor\Reflection\Metadata\Metadata
   {
      private string $hook;

      public function __construct(string $hook)
      {
          $this->hook = $hook;
      }

      public function key(): string
      {
         return "project-metadata";
      }

      public function hook(): string
      {
          return $this->hook;
      }
   }

.. note::
    We do highly recommend to keep your metadata objects small.
    When reflecting a large project the number of objects will grow fast.

Now we have an class that can be used it is time to create a :php:class:`\phpDocumentor\Reflection\Php\ProjectFactoryStrategy`.
Strategies are used to reflect nodes in the AST of `phpparser`_.

In the example below we are adding the Hook metadata to any functions containing a function call.

.. code:: php

   use \phpDocumentor\Reflection\Php\Function;

   final class HookStrategy implements \phpDocumentor\Reflection\Php\ProjectFactoryStrategy
   {
        public function matches(ContextStack $context, object $object): bool
        {
             return $this->context->peek() instanceof Function_ &&
                    $object instanceof \PhpParser\Node\Expr\FuncCall &&
                    ((string)$object->name) === 'hook'
        }

        public function create(ContextStack $context, object $object, StrategyContainer $strategies): void
        {
            $method = $context->peek();
            $method->addMetadata(new Hook($object->args[0]->value));
        }
   }

.. note::
    To speed up the reflection of your project the default factory instance has a Noop strategy. This strategy will
    ignore all statements that are not handled by any strategy. Keep this in mind when implementing your own strategies
    especially the statements you are looking for are nested in other statements like a ``while`` loop.

Finally add your new strategy to the project factory.

.. code:: php

    $factory = \phpDocumentor\Reflection\Php\ProjectFactory::createInstance();
    $factory->addStrategy(new HookStrategy());

.. _phpparser: https://github.com/nikic/PHP-Parser/
