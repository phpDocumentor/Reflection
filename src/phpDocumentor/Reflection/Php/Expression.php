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

namespace phpDocumentor\Reflection\Php;

use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Php\Expression\ExpressionPrinter;
use phpDocumentor\Reflection\Type;
use Webmozart\Assert\Assert;

use function array_keys;
use function md5;
use function str_replace;

/**
 * Represents an expression with a define statement, constant, property, enum case and any other location.
 *
 * Certain expressions contain useful references to other elements or types. Examples of these are:
 *
 * - Define statements that use an expression to refer to a class or function
 * - Properties whose default value refers to a constant
 * - Arguments whose default value initialize an object
 * - Enum Cases that refer to a function or constant
 *
 * This class represents every location where an expression is used and contains 2 pieces of information:
 *
 * - The expression string containing placeholders linking to useful information
 * - An array of 'parts' whose keys equal the placeholders in the expression string and whose values is the extracted
 *   information, such as an {@see FQSEN} or {@see Type}.
 *
 * In a way, the expression string is similar in nature to a URI Template (see links) where you have a string containing
 * variables that can be replaced. These variables are delimited by `{{` and `}}`, and are build up of the prefix PHPDOC
 * and then an MD5 hash of the name of the extracted information.
 *
 * It is not necessary for a consumer to interpret the information when they do not need it, a {@see self::__toString()}
 * magic method is provided that will replace the placeholders with the `toString()` output of each part.
 *
 * @link https://github.com/php/php-langspec/blob/master/spec/10-expressions.md
 *     for the definition of expressions in PHP.
 * @link https://www.rfc-editor.org/rfc/rfc6570 for more information on URI Templates.
 * @see ExpressionPrinter how an expression coming from PHP-Parser is transformed into an expression.
 *
 * @api
 */
final class Expression
{
    /** @var string The expression string containing placeholders for any extracted Types or FQSENs. */
    private string $expression;

    /**
     * The collection of placeholders with the value that their holding.
     *
     * In the expression string there can be several placeholders, this array contains a placeholder => value pair
     * that can be used by consumers to map the data to another formatting, adding links for example, and then render
     * the expression.
     *
     * @var array<string, Fqsen|Type>
     */
    private array $parts;

    /**
     * Returns the recommended placeholder string format given a name.
     *
     * Consumers can use their own formats when needed, the placeholders are all keys in the {@see self::$parts} array
     * and not interpreted by this class. However, to prevent collisions it is recommended to use this method to
     * generate a placeholder.
     *
     * @param string $name a string identifying the element for which the placeholder is generated.
     */
    public static function generatePlaceholder(string $name): string
    {
        Assert::notEmpty($name);

        return '{{ PHPDOC' . md5($name) . ' }}';
    }

    /** @param array<string, Fqsen|Type> $parts */
    public function __construct(string $expression, array $parts = [])
    {
        Assert::stringNotEmpty($expression);
        Assert::allIsInstanceOfAny($parts, [Fqsen::class, Type::class]);

        $this->expression = $expression;
        $this->parts = $parts;
    }

    /**
     * The raw expression string containing placeholders for any extracted Types or FQSENs.
     *
     * @see self::render() to render a human-readable expression and to replace some parts with custom values.
     * @see self::__toString() to render a human-readable expression with the previously extracted parts.
     */
    public function getExpression(): string
    {
        return $this->expression;
    }

    /**
     * A list of extracted parts for which placeholders exist in the expression string.
     *
     * The returned array will have the placeholders of the expression string as keys, and the related FQSEN or Type as
     * value. This can be used as a basis for doing your own transformations to {@see self::render()} the expression
     * in a custom way; or to extract type information from an expression and use that elsewhere in your application.
     *
     * @see ExpressionPrinter to transform a PHP-Parser expression into an expression string and list of parts.
     *
     * @return array<string, Fqsen|Type>
     */
    public function getParts(): array
    {
        return $this->parts;
    }

    /**
     * Renders the expression as a string and replaces all placeholders with either a provided value, or the
     * stringified value from the parts in this expression.
     *
     * The keys of the replacement parts should match those of {@see self::getParts()}, any unrecognized key is not
     * handled.
     *
     * @param array<string, string> $replacementParts
     */
    public function render(array $replacementParts = []): string
    {
        Assert::allStringNotEmpty($replacementParts);

        $valuesAsStrings = [];
        foreach ($this->parts as $placeholder => $part) {
            $valuesAsStrings[$placeholder] = $replacementParts[$placeholder] ?? (string) $part;
        }

        return str_replace(array_keys($this->parts), $valuesAsStrings, $this->expression);
    }

    /**
     * Returns a rendered version of the expression string where all placeholders are replaced by the stringified
     * versions of the included parts.
     *
     * @see self::$parts for the list of parts used in rendering
     * @see self::render() to influence rendering of the expression.
     */
    public function __toString(): string
    {
        return $this->render();
    }
}
