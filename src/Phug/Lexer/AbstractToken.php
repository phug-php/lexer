<?php

namespace Phug\Lexer;

abstract class AbstractToken implements TokenInterface
{

    private $line;
    private $offset;
    private $level;

    public function __construct($line = null, $offset = null, $level = null)
    {

        $this->line = $line ?: 0;
        $this->offset = $offset ?: 0;
        $this->level = $level ?: 0;
    }

    /**
     * @return int
     */
    public function getLine()
    {

        return $this->line;
    }

    /**
     * @return int
     */
    public function getOffset()
    {

        return $this->offset;
    }

    /**
     * @return int
     */
    public function getLevel()
    {

        return $this->level;
    }
}
