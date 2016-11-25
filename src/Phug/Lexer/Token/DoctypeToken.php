<?php

namespace Phug\Lexer\Token;

use Phug\Lexer\AbstractToken;
use Phug\Lexer\Token\Partial\NameTrait;

class DoctypeToken extends AbstractToken
{
    use NameTrait;
}
