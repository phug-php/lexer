<?php

namespace Phug\Test\Lexer\Scanner;

use Phug\Lexer\Token\CaseToken;

class CaseScannerTest extends AbstractControlStatementScannerTest
{
    protected function getTokenClassName()
    {
        return CaseToken::class;
    }

    protected function getStatementName()
    {
        return 'case';
    }

    /**
     * @covers Phug\Lexer\Scanner\CaseScanner::__construct
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
