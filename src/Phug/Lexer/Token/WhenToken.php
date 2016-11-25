<?php

namespace Phug\Lexer\Token;

use Phug\Lexer\AbstractToken;
use Phug\Lexer\Token\Partial\NameTrait;
use Phug\Lexer\Token\Partial\SubjectTrait;

class WhenToken extends AbstractToken
{
    use NameTrait;
    use SubjectTrait;
}
