<?php

namespace Phug\Test\Lexer\Scanner;

use Phug\Lexer\Token\ConditionalToken;

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
}