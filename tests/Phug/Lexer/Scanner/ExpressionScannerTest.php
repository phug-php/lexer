<?php

namespace Phug\Test\Lexer\Scanner;

use Phug\Lexer\Scanner\ExpressionScanner;
use Phug\Lexer\State;
use Phug\Lexer\Token\ExpressionToken;
use Phug\Lexer\Token\TagToken;
use Phug\Test\AbstractLexerTest;

class ExpressionScannerTest extends AbstractLexerTest
{
    /**
     * @group i
     * @covers \Phug\Lexer\Scanner\ExpressionScanner
     * @covers \Phug\Lexer\Scanner\ExpressionScanner::scan
     */
    public function testExpressionInTag()
    {
        list($tag, $exp) = $this->assertTokens('script!= \'foo()\'', [
            TagToken::class,
            ExpressionToken::class,
        ]);
        self::assertSame('\'foo()\'', trim($exp->getValue()));
        self::assertFalse($exp->isEscaped());

        list($tok) = $this->assertTokens('=$foo', [
            ExpressionToken::class,
        ]);

        self::assertSame('$foo', $tok->getValue());
        self::assertTrue($tok->isEscaped());
        self::assertTrue($tok->isChecked());

        list($tok) = $this->assertTokens('!=42', [
            ExpressionToken::class,
        ]);

        self::assertSame('42', $tok->getValue());
        self::assertFalse($tok->isEscaped());
        self::assertTrue($tok->isChecked());

        list($tok) = $this->assertTokens('?=bar()', [
            ExpressionToken::class,
        ]);

        self::assertSame('bar()', $tok->getValue());
        self::assertTrue($tok->isEscaped());
        self::assertFalse($tok->isChecked());

        list($tok) = $this->assertTokens('?!=true', [
            ExpressionToken::class,
        ]);

        self::assertSame('true', $tok->getValue());
        self::assertFalse($tok->isEscaped());
        self::assertFalse($tok->isChecked());
    }

    /**
     * @covers \Phug\Lexer\Scanner\ExpressionScanner
     * @covers \Phug\Lexer\Scanner\ExpressionScanner::scan
     */
    public function testExpressionQuit()
    {
        $state = new State('p', []);
        $scanners = [
            'expression' => ExpressionScanner::class,
        ];
        $tokens = [];
        foreach ($state->loopScan($scanners) as $token) {
            $tokens[] = $token;
        }

        self::assertSame(0, count($tokens));
    }
}
