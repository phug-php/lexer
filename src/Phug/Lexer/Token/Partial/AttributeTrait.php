<?php

namespace Phug\Lexer\Token\Partial;

use SplObjectStorage;

trait AttributeTrait
{

    private $attributes = null;

    public function getAttributes()
    {

        if (!$this->attributes) {
            $this->attributes = new SplObjectStorage;
        }

        return $this->attributes;
    }
}
