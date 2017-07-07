<?php

namespace Phug;

interface LexerInterface
{
    
    public function __construct(array $options = null);
    public function lex($input, $path = null);
    public function dump($input);
}
