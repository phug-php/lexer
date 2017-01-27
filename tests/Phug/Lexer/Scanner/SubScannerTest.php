<?php

namespace Phug\Test\Lexer\Scanner;

use Phug\Lexer\Token\ExpansionToken;
use Phug\Lexer\Token\TagToken;
use Phug\Lexer\Token\TextToken;
use Phug\Test\AbstractLexerTest;

class SubScannerTest extends AbstractLexerTest
{
    /**
     * @covers Phug\Lexer\Scanner\SubScanner
     * @covers Phug\Lexer\Scanner\SubScanner::scan
     */
    public function testScan()
    {
        $this->assertTokens('p Hello', [
            TagToken::class,
            TextToken::class,
        ]);

        $this->assertTokens('p. Hello', [
            TagToken::class,
            TextToken::class,
        ]);

        $this->assertTokens('p! Hello', [
            TagToken::class,
            TextToken::class,
        ]);

        $this->assertTokens('p!. Hello', [
            TagToken::class,
            TextToken::class,
        ]);

        $this->assertTokens('p:!. Hello', [
            TagToken::class,
            ExpansionToken::class,
            TextToken::class,
        ]);
    }
}
