<?php

namespace Phug\Lexer\Token\Partial;

trait NameTrait
{

    private $name = null;

    public function getName()
    {

        return $this->name;
    }

    public function setName($name)
    {

        $this->name = $name;

        return $this;
    }
}
