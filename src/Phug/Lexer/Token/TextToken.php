<?php

namespace Phug\Lexer\Token;

use Phug\Lexer\AbstractToken;
use Phug\Util\Partial\EscapeTrait;
use Phug\Util\Partial\ValueTrait;

class TextToken extends AbstractToken
{
    use ValueTrait;
    use EscapeTrait;
}
