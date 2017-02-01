<?php

namespace Phug\Lexer\Token;

use Phug\Lexer\AbstractToken;
use Phug\Util\Partial\BlockTrait;
use Phug\Util\Partial\CheckTrait;
use Phug\Util\Partial\EscapeTrait;

class CodeToken extends AbstractToken
{
    use BlockTrait;
}
