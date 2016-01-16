<?php
/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright 2010-2015 Mike van Riel<mike@phpdoc.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Reflection\Php\Factory\Middleware;


use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Middleware\Middleware;

final class Implements_ implements Middleware
{

    /**
     * Executes this middle ware class.
     *
     * @param $command
     * @param callable $next
     *
     * @return object
     */
    public function execute($command, callable $next)
    {
        $element = $next($command);
        $object = $command->getObject();

        if (isset($object->implements)) {
            foreach ($object->implements as $interfaceClassName) {
                $element->addInterface(
                    new Fqsen('\\' . $interfaceClassName->toString())
                );
            }
        }

        return $element;
    }
}
