<?php

namespace Phug\Lexer\Token;

use Phug\Lexer\AbstractToken;
use Phug\Lexer\Token\Partial\PairTrait;
use Phug\Lexer\Token\Partial\SubjectTrait;

class EachToken extends AbstractToken
{
    use SubjectTrait;
    use PairTrait;
}
