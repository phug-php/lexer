<?php

namespace Phug\Lexer\Scanner;

use Phug\Lexer;
use Phug\Lexer\ScannerInterface;
use Phug\Lexer\State;
use Phug\Lexer\Token\InterpolationEndToken;
use Phug\Lexer\Token\InterpolationStartToken;
use Phug\Lexer\Token\TextToken;

class TextScanner implements ScannerInterface
{
    public function scan(State $state)
    {
        static $debug = 0;
        $reader = $state->getReader();

        /** @var TextToken $token */
        while ($reader->match('(?<text>.*?)#\\[(?<interpolation>(?>"(?:\\\\[\\S\\s]|[^"\\\\])*"|\'(?:\\\\[\\S\\s]|[^\'\\\\])*\'|[^\\[\\]\'"]++|(?-2))*+)\\]')) {
            $text = $reader->getMatch('text');
            $interpolation = $reader->getMatch('interpolation');
            if (mb_strlen($text) > 0) {
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
        $token = $state->createToken(TextToken::class);
        $text = $reader->readUntilNewLine();

        if (mb_strlen($text) < 1) {
            return;
        }

        //Always omit the very first space in basically every text (if there is one)
        if ($text[0] === ' ') {
            $text = substr($text, 1);
        }

        $token->setValue($text);
        yield $token;
    }
}
