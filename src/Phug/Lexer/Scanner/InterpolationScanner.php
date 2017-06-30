<?php

namespace Phug\Lexer\Scanner;

use Phug\Lexer\ScannerInterface;
use Phug\Lexer\State;
use Phug\Lexer\Token\ExpressionToken;
use Phug\Lexer\Token\InterpolationEndToken;
use Phug\Lexer\Token\InterpolationStartToken;
use Phug\Lexer\Token\TagInterpolationEndToken;
use Phug\Lexer\Token\TagInterpolationStartToken;
use Phug\Lexer\Token\TextToken;

class InterpolationScanner implements ScannerInterface
{
    protected function scanInterpolation(State $state, $tagInterpolation, $interpolation, $escape)
    {
        if ($tagInterpolation) {
            /** @var TagInterpolationStartToken $start */
            $start = $state->createToken(TagInterpolationStartToken::class);
            /** @var TagInterpolationEndToken $end */
            $end = $state->createToken(TagInterpolationEndToken::class);

            $start->setEnd($end);
            $end->setStart($start);

            $lexer = clone $state->getLexer();

            yield $start;
            foreach ($lexer->lex($tagInterpolation) as $token) {
                yield $token;
            }
            yield $end;

            return;
        }

        /** @var InterpolationStartToken $start */
        $start = $state->createToken(InterpolationStartToken::class);
        /** @var InterpolationEndToken $end */
        $end = $state->createToken(InterpolationEndToken::class);

        $start->setEnd($end);
        $end->setStart($start);

        /** @var ExpressionToken $token */
        $token = $state->createToken(ExpressionToken::class);
        $token->setValue($interpolation);
        if ($escape === '#') {
            $token->escape();
        }

        yield $start;
        yield $token;
        yield $end;
    }

    public function scan(State $state)
    {
        $reader = $state->getReader();

        while ($reader->match(
            '(?<text>.*?)'.
            '(?<!\\\\)'.
            '(?<escape>#|!(?=\{))(?<wrap>'.
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
                $reader->getMatch('interpolation'),
                $reader->getMatch('escape')
            ) as $token) {
                yield $token;
            }

            $reader->consume();

            if ($reader->peekNewLine()) {
                /** @var TextToken $token */
                $token = $state->createToken(TextToken::class);
                $token->setValue("\n");
                yield $token;
            }
        }
    }
}
