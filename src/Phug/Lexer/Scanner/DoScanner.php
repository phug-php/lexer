<?php

namespace Phug\Lexer\Scanner;

use Phug\Lexer;
use Phug\Lexer\ScannerInterface;
use Phug\Lexer\State;
use Phug\Lexer\Token\DoToken;

class DoScanner extends ControlStatementScanner
{

    public function __construct()
    {

        parent::__construct(
            DoToken::class,
            ['do']
        );
    }
}
