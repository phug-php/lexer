<?php

namespace Phug\Test\Lexer\Scanner;

use Phug\Lexer\Token\WhileToken;

class WhileScannerTest extends AbstractControlStatementScannerTest
{
    protected function getTokenClassName()
    {
        return WhileToken::class;
    }

    protected function getStatementName()
    {
        return 'while';
    }
}
