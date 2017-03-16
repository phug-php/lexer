<?php

namespace Phug\Test\Lexer\Scanner;

use Phug\Lexer\Scanner\AttributeScanner;
use Phug\Lexer\State;
use Phug\Lexer\Token\AttributeEndToken;
use Phug\Lexer\Token\AttributeStartToken;
use Phug\Lexer\Token\AttributeToken;
use Phug\Lexer\Token\ClassToken;
use Phug\Lexer\Token\IdToken;
use Phug\Lexer\Token\TextToken;
use Phug\LexerException;
use Phug\Test\AbstractLexerTest;

class AttributeScannerTest extends AbstractLexerTest
{
    /**
     * @covers \Phug\Lexer\Scanner\AttributeScanner
     * @covers \Phug\Lexer\Scanner\AttributeScanner::scan
     */
    public function testScan()
    {
        $this->assertTokens('()', [
            AttributeStartToken::class,
            AttributeEndToken::class,
        ]);

        $this->assertTokens('(a=b c=d e=f)', [
            AttributeStartToken::class,
            AttributeToken::class,
            AttributeToken::class,
            AttributeToken::class,
            AttributeEndToken::class,
        ]);

        $this->assertTokens('(a=b,c=d, e=f)', [
            AttributeStartToken::class,
            AttributeToken::class,
            AttributeToken::class,
            AttributeToken::class,
            AttributeEndToken::class,
        ]);

        $this->assertTokens('(a=b c=d e=f)#foo', [
            AttributeStartToken::class,
            AttributeToken::class,
            AttributeToken::class,
            AttributeToken::class,
            AttributeEndToken::class,
            IdToken::class,
        ]);

        $this->assertTokens('(a=b c=d e=f).foo', [
            AttributeStartToken::class,
            AttributeToken::class,
            AttributeToken::class,
            AttributeToken::class,
            AttributeEndToken::class,
            ClassToken::class,
        ]);

        $this->assertTokens('(a=b c=d e=f). foo', [
            AttributeStartToken::class,
            AttributeToken::class,
            AttributeToken::class,
            AttributeToken::class,
            AttributeEndToken::class,
            TextToken::class,
        ]);

        $this->assertTokens(
            '(a=b
        c=d     e=f
        //ignored line
    ,g=h        )',
            [
                AttributeStartToken::class,
                AttributeToken::class,
                AttributeToken::class,
                AttributeToken::class,
                AttributeToken::class,
                AttributeEndToken::class,
            ]
        );

        $this->assertTokens(
            '(
                a//ignore
                b //ignore
                c//ignore
                =d
                e=//ignore
                f//ignore
                g=h//ignore
            )',
            [
                AttributeStartToken::class,
                AttributeToken::class,
                AttributeToken::class,
                AttributeToken::class,
                AttributeToken::class,
                AttributeToken::class,
                AttributeEndToken::class,
            ]
        );
    }

    /**
     * @covers \Phug\Lexer\Scanner\AttributeScanner
     * @covers \Phug\Lexer\Scanner\AttributeScanner::scan
     */
    public function testFailsOnUnclosedBracket()
    {
        $this->setExpectedException(LexerException::class);
        iterator_to_array($this->lexer->lex('(a=b'));
    }

    /**
     * @covers \Phug\Lexer\Scanner\AttributeScanner
     * @covers \Phug\Lexer\Scanner\AttributeScanner::scan
     */
    public function testDetailedScan()
    {
        /** @var AttributeToken $attr */
        list(, $attr) = $this->assertTokens('(a=b)', [
            AttributeStartToken::class,
            AttributeToken::class,
            AttributeEndToken::class,
        ]);
        $this->assertSame('a', $attr->getName());
        $this->assertSame('b', $attr->getValue());
        $this->assertSame(true, $attr->isEscaped());
        $this->assertSame(true, $attr->isChecked());

        /** @var AttributeToken $attr */
        list(, $attr) = $this->assertTokens('(a!=b)', [
            AttributeStartToken::class,
            AttributeToken::class,
            AttributeEndToken::class,
        ]);
        $this->assertSame('a', $attr->getName());
        $this->assertSame('b', $attr->getValue());
        $this->assertSame(false, $attr->isEscaped());
        $this->assertSame(true, $attr->isChecked());

        /** @var AttributeToken $attr */
        list(, $attr) = $this->assertTokens('(a?=b)', [
            AttributeStartToken::class,
            AttributeToken::class,
            AttributeEndToken::class,
        ]);
        $this->assertSame('a', $attr->getName());
        $this->assertSame('b', $attr->getValue());
        $this->assertSame(true, $attr->isEscaped());
        $this->assertSame(false, $attr->isChecked());

        /** @var AttributeToken $attr */
        list(, $attr) = $this->assertTokens('(a?!=b)', [
            AttributeStartToken::class,
            AttributeToken::class,
            AttributeEndToken::class,
        ]);
        $this->assertSame('a', $attr->getName());
        $this->assertSame('b', $attr->getValue());
        $this->assertSame(false, $attr->isEscaped());
        $this->assertSame(false, $attr->isChecked());
    }

    /**
     * @covers \Phug\Lexer\Scanner\AttributeScanner
     * @covers \Phug\Lexer\Scanner\AttributeScanner::scan
     */
    public function testAttributeQuit()
    {
        $state = new State('p', []);
        $scanners = [
            'attribute' => AttributeScanner::class,
        ];
        $tokens = [];
        foreach ($state->loopScan($scanners) as $token) {
            $tokens[] = $token;
        }

        self::assertSame(0, count($tokens));
    }
}
