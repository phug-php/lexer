<?php

namespace Phug\Lexer;

interface TokenInterface
{

    public function getLine();
    public function getOffset();
    public function getLevel();
}
