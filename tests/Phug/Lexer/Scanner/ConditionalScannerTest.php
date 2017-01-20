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

    /**
     * @covers Phug\Lexer\Scanner\ConditionalScanner::__construct
     * @covers Phug\Lexer\Scanner\ControlStatementScanner
     * @covers Phug\Lexer\Scanner\ControlStatementScanner::__construct
     * @covers Phug\Lexer\Scanner\ControlStatementScanner::scan
     * @covers Phug\Lexer\Scanner\Partial\NamespaceAndTernaryTrait::checkForTernary
     * @covers Phug\Lexer\Scanner\Partial\NamespaceAndTernaryTrait::checkForNamespaceAndTernary
     * @dataProvider provideExpressions
     */
    public function testExpandedExpressions($expr)
    {
        parent::testExpandedExpressions($expr);
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
            "else\t if",
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

        self::assertSame('else', $tok->getName());
        self::assertSame(null, $tok->getSubject());
    }

    public function testExpandedElseStatement()
    {

        /** @var ConditionalToken $tok */
        list($tok) = $this->assertTokens('else: p Do something', [
            $this->getTokenClassName(),
            ExpansionToken::class,
            TagToken::class,
            TextToken::class,
        ]);

        self::assertSame('else', $tok->getName());
        self::assertSame(null, $tok->getSubject());
    }

    public function testThatElseStatementFailsWithSubject()
    {
        self::setExpectedException(LexerException::class);

        /* @var ConditionalToken $tok */
        iterator_to_array($this->lexer->lex('else $someVar'));
    }

    /**
     * @dataProvider provideIfElseExpressions
     */
    public function testElseIfCommonStatementExpressions($expr, $stmt)
    {

        /** @var ConditionalToken $tok */
        list($tok) = $this->assertTokens("$stmt $expr", [$this->getTokenClassName()]);

        self::assertSame('elseif', $tok->getName());
        self::assertSame($expr, $tok->getSubject());
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
            TextToken::class,
        ]);

        self::assertSame('elseif', $tok->getName());
        self::assertSame($expr, $tok->getSubject());
    }
}
