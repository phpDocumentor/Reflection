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

use InvalidArgumentException;
use phpDocumentor\Reflection\Fqsen;
use PHPUnit\Framework\TestCase;

use function sprintf;

/**
 * @coversDefaultClass Expression
 * @covers ::__construct
 * @covers ::<private>
 */
final class ExpressionTest extends TestCase
{
    private const EXAMPLE_FQSEN = '\\' . self::class;
    private const EXAMPLE_FQSEN_PLACEHOLDER = '{{ PHPDOC0450ed2a7bac1efcf0c13b6560767954 }}';

    /**
     * @covers ::generatePlaceholder
     */
    public function testGeneratingPlaceholder(): void
    {
        $placeholder = Expression::generatePlaceholder(self::EXAMPLE_FQSEN);

        self::assertSame(self::EXAMPLE_FQSEN_PLACEHOLDER, $placeholder);
    }

    /**
     * @covers ::generatePlaceholder
     */
    public function testGeneratingPlaceholderErrorsUponPassingAnEmptyName(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Expression::generatePlaceholder('');
    }

    /**
     * @covers ::__construct
     */
    public function testExpressionTemplateCannotBeEmpty(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Expression('', []);
    }

    /**
     * @covers ::__construct
     */
    public function testPartsShouldContainFqsensOrTypes(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Expression('This is an expression', [self::EXAMPLE_FQSEN_PLACEHOLDER => self::EXAMPLE_FQSEN]);
    }

    /**
     * @covers ::__construct
     * @covers ::getExpression
     */
    public function testGetExpressionTemplateString(): void
    {
        $expressionTemplate = sprintf('This is an %s expression', self::EXAMPLE_FQSEN_PLACEHOLDER);
        $parts = [self::EXAMPLE_FQSEN_PLACEHOLDER => new Fqsen(self::EXAMPLE_FQSEN)];
        $expression = new Expression($expressionTemplate, $parts);

        $result = $expression->getExpression();

        self::assertSame($expressionTemplate, $result);
    }

    /**
     * @covers ::__construct
     * @covers ::getParts
     */
    public function testGetExtractedParts(): void
    {
        $expressionTemplate = sprintf('This is an %s expression', self::EXAMPLE_FQSEN_PLACEHOLDER);
        $parts = [self::EXAMPLE_FQSEN_PLACEHOLDER => new Fqsen(self::EXAMPLE_FQSEN)];
        $expression = new Expression($expressionTemplate, $parts);

        $result = $expression->getParts();

        self::assertSame($parts, $result);
    }

    /**
     * @covers ::__toString
     */
    public function testReplacePlaceholdersWhenCastingToString(): void
    {
        $expressionTemplate = sprintf('This is an %s expression', self::EXAMPLE_FQSEN_PLACEHOLDER);
        $parts = [self::EXAMPLE_FQSEN_PLACEHOLDER => new Fqsen(self::EXAMPLE_FQSEN)];
        $expression = new Expression($expressionTemplate, $parts);

        $result = (string) $expression;

        self::assertSame(sprintf('This is an %s expression', self::EXAMPLE_FQSEN), $result);
    }

    /**
     * @covers ::render
     */
    public function testRenderingExpressionWithoutOverridesIsTheSameAsWhenCastingToString(): void
    {
        $expressionTemplate = sprintf('This is an %s expression', self::EXAMPLE_FQSEN_PLACEHOLDER);
        $parts = [self::EXAMPLE_FQSEN_PLACEHOLDER => new Fqsen(self::EXAMPLE_FQSEN)];
        $expression = new Expression($expressionTemplate, $parts);

        $result = $expression->render();

        self::assertSame((string) $expression, $result);
    }

    /**
     * @covers ::render
     */
    public function testOverridePartsWhenRenderingExpression(): void
    {
        $replacement = 'ExpressionTest';

        $expressionTemplate = sprintf('This is an %s expression', self::EXAMPLE_FQSEN_PLACEHOLDER);
        $parts = [self::EXAMPLE_FQSEN_PLACEHOLDER => new Fqsen(self::EXAMPLE_FQSEN)];
        $expression = new Expression($expressionTemplate, $parts);

        $result = $expression->render([self::EXAMPLE_FQSEN_PLACEHOLDER => $replacement]);

        self::assertSame(sprintf('This is an %s expression', $replacement), $result);
    }
}
