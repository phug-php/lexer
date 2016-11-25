<?php

namespace Phug\Lexer\Token;

use Phug\Lexer\AbstractToken;
use Phug\Lexer\Token\Partial\CheckTrait;
use Phug\Lexer\Token\Partial\EscapeTrait;
use Phug\Lexer\Token\Partial\ValueTrait;

class ExpressionToken extends AbstractToken
{
    use ValueTrait;
    use EscapeTrait;
    use CheckTrait;
}
