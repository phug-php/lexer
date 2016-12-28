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
}
