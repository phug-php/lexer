<?php

namespace Phug\Lexer\Token;

use Phug\Lexer\AbstractToken;
use Phug\Lexer\Token\Partial\FilterTrait;
use Phug\Lexer\Token\Partial\NameTrait;
use Phug\Lexer\Token\Partial\PathTrait;

class ImportToken extends AbstractToken
{
    use NameTrait;
    use PathTrait;
    use FilterTrait;
}
