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
     * @covers \Phug\Lexer\State::nextOutdent
     * @covers \Phug\Lexer\Scanner\IndentationScanner::scan
     * @covers \Phug\Lexer\Scanner\IndentationScanner::formatIndentChar
     * @covers \Phug\Lexer\Scanner\IndentationScanner::getIndentChar
     * @covers \Phug\Lexer\Scanner\IndentationScanner::getIndentLevel
     * @covers \Phug\Lexer\Scanner\TextBlockScanner
     * @covers \Phug\Lexer\Scanner\TextBlockScanner::createBlockTokens
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

        $this->assertTokens("section\n  div\n    p.\n      Hello\n  article", [
            TagToken::class,
            NewLineToken::class,
            IndentToken::class,
            TagToken::class,
            NewLineToken::class,
            IndentToken::class,
            TagToken::class,
            NewLineToken::class,
            IndentToken::class,
            TextToken::class,
            NewLineToken::class,
            OutdentToken::class,
            OutdentToken::class,
            TagToken::class,
        ]);

        $this->assertTokens("p.\n\n\n  Hello", [
            TagToken::class,
            NewLineToken::class,
            NewLineToken::class,
            NewLineToken::class,
            IndentToken::class,
            TextToken::class,
        ]);

        $this->assertTokens("p.\ndiv", [
            TagToken::class,
            NewLineToken::class,
            TagToken::class,
        ]);

        $this->assertTokens("section\n  p.\ndiv", [
            TagToken::class,
            NewLineToken::class,
            IndentToken::class,
            TagToken::class,
            NewLineToken::class,
            OutdentToken::class,
            TagToken::class,
        ]);
    }

    /**
     * @covers \Phug\Lexer\State::nextOutdent
     * @covers \Phug\Lexer\Scanner\IndentationScanner::scan
     * @covers \Phug\Lexer\Scanner\IndentationScanner::getIndentChar
     * @covers \Phug\Lexer\Scanner\IndentationScanner::getIndentLevel
     * @covers \Phug\Lexer\Scanner\TextBlockScanner
     * @covers \Phug\Lexer\Scanner\TextBlockScanner::createBlockTokens
     * @covers \Phug\Lexer\Scanner\TextBlockScanner::scan
     */
    public function testScanWhiteSpaces()
    {
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

        $tokens = $this->assertTokens("p.\n  Hello\n    world\n\n       \n  bye\n    \n\ndiv", [
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

        self::assertSame("Hello\n  world\n\n     \nbye\n  \n", $token->getValue());
    }
}
