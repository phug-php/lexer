<?php

namespace Phug\Lexer\Scanner;

use Phug\Lexer;
use Phug\Lexer\ScannerInterface;
use Phug\Lexer\State;

class ControlStatementScanner implements ScannerInterface
{

    private $tokenClassName;
    private $names;

    public function __construct($tokenClassName, array $names)
    {

        $this->tokenClassName = $tokenClassName;
        $this->names = $names;
    }

    public function scan(State $state)
    {

        $reader = $state->getReader();
        $names = implode('|', $this->names);

        if (!$reader->match("({$names})[ \t\n:]", null, " \t\n:")) {
            return;
        }

        $token = $state->createToken($this->tokenClassName);
        $name = $reader->getMatch(1);
        $reader->consume();

        //Ignore spaces after identifier
        $reader->readIndentation();

        if (method_exists($token, 'setName')) {
            $token->setName($name);
        }

        if (method_exists($token, 'setSubject')) {
            $token->setSubject($reader->readExpression(["\n", ":"]));
        }

        yield $token;

        foreach ($state->scan(SubScanner::class) as $token) {
            yield $token;
        }
    }
}
