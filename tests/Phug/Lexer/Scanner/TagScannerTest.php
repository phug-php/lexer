<?php

namespace Phug\Test\Lexer\Scanner;

use Phug\Lexer\Token\ClassToken;
use Phug\Lexer\Token\ExpansionToken;
use Phug\Lexer\Token\TagToken;
use Phug\Test\AbstractLexerTest;

class TagScannerTest extends AbstractLexerTest
{
    /**
     * @covers \Phug\Lexer\Scanner\TagScanner
     * @covers \Phug\Lexer\Scanner\TagScanner::scan
     */
    public function testUsualTagName()
    {
        /** @var TagToken $tok */
        list($tok) = $this->assertTokens('some-tag-name', [
            TagToken::class,
        ]);

        self::assertSame('some-tag-name', $tok->getName());
    }

    /**
     * @covers \Phug\Lexer\Scanner\TagScanner
     * @covers \Phug\Lexer\Scanner\TagScanner::scan
     */
    public function testNamespacedTagName()
    {
        /** @var TagToken $tok */
        list($tok) = $this->assertTokens('some-namespace:some-tag-name', [
            TagToken::class,
        ]);

        self::assertSame('some-namespace:some-tag-name', $tok->getName());
    }

    /**
     * @covers \Phug\Lexer\Scanner\TagScanner
     * @covers \Phug\Lexer\Scanner\TagScanner::scan
     */
    public function testIfScannerConfusesExpansionWithNamespacedTagName()
    {
        /**
         * @var TagToken
         * @var TagToken $b
         */
        list($a, , $b) = $this->assertTokens('some-outer-tag: some-inner-tag', [
            TagToken::class,
            ExpansionToken::class,
            TagToken::class,
        ]);

        self::assertSame('some-outer-tag', $a->getName());
        self::assertSame('some-inner-tag', $b->getName());
    }

    /**
     * @covers \Phug\Lexer\Scanner\TagScanner
     * @covers \Phug\Lexer\Scanner\TagScanner::scan
     */
    public function testTagNameAndClassName()
    {
        /* @var TagToken $tok */
        list($tag, $class) = $this->assertTokens('foo:bar.foo-bar', [
            TagToken::class,
            ClassToken::class,
        ]);

        self::assertSame('foo:bar', $tag->getName());
        self::assertSame('foo-bar', $class->getName());
    }
}
