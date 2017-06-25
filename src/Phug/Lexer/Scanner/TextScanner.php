<?php

namespace Phug\Lexer\Scanner;

use Phug\Lexer\ScannerInterface;
use Phug\Lexer\State;
use Phug\Lexer\Token\InterpolationEndToken;
use Phug\Lexer\Token\TagInterpolationEndToken;
use Phug\Lexer\Token\TextToken;

class TextScanner implements ScannerInterface
{
    public function scan(State $state)
    {
        $reader = $state->getReader();

        foreach ($state->scan(InterpolationScanner::class) as $subToken) {
            yield $subToken;
        }

        /** @var TextToken $token */
        $token = $state->createToken(TextToken::class);
        $text = $reader->readUntilNewLine();

        if (mb_strlen($text) < 1) {
            return;
        }

        //Always omit the very first space in basically every text (if there is one)
        if (in_array(mb_substr($text, 0, 1), [' ', "\t"])) {
            $previous = $state->getLastToken();
            if (!($previous instanceof TagInterpolationEndToken || $previous instanceof InterpolationEndToken)) {
                $text = mb_substr($text, 1);
            }
        }

        $text = preg_replace('/\\\\([#!]\\[|#\\{)/', '$1', $text);
        $token->setValue($text);
        yield $token;
    }
}
