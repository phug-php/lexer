<?php

namespace Phug\Lexer\Scanner;

use Phug\Lexer;
use Phug\Lexer\ScannerInterface;
use Phug\Lexer\State;
use Phug\Lexer\Token\WhenToken;

class WhenScanner extends ControlStatementScanner
{

    public function __construct()
    {

        parent::__construct(
            WhenToken::class,
            ['when', 'default']
        );
    }
}
