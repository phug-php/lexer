<?php

namespace Phug\Lexer\Scanner;

use Phug\Lexer;
use Phug\Lexer\ScannerInterface;
use Phug\Lexer\State;
use Phug\Lexer\Token\TextToken;

class TextScanner implements ScannerInterface
{
    public function scan(State $state)
    {

        $reader = $state->getReader();

        /** @var TextToken $token */
        $token = $state->createToken(TextToken::class);
        $text = trim($reader->readUntilNewLine());

        if (mb_strlen($text) < 1) {
            return;
        }

        $token->setValue($text);
        yield $token;
    }
}
