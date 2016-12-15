<?php

namespace Phug\Lexer\Scanner;

use Phug\Lexer;
use Phug\Lexer\ScannerInterface;
use Phug\Lexer\State;
use Phug\Lexer\Token\ForToken;

class ForScanner extends ControlStatementScanner
{

    public function __construct()
    {

        parent::__construct(
            ForToken::class,
            ['for']
        );
    }
}
