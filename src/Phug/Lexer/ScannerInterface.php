<?php

namespace Phug\Lexer;

use Phug\Lexer;

interface ScannerInterface
{

    public function scan(State $state);
}
