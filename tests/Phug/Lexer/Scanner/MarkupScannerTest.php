<?php

namespace Phug\Test\Lexer\Scanner;

use Phug\Lexer\Scanner\MarkupScanner;
use Phug\Lexer\State;
use Phug\Lexer\Token\TextToken;
use Phug\Test\AbstractLexerTest;

class MarkupScannerTest extends AbstractLexerTest
{
    /**
     * @covers Phug\Lexer\Scanner\MarkupScanner
     * @covers Phug\Lexer\Scanner\MarkupScanner::scan
     */
    public function testRawMarkup()
    {
        /** @var TagToken $tok */
        list($tok) = $this->assertTokens('<a></a>', [
            TextToken::class,
        ]);

        self::assertFalse($tok->isEscaped());
        self::assertSame('<a></a>', $tok->getValue());
    }

    /**
     * @covers Phug\Lexer\Scanner\MarkupScanner
     * @covers Phug\Lexer\Scanner\MarkupScanner::scan
     */
    public function testRawMarkupQuit()
    {
        $state = new State('p', []);
        $scanners = [
            'markup' => MarkupScanner::class,
        ];
        $tokens = [];
        foreach ($state->loopScan($scanners) as $token) {
            $tokens[] = $token;
        }

        self::assertSame(0, count($tokens));
    }
}
