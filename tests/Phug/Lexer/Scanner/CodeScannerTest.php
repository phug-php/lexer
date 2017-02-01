<?php

namespace Phug\Test\Lexer\Scanner;

use Phug\Lexer\Scanner\CodeScanner;
use Phug\Lexer\State;
use Phug\Lexer\Token\CodeToken;
use Phug\Lexer\Token\IndentToken;
use Phug\Lexer\Token\NewLineToken;
use Phug\Lexer\Token\TagToken;
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

        self::assertSame('$someCode()', $tok->getValue());

        // attached to a tag
        $this->assertTokens("div- foo();", [
            TagToken::class,
            CodeToken::class,
            TextToken::class,
        ]);
    }

    /**
     * @covers Phug\Lexer\Scanner\CodeScanner
     * @covers Phug\Lexer\Scanner\CodeScanner::scan
     */
    public function testCodeBlock()
    {

        /** @var TextToken $tok */
        list(, , , $tok) = $this->assertTokens("-\n  foo();\n  \$bar = 1;", [
            CodeToken::class,
            NewLineToken::class,
            IndentToken::class,
            TextToken::class,
            NewLineToken::class,
            TextToken::class,
        ]);

        self::assertSame('foo();', $tok->getValue());

        $state = new State('p', []);
        $scanners = [
            'tag' => CodeScanner::class,
        ];
        $tokens = [];
        foreach ($state->loopScan($scanners) as $token) {
            $tokens[] = $token;
        }

        self::assertSame(0, count($tokens));

        // attached to a tag
        $this->assertTokens("-\n  foo();\n  \$bar = 1;", [
            CodeToken::class,
            NewLineToken::class,
            IndentToken::class,
            TextToken::class,
            NewLineToken::class,
            TextToken::class,
        ]);
    }
}
