<?php

namespace Phug\Test\Lexer\Scanner;

use Phug\Lexer\Token\ConditionalToken;
use Phug\Lexer\Token\ExpansionToken;
use Phug\Lexer\Token\NewLineToken;
use Phug\Lexer\Token\TagToken;
use Phug\Lexer\Token\TextToken;
use Phug\LexerException;

class ConditionalScannerTest extends AbstractControlStatementScannerTest
{

    protected function getTokenClassName()
    {

        return ConditionalToken::class;
    }

    protected function getStatementName()
    {

        return 'if';
    }

    public function provideIfElseExpressions()
    {

        $exprs = $this->provideExpressions();

        $data = [];
        $styles = [
            'elseif',
            'else if',
            'else    if',
            "else\tif",
            "else\t if"
        ];

        foreach ($styles as $style) {
            foreach ($exprs as $expr) {

                $data[] = [$expr[0], $style];
            }
        }

        return $data;
    }

    public function testElseStatement()
    {

        /** @var ConditionalToken $tok */
        list($tok) = $this->assertTokens("else\n", [$this->getTokenClassName(), NewLineToken::class]);
        
        self::assertEquals('else', $tok->getName());
        self::assertEquals(null, $tok->getSubject());
    }

    public function testExpandedElseStatement()
    {

        /** @var ConditionalToken $tok */
        list($tok) = $this->assertTokens('else: p Do something', [
            $this->getTokenClassName(),
            ExpansionToken::class,
            TagToken::class,
            TextToken::class
        ]);

        self::assertEquals('else', $tok->getName());
        self::assertEquals(null, $tok->getSubject());
    }

    public function testThatElseStatementFailsWithSubject()
    {

        self::setExpectedException(LexerException::class);

        /** @var ConditionalToken $tok */
        iterator_to_array($this->lexer->lex('else $someVar'));
    }

    /**
     * @dataProvider provideIfElseExpressions
     */
    public function testElseIfCommonStatementExpressions($expr, $stmt)
    {

        /** @var ConditionalToken $tok */
        list($tok) = $this->assertTokens("$stmt $expr", [$this->getTokenClassName()]);

        self::assertEquals('elseif', $tok->getName());
        self::assertEquals($expr, $tok->getSubject());
    }

    /**
     * @dataProvider provideIfElseExpressions
     */
    public function testElseIfExpandedExpressions($expr, $stmt)
    {


        /** @var ConditionalToken $tok */
        list($tok) = $this->assertTokens("$stmt $expr: p Some Text", [
            $this->getTokenClassName(),
            ExpansionToken::class,
            TagToken::class,
            TextToken::class
        ]);

        self::assertEquals('elseif', $tok->getName());
        self::assertEquals($expr, $tok->getSubject());
    }
}