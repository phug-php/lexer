<?php

namespace Phug\Lexer\Token;

use Phug\Lexer\AbstractToken;

class ExpansionToken extends AbstractToken
{

    private $space = false;

    public function hasSpace()
    {

        return $this->space;
    }

    public function setHasSpace($space)
    {

        $this->space = $space;

        return $this;
    }
}
