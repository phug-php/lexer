<?php

namespace Phug\Test\Lexer\Scanner;

use Phug\Lexer\Token\CodeToken;
use Phug\Lexer\Token\TextToken;
use Phug\Test\AbstractLexerTest;

class CodeScannerTest extends AbstractLexerTest
{
    /**
     * @covers Phug\Lexer\Scanner\CodeScanner
     * @covers Phug\Lexer\Scanner\CodeScanner::scan
     */
    public function testSingleLineCode()
    {

        /** @var TextToken $tok */
        list(, $tok) = $this->assertTokens('- $someCode()', [
            CodeToken::class,
            TextToken::class,
        ]);

        self::assertEquals('$someCode()', $tok->getValue());
    }
}
