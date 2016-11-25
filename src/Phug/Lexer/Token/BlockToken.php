<?php

namespace Phug\Lexer\Token;

use Phug\Lexer\AbstractToken;
use Phug\Lexer\Token\Partial\ModeTrait;
use Phug\Lexer\Token\Partial\NameTrait;

class BlockToken extends AbstractToken
{
    use NameTrait;
    use ModeTrait;
}
