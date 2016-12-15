<?php

namespace Phug\Lexer;

use Phug\Util\DocumentLocationInterface;

interface TokenInterface extends DocumentLocationInterface
{
    
    public function getLevel();
}
