<?php

namespace Phug\Lexer\Token;

use Phug\Lexer\AbstractToken;
use Phug\Util\Partial\CheckTrait;
use Phug\Util\Partial\EscapeTrait;
use Phug\Util\Partial\ValueTrait;

class ExpressionToken extends AbstractToken
{
    use ValueTrait;
    use EscapeTrait;
    use CheckTrait;
}
