<?php

namespace Phug\Lexer\Token;

use Phug\Lexer\AbstractToken;
use Phug\Lexer\Token\Partial\CheckTrait;
use Phug\Lexer\Token\Partial\EscapeTrait;
use Phug\Lexer\Token\Partial\NameTrait;
use Phug\Lexer\Token\Partial\ValueTrait;

class AttributeToken extends AbstractToken
{
    use NameTrait;
    use ValueTrait;
    use EscapeTrait;
    use CheckTrait;
}
