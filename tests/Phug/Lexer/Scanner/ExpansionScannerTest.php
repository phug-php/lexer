<?php

namespace Phug\Test\Lexer\Scanner;

use Phug\Lexer\Token\ExpansionToken;
use Phug\Lexer\Token\FilterToken;
use Phug\Lexer\Token\TagToken;
use Phug\Test\AbstractLexerTest;

class ExpansionScannerTest extends AbstractLexerTest
{

    /**
     * @covers Phug\Lexer\Scanner\ExpansionScanner
     * @covers Phug\Lexer\Scanner\ExpansionScanner::scan
     */
    public function testStandaloneExpansion()
    {

        $this->assertTokens(':', [
            ExpansionToken::class
        ]);
    }

    /**
     * @covers Phug\Lexer\Scanner\ExpansionScanner
     * @covers Phug\Lexer\Scanner\ExpansionScanner::scan
     */
    public function testTagExpansion()
    {

        /** @var TagToken $tok */
        list($tok) = $this->assertTokens('some-tag:', [
            TagToken::class,
            ExpansionToken::class
        ]);
        self::assertEquals('some-tag', $tok->getName());

        list($tok) = $this->assertTokens('some:namespaced:tag:', [
            TagToken::class,
            ExpansionToken::class
        ]);
        self::assertEquals('some:namespaced:tag', $tok->getName());
    }

    /**
     * @covers Phug\Lexer\Scanner\ExpansionScanner
     * @covers Phug\Lexer\Scanner\ExpansionScanner::scan
     */
    public function testFilterExpansion()
    {

        /** @var FilterToken $tok */
        list($tok) = $this->assertTokens(':some-filter:', [
            FilterToken::class,
            ExpansionToken::class
        ]);
        self::assertEquals('some-filter', $tok->getName());

        list($tok) = $this->assertTokens(':some:namespaced:filter:', [
            FilterToken::class,
            ExpansionToken::class
        ]);
        self::assertEquals('some:namespaced:filter', $tok->getName());
    }
}