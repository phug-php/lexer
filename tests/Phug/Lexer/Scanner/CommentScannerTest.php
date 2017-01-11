<?php

namespace Phug\Test\Lexer\Scanner;

use Phug\Lexer\Token\CommentToken;
use Phug\Lexer\Token\IndentToken;
use Phug\Lexer\Token\NewLineToken;
use Phug\Lexer\Token\TextToken;
use Phug\Test\AbstractLexerTest;

class CommentScannerTest extends AbstractLexerTest
{
    /**
     * @covers Phug\Lexer\Scanner\CommentScanner
     * @covers Phug\Lexer\Scanner\CommentScanner::scan
     */
    public function testVisibleSingleLineComment()
    {

        /**
         * @var CommentToken
         * @var TextToken    $t
         */
        list($c, $t) = $this->assertTokens('// This is some comment text', [
            CommentToken::class,
            TextToken::class,
        ]);

        self::assertTrue($c->isVisible());
        self::assertSame('This is some comment text', $t->getValue());
    }

    /**
     * @covers Phug\Lexer\Scanner\CommentScanner
     * @covers Phug\Lexer\Scanner\CommentScanner::scan
     */
    public function testInvisibleSingleLineComment()
    {

        /**
         * @var CommentToken
         * @var TextToken    $t
         */
        list($c, $t) = $this->assertTokens('//- This is some comment text', [
            CommentToken::class,
            TextToken::class,
        ]);

        self::assertFalse($c->isVisible());
        self::assertSame('This is some comment text', $t->getValue());
    }

    /**
     * @covers Phug\Lexer\Scanner\CommentScanner
     * @covers Phug\Lexer\Scanner\CommentScanner::scan
     */
    public function testVisibleMultiLineComment()
    {

        /**
         * @var CommentToken
         * @var TextToken    $t1
         * @var TextToken    $t2
         * @var TextToken    $t3
         */
        list($c, , , $t1, , $t2, , $t3) = $this->assertTokens("//\n\tFirst line\n\tSecond line\n\tThird line", [
            CommentToken::class,
            NewLineToken::class,
            IndentToken::class,
            TextToken::class,
            NewLineToken::class,
            TextToken::class,
            NewLineToken::class,
            TextToken::class,
        ]);

        self::assertTrue($c->isVisible());
        self::assertSame('First line', $t1->getValue());
        self::assertSame('Second line', $t2->getValue());
        self::assertSame('Third line', $t3->getValue());
    }

    /**
     * @covers Phug\Lexer\Scanner\CommentScanner
     * @covers Phug\Lexer\Scanner\CommentScanner::scan
     */
    public function testInvisibleMultiLineComment()
    {

        /**
         * @var CommentToken
         * @var TextToken    $t1
         * @var TextToken    $t2
         * @var TextToken    $t3
         */
        list($c, , , $t1, , $t2, , $t3) = $this->assertTokens("//-\n\tFirst line\n\tSecond line\n\tThird line", [
            CommentToken::class,
            NewLineToken::class,
            IndentToken::class,
            TextToken::class,
            NewLineToken::class,
            TextToken::class,
            NewLineToken::class,
            TextToken::class,
        ]);

        self::assertFalse($c->isVisible());
        self::assertSame('First line', $t1->getValue());
        self::assertSame('Second line', $t2->getValue());
        self::assertSame('Third line', $t3->getValue());
    }
}
