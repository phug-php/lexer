<?php

namespace Phug\Test\Lexer\Scanner;

use Phug\Lexer\Token\AttributeEndToken;
use Phug\Lexer\Token\AttributeStartToken;
use Phug\Lexer\Token\AttributeToken;
use Phug\LexerException;
use Phug\Test\AbstractLexerTest;

class AttributeScannerTest extends AbstractLexerTest
{
    /**
     * @covers Phug\Lexer\Scanner\AttributeScanner
     * @covers Phug\Lexer\Scanner\AttributeScanner::scan
     */
    public function testScan()
    {
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
     * @covers Phug\Lexer\Scanner\AttributeScanner
     * @covers Phug\Lexer\Scanner\AttributeScanner::scan
     */
    public function testFailsOnUnclosedBracket()
    {
        $this->setExpectedException(LexerException::class);
        iterator_to_array($this->lexer->lex('(a=b'));
    }

    /**
     * @covers Phug\Lexer\Scanner\AttributeScanner
     * @covers Phug\Lexer\Scanner\AttributeScanner::scan
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
}
