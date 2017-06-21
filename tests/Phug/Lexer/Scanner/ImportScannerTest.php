<?php

namespace Phug\Test\Lexer\Scanner;

use Phug\Lexer\Token\AttributeEndToken;
use Phug\Lexer\Token\AttributeStartToken;
use Phug\Lexer\Token\AttributeToken;
use Phug\Lexer\Token\FilterToken;
use Phug\Lexer\Token\ImportToken;
use Phug\Test\AbstractLexerTest;

class ImportScannerTest extends AbstractLexerTest
{
    /**
     * @covers \Phug\Lexer\Scanner\ImportScanner
     * @covers \Phug\Lexer\Scanner\ImportScanner::scan
     */
    public function testImport()
    {
        /** @var ImportToken $tok */
        list($tok) = $this->assertTokens('extend foo/bar.pug', [
            ImportToken::class,
        ]);

        self::assertSame('extend', $tok->getName());
        self::assertSame('foo/bar.pug', $tok->getPath());

        /** @var ImportToken $tok */
        list($tok) = $this->assertTokens('extends foo/bar.pug', [
            ImportToken::class,
        ]);

        self::assertSame('extend', $tok->getName());
        self::assertSame('foo/bar.pug', $tok->getPath());

        /** @var ImportToken $tok */
        list($tok) = $this->assertTokens('include:markdown-it _foo\\bar', [
            ImportToken::class,
            FilterToken::class,
        ]);

        self::assertSame('include', $tok->getName());
        self::assertSame('_foo\\bar', $tok->getPath());

        /** @var ImportToken $tok */
        /** @var FilterToken $filter */
        list($tok, $filter) = $this->assertTokens('includes:markdown-it _foo\\bar', [
            ImportToken::class,
            FilterToken::class,
        ]);

        self::assertSame('include', $tok->getName());
        self::assertSame('markdown-it', $filter->getName());
        self::assertSame('_foo\\bar', $tok->getPath());

        /** @var ImportToken $tok */
        /** @var FilterToken $filter */
        list($tok, $filter) = $this->assertTokens('includes:markdown-it(option="(aa)") _foo\\bar', [
            ImportToken::class,
            FilterToken::class,
            AttributeStartToken::class,
            AttributeToken::class,
            AttributeEndToken::class,
        ]);

        self::assertSame('include', $tok->getName());
        self::assertSame('markdown-it', $filter->getName());
        self::assertSame('_foo\\bar', $tok->getPath());
    }
}
