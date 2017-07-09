<?php

namespace Phug\Lexer;

use Phug\Util\Partial\DocumentLocationTrait;
use Phug\Util\Partial\LevelGetTrait;

abstract class AbstractToken implements TokenInterface
{
    use DocumentLocationTrait;
    use LevelGetTrait;

    private $offsetLength = 0;
    private $indent;

    public function __construct($line = null, $offset = null, $level = null, $indent = null)
    {
        $this->line = $line ?: 0;
        $this->offset = $offset ?: 0;
        $this->level = $level ?: 0;
        $this->indent = $indent;
    }

    /**
     * @return int
     */
    public function getOffsetLength()
    {
        return $this->offsetLength;
    }

    /**
     * @param int $offsetLength
     *
     * @return $this
     */
    public function setOffsetLength($offsetLength)
    {
        $this->offsetLength = $offsetLength;

        return $this;
    }

    public function getIndent()
    {
        return $this->indent;
    }
}
