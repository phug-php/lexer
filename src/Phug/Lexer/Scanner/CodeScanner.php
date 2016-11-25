<?php

namespace Phug\Lexer\Scanner;

use Phug\Lexer;
use Phug\Lexer\ScannerInterface;
use Phug\Lexer\State;
use Phug\Lexer\Token\CaseToken;
use Phug\Lexer\Token\CodeToken;
use Phug\Lexer\Token\ExpressionToken;

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

        foreach ($state->scan(TextScanner::class) as $textToken) {
            yield $token;
            yield $textToken;
            return;
        }

        $token->setIsBlock(true);
        yield $token;

        foreach ($state->scan(TextBlockScanner::class) as $token) {
            yield $token;
        }
    }
}
