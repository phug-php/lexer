<?php

namespace Phug\Test;

use Phug\Lexer;
use Phug\Lexer\Token\AssignmentToken;
use Phug\Lexer\Token\AttributeEndToken;
use Phug\Lexer\Token\AttributeStartToken;
use Phug\Lexer\Token\AttributeToken;
use Phug\Lexer\Token\DoctypeToken;
use Phug\LexerException;

/**
 * @coversDefaultClass Phug\Lexer
 */
class LexerTest extends AbstractLexerTest
{

    /**
     * @covers ::lex
     */
    public function testAssignmentScan()
    {

        $this->assertTokens('&test', [
            AssignmentToken::class
        ]);
    }

    /**
     * @covers ::lex
     */

    public function testAttributeScan()
    {

        $this->assertTokens('(a=b c=d e=f)', [
            AttributeStartToken::class,
            AttributeToken::class,
            AttributeToken::class,
            AttributeToken::class,
            AttributeEndToken::class
        ]);

        $this->assertTokens('(a=b,c=d, e=f)', [
            AttributeStartToken::class,
            AttributeToken::class,
            AttributeToken::class,
            AttributeToken::class,
            AttributeEndToken::class
        ]);

        $this->assertTokens(
            '(a=b
        c=d     e=f
        //ignored line
    ,g=h        )', [
            AttributeStartToken::class,
            AttributeToken::class,
            AttributeToken::class,
            AttributeToken::class,
            AttributeToken::class,
            AttributeEndToken::class
        ]);


        $this->assertTokens(
            '(
                a//ignore
                b //ignore
                c//ignore
                =d
                e=//ignore
                f//ignore
                g=h//ignore
            )', [
            AttributeStartToken::class,
            AttributeToken::class,
            AttributeToken::class,
            AttributeToken::class,
            AttributeToken::class,
            AttributeToken::class,
            AttributeEndToken::class
        ]);

        $this->expectException(LexerException::class);
        iterator_to_array($this->lexer->lex('(a=b'));
    }

    /**
     * @covers ::lex
     */
    public function testAttributeDetailScan()
    {

        /** @var AttributeToken $attr */
        $attr = iterator_to_array($this->lexer->lex('(a=b)'))[1];
        $this->assertEquals('a', $attr->getName());
        $this->assertEquals('b', $attr->getValue());
        $this->assertEquals(true, $attr->isEscaped());
        $this->assertEquals(true, $attr->isChecked());

        /** @var AttributeToken $attr */
        $attr = iterator_to_array($this->lexer->lex('(a!=b)'))[1];
        $this->assertEquals('a', $attr->getName());
        $this->assertEquals('b', $attr->getValue());
        $this->assertEquals(false, $attr->isEscaped());
        $this->assertEquals(true, $attr->isChecked());

        /** @var AttributeToken $attr */
        $attr = iterator_to_array($this->lexer->lex('(a?=b)'))[1];
        $this->assertEquals('a', $attr->getName());
        $this->assertEquals('b', $attr->getValue());
        $this->assertEquals(true, $attr->isEscaped());
        $this->assertEquals(false, $attr->isChecked());

        /** @var AttributeToken $attr */
        $attr = iterator_to_array($this->lexer->lex('(a?!=b)'))[1];
        $this->assertEquals('a', $attr->getName());
        $this->assertEquals('b', $attr->getValue());
        $this->assertEquals(false, $attr->isEscaped());
        $this->assertEquals(false, $attr->isChecked());
    }

    /**
     * @covers ::lex
     */
    public function testDoctypeScan()
    {

        $this->assertTokens(
            'doctype 5',
            [DoctypeToken::class]
        );

        $this->assertTokens(
            '!!! 5',
            [DoctypeToken::class]
        );
    }
}
