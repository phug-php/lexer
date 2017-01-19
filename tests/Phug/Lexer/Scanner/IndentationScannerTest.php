<?php

namespace Phug\Test\Lexer\Scanner;

use Phug\Lexer\Scanner\IndentationScanner;
use Phug\Lexer\Scanner\NewLineScanner;
use Phug\Lexer\Scanner\TagScanner;
use Phug\Lexer\State;
use Phug\Lexer;
use Phug\Lexer\Token\IndentToken;
use Phug\Lexer\Token\NewLineToken;
use Phug\Lexer\Token\OutdentToken;
use Phug\Lexer\Token\TagToken;
use Phug\Test\AbstractLexerTest;

class IndentationScannerTest extends AbstractLexerTest
{
    /**
     * @covers Phug\Lexer\Scanner\IndentationScanner
     * @covers Phug\Lexer\Scanner\IndentationScanner::scan
     */
    public function testIndentation()
    {

        $this->assertTokens('  ', [
            IndentToken::class,
        ]);
        $this->assertTokens("  \n", [
            NewLineToken::class,
        ]);
    }

    /**
     * @covers Phug\Lexer\Scanner\IndentationScanner
     * @covers Phug\Lexer\Scanner\IndentationScanner::scan
     */
    public function testIndentationQuit()
    {

        $state = new State('p', []);
        $scanners = [
            'indent' => IndentationScanner::class,
        ];
        $tokens = [];
        foreach ($state->loopScan($scanners) as $token) {
            $tokens[] = $token;
        }

        self::assertSame(0, count($tokens));

        $state = new State("p\t\t", []);
        $scanners = [
            'tag'    => TagScanner::class,
            'indent' => IndentationScanner::class,
        ];
        $tokens = [];
        foreach ($state->loopScan($scanners) as $token) {
            $tokens[] = $token;
        }

        self::assertSame(1, count($tokens));
        self::assertInstanceOf(TagToken::class, $tokens[0]);
    }

    /**
     * @covers Phug\Lexer\Scanner\IndentationScanner
     * @covers Phug\Lexer\Scanner\IndentationScanner::scan
     */
    public function testMixedIndentation()
    {

        $this->assertTokens("div\n    \tp\n\t    a\nfooter", [
            TagToken::class,
            NewLineToken::class,
            IndentToken::class,
            TagToken::class,
            NewLineToken::class,
            TagToken::class,
            NewLineToken::class,
            OutdentToken::class,
            TagToken::class,
        ]);

        $lexer = new Lexer([
            'indent_style' => Lexer::INDENT_TAB,
            'indent_width' => 1,
        ]);
        $gen = $lexer->lex("div\n\t  p\n\t  a\nfooter");
        $tokensClasses = [];
        foreach ($gen as $token) {
            $tokensClasses[] = get_class($token);
        }

        self::assertSame([
            TagToken::class,
            NewLineToken::class,
            IndentToken::class,
            TagToken::class,
            NewLineToken::class,
            TagToken::class,
            NewLineToken::class,
            OutdentToken::class,
            TagToken::class,
        ], $tokensClasses);
    }
}
