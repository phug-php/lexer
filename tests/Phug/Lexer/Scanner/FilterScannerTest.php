<?php

namespace Phug\Test\Lexer\Scanner;

use Phug\Lexer\Token\FilterToken;
use Phug\Lexer\Token\IndentToken;
use Phug\Lexer\Token\NewLineToken;
use Phug\Lexer\Token\TextToken;
use Phug\Test\AbstractLexerTest;

class FilterScannerTest extends AbstractLexerTest
{
    /**
     * @covers \Phug\Lexer\Scanner\FilterScanner
     * @covers \Phug\Lexer\Scanner\FilterScanner::scan
     */
    public function testFilter()
    {
        list($tok) = $this->assertTokens(':foo bar', [
            FilterToken::class,
            TextToken::class,
        ]);

        self::assertSame('foo', $tok->getName());

        list($tok) = $this->assertTokens(":foo:bar\n  bar", [
            FilterToken::class,
            NewLineToken::class,
            IndentToken::class,
            TextToken::class,
        ]);

        self::assertSame('foo:bar', $tok->getName());
    }
}
