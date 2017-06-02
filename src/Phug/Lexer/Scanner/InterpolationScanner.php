<?php

namespace Phug\Lexer\Scanner;

use Phug\Lexer\ScannerInterface;
use Phug\Lexer\State;
use Phug\Lexer\Token\ExpressionToken;
use Phug\Lexer\Token\TagInterpolationEndToken;
use Phug\Lexer\Token\TagInterpolationStartToken;
use Phug\Lexer\Token\InterpolationEndToken;
use Phug\Lexer\Token\InterpolationStartToken;
use Phug\Lexer\Token\TextToken;

class InterpolationScanner implements ScannerInterface
{
    protected function scanInterpolation(State $state, $tagInterpolation, $interpolation)
    {
        if ($tagInterpolation) {
            yield $state->createToken(TagInterpolationStartToken::class);
            $lexer = clone $state->getLexer();
            foreach ($lexer->lex($tagInterpolation) as $token) {
                yield $token;
            }
            yield $state->createToken(TagInterpolationEndToken::class);

            return;
        }

        yield $state->createToken(InterpolationStartToken::class);
        /** @var ExpressionToken $token */
        $token = $state->createToken(ExpressionToken::class);
        $token->setValue($interpolation);
        yield $token;
        yield $state->createToken(InterpolationEndToken::class);
    }

    public function scan(State $state)
    {
        $reader = $state->getReader();

        while ($reader->match(
            '(?<text>.*?)'.
            '#(?<wrap>'.
                '\\[(?<tagInterpolation>'.
                    '(?>"(?:\\\\[\\S\\s]|[^"\\\\])*"|\'(?:\\\\[\\S\\s]|[^\'\\\\])*\'|[^\\[\\]\'"]++|(?-3))*+'.
                ')\\]|'.
                '\\{(?<interpolation>'.
                    '(?>"(?:\\\\[\\S\\s]|[^"\\\\])*"|\'(?:\\\\[\\S\\s]|[^\'\\\\])*\'|[^{}\'"]++|(?-3))*+'.
                ')\\}'.
            ')'
        )) {
            $text = $reader->getMatch('text');

            if (mb_strlen($text) > 0) {
                /** @var TextToken $token */
                $token = $state->createToken(TextToken::class);
                $token->setValue($text);
                yield $token;
            }

            foreach ($this->scanInterpolation(
                $state,
                $reader->getMatch('tagInterpolation'),
                $reader->getMatch('interpolation')
            ) as $token) {
                yield $token;
            }

            $reader->consume();
        }
    }
}
