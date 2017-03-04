<?php

namespace Phug\Test\Lexer\Scanner;

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
        self::assertTrue($tok->isEscaped());
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
