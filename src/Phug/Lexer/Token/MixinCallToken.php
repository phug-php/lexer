<?php

namespace Phug\Lexer\Token;

use Phug\Lexer\AbstractToken;
use Phug\Lexer\Token\Partial\NameTrait;

class MixinCallToken extends AbstractToken
{
    use NameTrait;
}
