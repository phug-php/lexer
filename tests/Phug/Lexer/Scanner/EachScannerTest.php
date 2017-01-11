<?php

namespace Phug\Test\Lexer\Scanner;

use Phug\Lexer\Token\EachToken;
use Phug\Lexer\Token\ExpansionToken;
use Phug\Lexer\Token\TagToken;
use Phug\Lexer\Token\TextToken;
use Phug\LexerException;
use Phug\Test\AbstractLexerTest;

abstract class EachScannerTest extends AbstractLexerTest
{
    public function provideExpressions()
    {
        return [
            ['$someSubject'],
            ['$a ? $b : $c'],
            ['Foo::$bar'],
            ['Foo::bar()'],
            ['$a ? $b : ($c ? $d : $e)'],
            ['($some ? $ternary : $operator)'],
        ];
    }

    public function provideInvalidSyntaxStyles()
    {
        return [
            ['each item, key in $something'],
            ['each item in $something'],
            ['each anything'],
            ['each $something'],
            ['each $item, anything in $something'],
            ['each $item, $key'],
            ['each $item, $key in'],
        ];
    }

    /**
     * @dataProvider provideExpressions
     */
    public function testWithItemOnly($expr)
    {

        /** @var EachToken $tok */
        list($tok) = $this->assertTokens("each \$item in $expr", [EachToken::class]);

        self::assertSame('item', $tok->getItem());
        self::assertSame($expr, $tok->getSubject());
    }

    /**
     * @dataProvider provideExpressions
     */
    public function testWithItemAndKey($expr)
    {

        /** @var EachToken $tok */
        list($tok) = $this->assertTokens("each \$someItem, \$someKey in $expr", [EachToken::class]);

        self::assertSame('someItem', $tok->getItem());
        self::assertSame('someKey', $tok->getKey());
        self::assertSame($expr, $tok->getSubject());
    }

    /**
     * @dataProvider provideExpressions
     */
    public function testExpandedWithItemOnly($expr)
    {

        /** @var EachToken $tok */
        list($tok) = $this->assertTokens("each \$item in $expr: p Some Text", [
            EachToken::class,
            ExpansionToken::class,
            TagToken::class,
            TextToken::class,
        ]);

        self::assertSame('item', $tok->getItem());
        self::assertSame($expr, $tok->getSubject());
    }

    /**
     * @dataProvider provideExpressions
     */
    public function testExpandedWithItemAndKey($expr)
    {

        /** @var EachToken $tok */
        list($tok) = $this->assertTokens("each \$someItem, \$someKey in $expr: p Some Text", [
            EachToken::class,
            ExpansionToken::class,
            TagToken::class,
            TextToken::class,
        ]);

        self::assertSame('someItem', $tok->getItem());
        self::assertSame('someKey', $tok->getKey());
        self::assertSame($expr, $tok->getSubject());
    }

    /**
     * @dataProvider provideInvalidSyntaxStyles
     */
    public function testThatItFailsWithInvalidSyntax($syntax)
    {
        self::setExpectedException(LexerException::class);
        $this->lexer->lex($syntax);
    }
}
