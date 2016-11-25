<?php

namespace Phug\Lexer\Token;

use Phug\Lexer\AbstractToken;
use Phug\Lexer\Token\Partial\BlockTrait;
use Phug\Lexer\Token\Partial\ValueTrait;

class CodeToken extends AbstractToken
{
    use ValueTrait;
    use BlockTrait;
}
