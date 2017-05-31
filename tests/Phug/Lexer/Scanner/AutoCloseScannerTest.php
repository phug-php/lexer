<?php

namespace Phug\Test\Lexer\Scanner;

use Phug\Lexer\Token\AssignmentToken;
use Phug\Lexer\Token\AttributeEndToken;
use Phug\Lexer\Token\AttributeStartToken;
use Phug\Lexer\Token\AttributeToken;
use Phug\Lexer\Token\AutoCloseToken;
use Phug\Lexer\Token\ClassToken;
use Phug\Lexer\Token\IdToken;
use Phug\Lexer\Token\TagToken;
use Phug\Lexer\Token\TextToken;
use Phug\Test\AbstractLexerTest;

class AutoCloseScannerTest extends AbstractLexerTest
{
    /**
     * @covers \Phug\Lexer\Scanner\BlockScanner
     * @covers \Phug\Lexer\Scanner\BlockScanner::scan
     * @group i
     */
    public function testScan()
    {
        $this->assertTokens('link/', [
            TagToken::class,
            AutoCloseToken::class,
        ]);
        $this->assertTokens('div()/', [
            TagToken::class,
            AttributeStartToken::class,
            AttributeEndToken::class,
            AutoCloseToken::class,
        ]);
        $this->assertTokens('div(foo="bar")/', [
            TagToken::class,
            AttributeStartToken::class,
            AttributeToken::class,
            AttributeEndToken::class,
            AutoCloseToken::class,
        ]);
        $this->assertTokens('div&attributes($foo)/', [
            TagToken::class,
            AssignmentToken::class,
            AttributeStartToken::class,
            AttributeToken::class,
            AttributeEndToken::class,
            AutoCloseToken::class,
        ]);
        $this->assertTokens('div.foo/', [
            TagToken::class,
            ClassToken::class,
            AutoCloseToken::class,
        ]);
        $this->assertTokens('div(foo="bar").foo#bar/', [
            TagToken::class,
            AttributeStartToken::class,
            AttributeToken::class,
            AttributeEndToken::class,
            ClassToken::class,
            IdToken::class,
            AutoCloseToken::class,
        ]);

        $this->assertTokens('link /', [
            TagToken::class,
            TextToken::class,
        ]);
        $this->assertTokens('div() /', [
            TagToken::class,
            AttributeStartToken::class,
            AttributeEndToken::class,
            TextToken::class,
        ]);
        $this->assertTokens('div(foo="bar") /', [
            TagToken::class,
            AttributeStartToken::class,
            AttributeToken::class,
            AttributeEndToken::class,
            TextToken::class,
        ]);
        $this->assertTokens('div&attributes($foo) /', [
            TagToken::class,
            AssignmentToken::class,
            AttributeStartToken::class,
            AttributeToken::class,
            AttributeEndToken::class,
            TextToken::class,
        ]);
        $this->assertTokens('div.foo /', [
            TagToken::class,
            ClassToken::class,
            TextToken::class,
        ]);
        $this->assertTokens('div(foo="bar").foo#bar /', [
            TagToken::class,
            AttributeStartToken::class,
            AttributeToken::class,
            AttributeEndToken::class,
            ClassToken::class,
            IdToken::class,
            TextToken::class,
        ]);
    }
}
