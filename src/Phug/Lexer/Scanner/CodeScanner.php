<?php

namespace Phug\Lexer\Scanner;

use Phug\Lexer;
use Phug\Lexer\ScannerInterface;
use Phug\Lexer\State;
use Phug\Lexer\Token\CodeToken;

class CodeScanner implements ScannerInterface
{
    public function scan(State $state)
    {
        $reader = $state->getReader();

        if (!$reader->peekChar('-')) {
            return;
        }

        /** @var CodeToken $token */
        $token = $state->createToken(CodeToken::class);
        $reader->consume();

        //Single-line code
        foreach ($state->scan(TextScanner::class) as $textToken) {

            //Trim the text as expressions usually would
            yield $token;

            if ($textToken instanceof Lexer\Token\TextToken) {
                $textToken->setValue(trim($textToken->getValue()));
                yield $textToken;
            }

            return;
        }

        //Multi-line code
        $token->setIsBlock(true);
        yield $token;

        foreach ($state->scan(TextBlockScanner::class) as $token) {
            yield $token;
        }
    }
}
