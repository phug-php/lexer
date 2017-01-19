<?php

namespace Phug\Test\Lexer\Scanner;

use Phug\Lexer\Token\ImportToken;
use Phug\Test\AbstractLexerTest;

class ImportScannerTest extends AbstractLexerTest
{
    /**
     * @covers Phug\Lexer\Scanner\ImportScanner
     * @covers Phug\Lexer\Scanner\ImportScanner::scan
     */
    public function testImport()
    {

        /** @var ImportToken $tok */
        list($tok) = $this->assertTokens('extends foo/bar.pug', [
            ImportToken::class,
        ]);

        self::assertSame('extends', $tok->getName());
        self::assertSame('', $tok->getFilter());
        self::assertSame('foo/bar.pug', $tok->getPath());

        /** @var ImportToken $tok */
        list($tok) = $this->assertTokens('include:markdown-it _foo\\bar', [
            ImportToken::class,
        ]);

        self::assertSame('include', $tok->getName());
        self::assertSame('markdown-it', $tok->getFilter());
        self::assertSame('_foo\\bar', $tok->getPath());
    }
}
