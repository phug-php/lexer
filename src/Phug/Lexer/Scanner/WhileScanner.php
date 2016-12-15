<?php

namespace Phug\Lexer\Scanner;

use Phug\Lexer;
use Phug\Lexer\ScannerInterface;
use Phug\Lexer\State;
use Phug\Lexer\Token\WhileToken;

class WhileScanner extends ControlStatementScanner
{

    public function __construct()
    {

        parent::__construct(
            WhileToken::class,
            ['while']
        );
    }
}
