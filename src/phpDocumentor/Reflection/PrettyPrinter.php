<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link http://phpdoc.org
 */

namespace phpDocumentor\Reflection;

use PhpParser\Lexer;
use PhpParser\Node\Scalar\String_;
use PhpParser\PrettyPrinter\Standard;

/**
 * Custom PrettyPrinter for phpDocumentor.
 *
 * phpDocumentor has a custom PrettyPrinter for PHP-Parser because it needs the
 * unmodified value for Scalar variables instead of an interpreted version.
 *
 * If the interpreted version was to be used then the XML interpretation would
 * fail because of special characters.
 */
class PrettyPrinter extends Standard
{
    /**
     * Converts the string into it's original representation without converting
     * the special character combinations.
     *
     * This method is overridden from the original Zend Pretty Printer because
     * the original returns the strings as interpreted by PHP-Parser.
     * Since we do not want such conversions we take the original that is
     * injected by our own custom Lexer.
     *
     * @see Lexer where the originalValue is injected.
     *
     * @param String_ $node The node to return a string representation of.
     */
    public function pScalar_String(String_ $node) : string
    {
        if (!$node->getAttribute('originalValue')) {
            return $node->value;
        }

        return (string) $node->getAttribute('originalValue');
    }
}
