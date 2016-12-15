<?php

namespace Phug\Lexer\Scanner;

use Phug\Lexer;
use Phug\Lexer\ScannerInterface;
use Phug\Lexer\State;
use Phug\Lexer\Token\CaseToken;

class CaseScanner extends ControlStatementScanner
{

    public function __construct()
    {

        parent::__construct(
            CaseToken::class,
            ['case']
        );
    }
}
