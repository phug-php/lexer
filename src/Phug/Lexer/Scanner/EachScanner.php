<?php

namespace Phug\Lexer\Scanner;

use Phug\Lexer\ScannerInterface;
use Phug\Lexer\State;
use Phug\Lexer\Token\EachToken;

class EachScanner implements ScannerInterface
{
    public function scan(State $state)
    {
        $reader = $state->getReader();

        if (!$reader->match('each[\t ]+')) {
            return;
        }

        /** @var EachToken $token */
        $token = $state->createToken(EachToken::class);
        $reader->consume();

        if (!$reader->match(
            "\\$(?<itemName>[a-zA-Z_][a-zA-Z0-9_]*)(?:[\t ]*,[\t ]*".
            "\\$(?<keyName>[a-zA-Z_][a-zA-Z0-9_]*))?[\t ]+in[\t ]+"
        )) {
            $state->throwException(
                'The syntax for each is `each $itemName[, $keyName]] in {{subject}}`'
            );
        }

        $token->setItem($reader->getMatch('itemName'));
        $token->setKey($reader->getMatch('keyName'));

        $reader->consume();

        $subject = $reader->readExpression(["\n", '?', ':']);

        //TODO: [DRY] This is copied from `Phug\Lexer\Scanner\ControlStatementScanner->scan`
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
        //Up to here (See TODO above)

        if (empty($subject)) {
            $state->throwException(
                '`each`-statement has no subject to operate on'
            );
        }

        $token->setSubject($subject);

        yield $token;
    }
}
