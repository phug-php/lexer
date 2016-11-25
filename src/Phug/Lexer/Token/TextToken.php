<?php

namespace Phug\Lexer\Token;

use Phug\Lexer\AbstractToken;
use Phug\Lexer\Token\Partial\EscapeTrait;
use Phug\Lexer\Token\Partial\ValueTrait;

class TextToken extends AbstractToken
{
    use ValueTrait;
    use EscapeTrait;
}
