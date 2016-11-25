<?php

namespace Phug\Lexer\Token;

use Phug\Lexer\AbstractToken;
use Phug\Lexer\Token\Partial\NameTrait;

class MixinToken extends AbstractToken
{
    use NameTrait;
}
