<?php

namespace Phug\Test\Lexer\Scanner;

use Phug\Lexer\Token\IndentToken;
use Phug\Lexer\Token\NewLineToken;
use Phug\Lexer\Token\OutdentToken;
use Phug\Lexer\Token\TagToken;
use Phug\Lexer\Token\TextToken;
use Phug\Test\AbstractLexerTest;

class TextBlockScannerTest extends AbstractLexerTest
{
    /**
     * @covers \Phug\Lexer\Scanner\TextBlockScanner
     * @covers \Phug\Lexer\Scanner\TextBlockScanner::scan
     */
    public function testScan()
    {
        $this->assertTokens('p. Hello', [
            TagToken::class,
            TextToken::class,
        ]);

        $this->assertTokens("p.\n  Hello", [
            TagToken::class,
            NewLineToken::class,
            IndentToken::class,
            TextToken::class,
        ]);

        $tokens = $this->assertTokens("p.\n  Hello\n    world\n  bye\ndiv", [
            TagToken::class,
            NewLineToken::class,
            IndentToken::class,
            TextToken::class,
            NewLineToken::class,
            OutdentToken::class,
            TagToken::class,
        ]);

        $tokens = array_filter($tokens, function ($token) {
            return $token instanceof TextToken;
        });
        $token = reset($tokens);

        self::assertSame("Hello\n  world\nbye", $token->getValue());

        $tokens = $this->assertTokens("p.\n  Hello\n    world\n       \n  bye\ndiv", [
            TagToken::class,
            NewLineToken::class,
            IndentToken::class,
            TextToken::class,
            NewLineToken::class,
            OutdentToken::class,
            TagToken::class,
        ]);

        $tokens = array_filter($tokens, function ($token) {
            return $token instanceof TextToken;
        });
        $token = reset($tokens);

        self::assertSame("Hello\n  world\n     \nbye", $token->getValue());
    }
}
