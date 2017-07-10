<?php

namespace Phug\Test\Lexer\Scanner;

use Phug\Lexer;
use Phug\Lexer\Scanner\MarkupScanner;
use Phug\Lexer\State;
use Phug\Lexer\Token\TextToken;
use Phug\Test\AbstractLexerTest;

class MarkupScannerTest extends AbstractLexerTest
{
    /**
     * @covers \Phug\Lexer\Scanner\MarkupScanner
     * @covers \Phug\Lexer\Scanner\MarkupScanner::scan
     */
    public function testRawMarkup()
    {
        $template = '<a></a>';
        /** @var TextToken $tok */
        list($tok) = $this->assertTokens($template, [
            TextToken::class,
        ]);

        self::assertFalse($tok->isEscaped());
        self::assertSame($template, $tok->getValue());

        $template = "<ul id='aa'>\n  <li class='foo'>item</li>\n</ul>";
        /** @var TextToken $tok */
        list($tok) = $this->assertTokens($template, [
            TextToken::class,
        ]);

        self::assertFalse($tok->isEscaped());
        self::assertSame($template, $tok->getValue());
    }

    /**
     * @covers \Phug\Lexer\Scanner\MarkupScanner
     * @covers \Phug\Lexer\Scanner\MarkupScanner::scan
     */
    public function testRawMarkupQuit()
    {
        $state = new State(new Lexer(), 'p', []);
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
