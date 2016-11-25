<?php

namespace Phug\Lexer\Token\Partial;

trait ModeTrait
{

    private $mode = null;

    public function getMode()
    {

        return $this->mode;
    }

    public function setMode($mode)
    {

        $this->mode = $mode;

        return $this;
    }
}
