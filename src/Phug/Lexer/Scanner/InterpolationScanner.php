<?php

namespace Phug\Lexer\Scanner;

use Phug\Lexer\ScannerInterface;
use Phug\Lexer\State;
use Phug\Lexer\Token\InterpolationEndToken;
use Phug\Lexer\Token\InterpolationStartToken;
use Phug\Lexer\Token\TextToken;

class InterpolationScanner implements ScannerInterface
{
    public function scan(State $state)
    {
        $reader = $state->getReader();

        while ($reader->match(
            '(?<text>.*?)'.
            '#\\[(?<interpolation>'.
                '(?>"(?:\\\\[\\S\\s]|[^"\\\\])*"|\'(?:\\\\[\\S\\s]|[^\'\\\\])*\'|[^\\[\\]\'"]++|(?-2))*+'.
            ')\\]'
        )) {
            $text = $reader->getMatch('text');
            $interpolation = $reader->getMatch('interpolation');
            if (mb_strlen($text) > 0) {
                /** @var TextToken $token */
                $token = $state->createToken(TextToken::class);
                $token->setValue($text);
                yield $token;
            }
            yield $state->createToken(InterpolationStartToken::class);
            $lexer = clone $state->getLexer();
            foreach ($lexer->lex($interpolation) as $token) {
                yield $token;
            }
            yield $state->createToken(InterpolationEndToken::class);
            $reader->consume();
        }
    }
}
