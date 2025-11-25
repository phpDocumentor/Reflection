<?php

declare(strict_types=1);

namespace unit\phpDocumentor\Reflection\Php\Expression;

use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Php\Expression\ExpressionPrinter;
use phpDocumentor\Reflection\PseudoTypes\True_;
use phpDocumentor\Reflection\Types\Null_;
use PhpParser\ParserFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(ExpressionPrinter::class)]
final class ExpressionPrinterTest extends TestCase
{
    /** @param array<string, Type> $expectedParts */
    #[DataProvider('argumentProvider')]
    public function testArgumentIsParsed(string $code, string $expectedExpression, array $expectedParts): void
    {
        $parser = (new ParserFactory())->createForHostVersion();
        $node = $parser->parse($code);
        $printer = new ExpressionPrinter();

        $expression = $printer->prettyPrintExpr($node[0]->params[0]->default);

        self::assertSame($expectedExpression, $expression);
        self::assertEquals($expectedParts, $printer->getParts());
    }

    /**
     * @return array<string, array{
     *   'code': string,
     *   'expectedExpression': string,
     *   'expectedParts': array<string, Type>
     * }>
     */
    public static function argumentProvider(): array
    {
        return [
            'myClassDefault' => [
                'code' => '<?php function foo(MyClass $arg = new MyClass()) {}',
                'expectedExpression' => 'new {{ PHPDOC9f1f93179bc4ea54e11fc3cda63a284f }}()',
                'expectedParts' => [
                    '{{ PHPDOC9f1f93179bc4ea54e11fc3cda63a284f }}' => new Fqsen('\MyClass'),
                ],
            ],
            // Enum case default.
            // After first run, replace ENUM_PLACEHOLDER below with the actual placeholder shown in the failure output.
            'enumCaseDefault' => [
                'code' => '<?php function foo(MyEnum $arg = MyEnum::CaseA) {}',
                'expectedExpression' => '{{ PHPDOC8844445ee68bb81ea3fd9529f906598b }}',
                'expectedParts' => [
                    '{{ PHPDOC8844445ee68bb81ea3fd9529f906598b }}' => new Fqsen('\MyEnum::CaseA'),
                ],
            ],
            'classConstantDefault' => [
                'code' => '<?php function foo(MyClass $arg = MyClass::SOME_CONST) {}',
                'expectedExpression' => '{{ PHPDOCe54b7c24dd0f847c3193039223751b3d }}',
                'expectedParts' => [
                    '{{ PHPDOCe54b7c24dd0f847c3193039223751b3d }}' => new Fqsen('\MyClass::SOME_CONST'),
                ],
            ],
            'selfConstantDefault' => [
                'code' => '<?php function foo(MyClass $arg = self::SOME_CONST) {}',
                'expectedExpression' => '{{ PHPDOCe54b7c24dd0f847c3193039223751b3d }}',
                'expectedParts' => [
                    '{{ PHPDOCe54b7c24dd0f847c3193039223751b3d }}' => new Fqsen('\self::SOME_CONST'),
                ],
            ],
            'stringDefault' => [
                'code' => '<?php function foo(string $arg = \'hello\') {}',
                'expectedExpression' => "'hello'",
                'expectedParts' => [],
            ],
            'intDefault' => [
                'code' => '<?php function foo(int $arg = 42) {}',
                'expectedExpression' => '42',
                'expectedParts' => [],
            ],
            'booleanDefault' => [
                'code' => '<?php function foo(bool $arg = true) {}',
                'expectedExpression' => '{{ PHPDOCb326b5062b2f0e69046810717534cb09 }}',
                'expectedParts' => [
                    '{{ PHPDOCb326b5062b2f0e69046810717534cb09 }}' => new True_(),
                ],
            ],
            'nullDefault' => [
                'code' => '<?php function foo(bool|null $arg = null) {}',
                'expectedExpression' => '{{ PHPDOC37a6259cc0c1dae299a7866489dff0bd }}',
                'expectedParts' => [
                    '{{ PHPDOC37a6259cc0c1dae299a7866489dff0bd }}' => new Null_(),
                ],
            ],
            'emptyArrayDefault' => [
                'code' => '<?php function foo(array $arg = []) {}',
                'expectedExpression' => '[]',
                'expectedParts' => [],
            ],
            'intArrayDefault' => [
                'code' => '<?php function foo(array $arg = [1, 2]) {}',
                'expectedExpression' => '[1, 2]',
                'expectedParts' => [],
            ],
            'stringArrayDefault' => [
                'code' => '<?php function foo(array $arg = [\'hello\', \'world\']) {}',
                'expectedExpression' => "['hello', 'world']",
                'expectedParts' => [],
            ],
            'objectArrayDefault' => [
                'code' => '<?php function foo(array $arg = [new MyClass(), new MyClass()]) {}',
                'expectedExpression' => '[new {{ PHPDOC9f1f93179bc4ea54e11fc3cda63a284f }}(), new {{ PHPDOC9f1f93179bc4ea54e11fc3cda63a284f }}()]',
                'expectedParts' => [
                    '{{ PHPDOC9f1f93179bc4ea54e11fc3cda63a284f }}' => new Fqsen('\MyClass'),
                ],
            ],
        ];
    }
}
