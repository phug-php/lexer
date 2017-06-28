<?php

namespace Phug\Test\Lexer\Scanner;

use Phug\Lexer\Scanner\CommentScanner;
use Phug\Lexer\State;
use Phug\Lexer\Token\CommentToken;
use Phug\Lexer\Token\TextToken;
use Phug\Test\AbstractLexerTest;

class CommentScannerTest extends AbstractLexerTest
{
    /**
     * @covers \Phug\Lexer\Scanner\CommentScanner
     * @covers \Phug\Lexer\Scanner\CommentScanner::scan
     */
    public function testVisibleSingleLineComment()
    {

        /**
         * @var CommentToken $c
         * @var TextToken    $t
         */
        list($c, $t) = $this->assertTokens('// This is some comment text', [
            CommentToken::class,
            TextToken::class,
        ]);

        self::assertTrue($c->isVisible());
        self::assertSame(' This is some comment text', $t->getValue());
    }

    /**
     * @covers \Phug\Lexer\Scanner\CommentScanner
     * @covers \Phug\Lexer\Scanner\CommentScanner::scan
     */
    public function testInvisibleSingleLineComment()
    {

        /**
         * @var CommentToken $c
         * @var TextToken    $t
         */
        list($c, $t) = $this->assertTokens('//- This is some comment text', [
            CommentToken::class,
            TextToken::class,
        ]);

        self::assertFalse($c->isVisible());
        self::assertSame(' This is some comment text', $t->getValue());
    }

    /**
     * @covers \Phug\Lexer\Scanner\CommentScanner
     * @covers \Phug\Lexer\Scanner\CommentScanner::scan
     */
    public function testVisibleMultiLineComment()
    {

        /**
         * @var CommentToken $c
         * @var TextToken    $t
         */
        list($c, $t) = $this->assertTokens("//\n\tFirst line\n\tSecond line\n\tThird line", [
            CommentToken::class,
            TextToken::class,
        ]);

        self::assertTrue($c->isVisible());
        self::assertSame(
            "\n\tFirst line\n\tSecond line\n\tThird line",
            $t->getValue()
        );
    }

    /**
     * @covers \Phug\Lexer\Scanner\CommentScanner
     * @covers \Phug\Lexer\Scanner\CommentScanner::scan
     */
    public function testInvisibleMultiLineComment()
    {

        /**
         * @var CommentToken $c
         * @var TextToken    $t
         */
        list($c, $t) = $this->assertTokens("//-\n\tFirst line\n\tSecond line\n\tThird line", [
            CommentToken::class,
            TextToken::class,
        ]);

        self::assertFalse($c->isVisible());
        self::assertSame(
            "\n\tFirst line\n\tSecond line\n\tThird line",
            $t->getValue()
        );
    }

    /**
     * @covers \Phug\Lexer\Scanner\CommentScanner
     * @covers \Phug\Lexer\Scanner\CommentScanner::scan
     */
    public function testCommentQuit()
    {
        $state = new State('p', []);
        $scanners = [
            'comment' => CommentScanner::class,
        ];
        $tokens = [];
        foreach ($state->loopScan($scanners) as $token) {
            $tokens[] = $token;
        }

        self::assertSame(0, count($tokens));
    }
}
