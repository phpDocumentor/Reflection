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

use Generator;
use InvalidArgumentException;
use phpDocumentor\Reflection\Fqsen;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

use function sprintf;

#[CoversClass(Expression::class)]
final class ExpressionTest extends TestCase
{
    private const EXAMPLE_FQSEN = '\\' . self::class;
    private const EXAMPLE_FQSEN_PLACEHOLDER = '{{ PHPDOC0450ed2a7bac1efcf0c13b6560767954 }}';

    public function testGeneratingPlaceholder(): void
    {
        $placeholder = Expression::generatePlaceholder(self::EXAMPLE_FQSEN);

        self::assertSame(self::EXAMPLE_FQSEN_PLACEHOLDER, $placeholder);
    }

    public function testGeneratingPlaceholderErrorsUponPassingAnEmptyName(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Expression::generatePlaceholder('');
    }

    public function testExpressionTemplateCannotBeEmpty(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Expression('', []);
    }

    public function testPartsShouldContainFqsensOrTypes(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Expression('This is an expression', [self::EXAMPLE_FQSEN_PLACEHOLDER => self::EXAMPLE_FQSEN]);
    }

    public function testGetExpressionTemplateString(): void
    {
        $expressionTemplate = sprintf('This is an %s expression', self::EXAMPLE_FQSEN_PLACEHOLDER);
        $parts = [self::EXAMPLE_FQSEN_PLACEHOLDER => new Fqsen(self::EXAMPLE_FQSEN)];
        $expression = new Expression($expressionTemplate, $parts);

        $result = $expression->getExpression();

        self::assertSame($expressionTemplate, $result);
    }

    public function testGetExtractedParts(): void
    {
        $expressionTemplate = sprintf('This is an %s expression', self::EXAMPLE_FQSEN_PLACEHOLDER);
        $parts = [self::EXAMPLE_FQSEN_PLACEHOLDER => new Fqsen(self::EXAMPLE_FQSEN)];
        $expression = new Expression($expressionTemplate, $parts);

        $result = $expression->getParts();

        self::assertSame($parts, $result);
    }

    public function testReplacePlaceholdersWhenCastingToString(): void
    {
        $expressionTemplate = sprintf('This is an %s expression', self::EXAMPLE_FQSEN_PLACEHOLDER);
        $parts = [self::EXAMPLE_FQSEN_PLACEHOLDER => new Fqsen(self::EXAMPLE_FQSEN)];
        $expression = new Expression($expressionTemplate, $parts);

        $result = (string) $expression;

        self::assertSame(sprintf('This is an %s expression', self::EXAMPLE_FQSEN), $result);
    }

    public function testRenderingExpressionWithoutOverridesIsTheSameAsWhenCastingToString(): void
    {
        $expressionTemplate = sprintf('This is an %s expression', self::EXAMPLE_FQSEN_PLACEHOLDER);
        $parts = [self::EXAMPLE_FQSEN_PLACEHOLDER => new Fqsen(self::EXAMPLE_FQSEN)];
        $expression = new Expression($expressionTemplate, $parts);

        $result = $expression->render();

        self::assertSame((string) $expression, $result);
    }

    public function testOverridePartsWhenRenderingExpression(): void
    {
        $replacement = 'ExpressionTest';

        $expressionTemplate = sprintf('This is an %s expression', self::EXAMPLE_FQSEN_PLACEHOLDER);
        $parts = [self::EXAMPLE_FQSEN_PLACEHOLDER => new Fqsen(self::EXAMPLE_FQSEN)];
        $expression = new Expression($expressionTemplate, $parts);

        $result = $expression->render([self::EXAMPLE_FQSEN_PLACEHOLDER => $replacement]);

        self::assertSame(sprintf('This is an %s expression', $replacement), $result);
    }

    #[DataProvider('expressionValues')]
    public function testExpressionTemplateCreation(string $expression): void
    {
        $actual = new Expression($expression, []);
        self::assertSame($expression, $actual->getExpression());
    }

    /** @return Generator<string, array{expression: string} */
    public static function expressionValues(): Generator
    {
        $values = ['0', 'null', 'false'];

        foreach ($values as $value) {
            yield $value => ['expression' => $value];
        }
    }
}
