<?php

namespace Phug\Lexer\Scanner;

use Phug\Lexer;
use Phug\Lexer\ScannerInterface;
use Phug\Lexer\State;
use Phug\Lexer\Token\CommentToken;
use Phug\Lexer\Token\FilterToken;

class FilterScanner implements ScannerInterface
{
    public function scan(State $state)
    {

        foreach ($state->scanToken(
            FilterToken::class,
            ':(?<name>[a-zA-Z_][a-zA-Z0-9\-_]*(?::[a-zA-Z_][a-zA-Z0-9\-_]*)*)'
        ) as $token) {
            yield $token;

            foreach ($state->scan(TextBlockScanner::class) as $subToken) {
                yield $subToken;
            }
        }
    }
}
