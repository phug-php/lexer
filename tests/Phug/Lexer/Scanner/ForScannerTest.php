<?php

namespace Phug\Test\Lexer\Scanner;

use Phug\Lexer\Token\ForToken;

class ForScannerTest extends AbstractControlStatementScannerTest
{
    protected function getTokenClassName()
    {
        return ForToken::class;
    }

    protected function getStatementName()
    {
        return 'for';
    }

    /**
     * @covers Phug\Lexer\Scanner\ForScanner::__construct
     * @covers Phug\Lexer\Scanner\ControlStatementScanner
     * @covers Phug\Lexer\Scanner\ControlStatementScanner::__construct
     * @covers Phug\Lexer\Scanner\ControlStatementScanner::scan
     * @dataProvider provideExpressions
     */
    public function testExpandedExpressions($expr)
    {
        parent::testExpandedExpressions($expr);
    }
}
