<?php

namespace Phug\Test\Lexer\Scanner;

use Phug\Lexer\Token\DoToken;
use Phug\Lexer\Token\ExpansionToken;
use Phug\Lexer\Token\NewLineToken;
use Phug\Lexer\Token\TagToken;
use Phug\Lexer\Token\TextToken;
use Phug\Test\AbstractLexerTest;

class DoScannerTest extends AbstractLexerTest
{
    /**
     * @covers Phug\Lexer\Scanner\DoScanner::__construct
     * @covers Phug\Lexer\Scanner\ControlStatementScanner
     * @covers Phug\Lexer\Scanner\ControlStatementScanner::__construct
     * @covers Phug\Lexer\Scanner\ControlStatementScanner::scan
     */
    public function testSingleLine()
    {

        /* @var DoToken $tok */
        $this->assertTokens("do\n", [DoToken::class, NewLineToken::class]);
    }

    /**
     * @covers Phug\Lexer\Scanner\DoScanner::__construct
     * @covers Phug\Lexer\Scanner\ControlStatementScanner
     * @covers Phug\Lexer\Scanner\ControlStatementScanner::__construct
     * @covers Phug\Lexer\Scanner\ControlStatementScanner::scan
     */
    public function testExpanded()
    {

        /* @var DoToken $tok */
        $this->assertTokens('do: p something', [
            DoToken::class,
            ExpansionToken::class,
            TagToken::class,
            TextToken::class,
        ]);
    }
}
