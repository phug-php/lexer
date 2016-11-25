<?php

namespace Phug\Lexer\Token\Partial;

trait ValueTrait
{

    private $value = null;

    public function getValue()
    {

        return $this->value;
    }

    public function setValue($value)
    {

        $this->value = $value;
    }
}
