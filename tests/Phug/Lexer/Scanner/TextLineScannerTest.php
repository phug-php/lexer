<?php

namespace Phug\Test\Lexer\Scanner;

use Phug\Lexer\Token\TagToken;
use Phug\Lexer\Token\TextToken;
use Phug\Test\AbstractLexerTest;

class TextLineScannerTest extends AbstractLexerTest
{
    /**
     * @covers Phug\Lexer\Scanner\TextLineScanner
     * @covers Phug\Lexer\Scanner\TextLineScanner::scan
     */
    public function testScan()
    {
        $this->assertTokens('p Hello', [
            TagToken::class,
            TextToken::class,
        ]);
    }
}
