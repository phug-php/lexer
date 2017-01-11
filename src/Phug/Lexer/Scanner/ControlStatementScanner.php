<?php

namespace Phug\Lexer\Scanner;

use Phug\Lexer\ScannerInterface;
use Phug\Lexer\State;

abstract class ControlStatementScanner implements ScannerInterface
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
            $subject = $reader->readExpression(["\n", '?', ':']);

            //Handle `if Foo::bar`
            if ($reader->peekString('::')) {
                $subject .= $reader->getLastPeekResult();
                $reader->consume();

                $subject .= $reader->readExpression(["\n", ':']);
            } elseif ($reader->peekChar('?')) {
                $subject .= ' '.$reader->getLastPeekResult();
                $reader->consume();

                //Ternary expression
                if ($reader->peekChars('?:')) {
                    $subject .= $reader->getLastPeekResult().' ';
                    $reader->consume();
                } else {
                    $subject .= ' '.$reader->readExpression(["\n", ':']).' ';

                    if ($reader->peekChar(':')) {
                        $subject .= $reader->getLastPeekResult().' ';
                        $reader->consume();
                    }
                }

                $subject .= $reader->readExpression(["\n", ':']);
            }

            $subject = trim($subject);

            $token->setSubject(!empty($subject) ? $subject : null);
        }

        yield $token;

        foreach ($state->scan(SubScanner::class) as $token) {
            yield $token;
        }
    }
}
