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
}