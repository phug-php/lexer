<?php

namespace Phug\Lexer\Token;

use Phug\Lexer\AbstractToken;
use Phug\Util\Partial\BlockTrait;
use Phug\Util\Partial\CheckTrait;
use Phug\Util\Partial\EscapeTrait;
use Phug\Util\Partial\ValueTrait;

class CodeToken extends AbstractToken
{
    use ValueTrait;
    use BlockTrait;
    use EscapeTrait;
    use CheckTrait;
}
