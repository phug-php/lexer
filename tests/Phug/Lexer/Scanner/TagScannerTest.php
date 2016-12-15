<?php

namespace Phug\Test\Lexer\Scanner;

use Phug\Lexer\Token\ExpansionToken;
use Phug\Lexer\Token\TagToken;
use Phug\Test\AbstractLexerTest;

class TagScannerTest extends AbstractLexerTest
{

    /**
     * @covers Phug\Lexer\Scanner\TagScanner
     * @covers Phug\Lexer\Scanner\TagScanner::scan
     */
    public function testUsualTagName()
    {

        /** @var TagToken $tok */
        list($tok) = $this->assertTokens('some-tag-name', [
            TagToken::class
        ]);

        self::assertEquals('some-tag-name', $tok->getName());
    }

    public function testNamespacedTagName()
    {

        /** @var TagToken $tok */
        list($tok) = $this->assertTokens('some-namespace:some-tag-name', [
            TagToken::class
        ]);

        self::assertEquals('some-namespace:some-tag-name', $tok->getName());
    }

    public function testIfScannerConfusesExpansionWithNamespacedTagName()
    {

        /**
         * @var TagToken $a
         * @var TagToken $b
         */
        list($a, , $b) = $this->assertTokens('some-outer-tag: some-inner-tag', [
            TagToken::class,
            ExpansionToken::class,
            TagToken::class
        ]);

        self::assertEquals('some-outer-tag', $a->getName());
        self::assertEquals('some-inner-tag', $b->getName());
    }
}