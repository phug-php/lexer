<?php

namespace Phug\Lexer\Token\Partial;

trait BlockTrait
{

    private $block = false;

    public function isBlock()
    {

        return $this->block;
    }

    public function setIsBlock($block)
    {

        $this->block = $block;
        return $this;
    }
}
