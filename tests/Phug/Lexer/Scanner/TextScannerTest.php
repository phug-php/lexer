<?php

namespace Phug\Test\Lexer\Scanner;

use Phug\Lexer\Token\IndentToken;
use Phug\Lexer\Token\NewLineToken;
use Phug\Lexer\Token\TagToken;
use Phug\Lexer\Token\TextToken;
use Phug\Test\AbstractLexerTest;

class TextScannerTest extends AbstractLexerTest
{
    /**
     * @covers \Phug\Lexer\Scanner\TextScanner
     * @covers \Phug\Lexer\Scanner\TextScanner::scan
     */
    public function testText()
    {
        /* @var TextToken $tok */
        list($tok) = $this->assertTokens('| foo', [
            TextToken::class,
        ]);

        self::assertSame('foo', $tok->getValue());
        self::assertFalse($tok->isEscaped());
    }

    /**
     * @covers \Phug\Lexer\AbstractToken::getIndent
     */
    public function testTextIndent()
    {
        /* @var TextToken $tok */
        list(, , , $tok) = $this->assertTokens('p'."\n".'  | foo', [
            TagToken::class,
            NewLineToken::class,
            IndentToken::class,
            TextToken::class,
        ]);

        self::assertSame('  ', $tok->getIndent());
    }

    /**
     * @covers \Phug\Lexer\Scanner\TextScanner
     * @covers \Phug\Lexer\Scanner\TextScanner::scan
     */
    public function testTextQuit()
    {
        $this->assertTokens('|', []);
    }
}
