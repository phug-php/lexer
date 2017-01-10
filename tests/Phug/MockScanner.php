<?php

namespace Phug\Test;

use Phug\Lexer\ScannerInterface;
use Phug\Lexer\State;

class MockScanner implements ScannerInterface
{
    protected $state;
    protected $lexer;

    public function setLexer($lexer)
    {
        $this->lexer = $lexer;
    }

    public function scan(State $state)
    {
        $this->state = $this->lexer->getState();

        return [];
    }

    public function getState()
    {
        return $this->state;
    }
}
