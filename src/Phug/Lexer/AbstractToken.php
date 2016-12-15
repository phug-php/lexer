<?php

namespace Phug\Lexer;

use Phug\Util\Partial\DocumentLocationTrait;
use Phug\Util\Partial\LevelGetTrait;

abstract class AbstractToken implements TokenInterface
{
    use DocumentLocationTrait;
    use LevelGetTrait;

    public function __construct($line = null, $offset = null, $level = null)
    {

        $this->line = $line ?: 0;
        $this->offset = $offset ?: 0;
        $this->level = $level ?: 0;
    }
}
