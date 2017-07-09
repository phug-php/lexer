<?php

namespace Phug\Lexer;

use Phug\Util\DocumentLocationInterface;

interface TokenInterface extends DocumentLocationInterface
{
    public function getOffsetLength();

    public function setOffsetLength($offsetLength);

    public function getLevel();

    public function getIndent();
}
