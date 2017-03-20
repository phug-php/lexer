<?php

namespace Phug\Lexer\Scanner;

use Phug\Lexer\ScannerInterface;
use Phug\Lexer\State;
use Phug\Lexer\Token\IndentToken;
use Phug\Lexer\Token\OutdentToken;
use Phug\Lexer\Token\TextToken;
use Phug\LexerException;

class TextBlockScanner implements ScannerInterface
{
    public function scan(State $state)
    {
        $reader = $state->getReader();

        foreach ($state->scan(TextScanner::class) as $token) {
            yield $token;
        }

        foreach ($state->scan(NewLineScanner::class) as $token) {
            yield $token;
        }

        foreach ($state->scan(IndentationScanner::class) as $token) {
            if (!($token instanceof IndentToken)) {
                throw new LexerException(
                    'Unexpected '.get_class($token)
                );
            }

            yield $token;
        }

        $level = 0;
        while ($reader->hasLength()) {
            foreach ($state->loopScan([IndentationScanner::class, NewLineScanner::class]) as $token) {
                if ($token instanceof IndentToken) {
                    $level++;
                }

                if ($token instanceof OutdentToken) {
                    $level--;
                }

                yield $token;

                if ($level > 0) {
                    break;
                }
            }

            if ($level <= 0) {
                break;
            }

            $token = $state->loopScan([IndentationScanner::class]);
            if ($token instanceof OutdentToken) {
                yield $token;

                break;
            }

            if ($token instanceof IndentToken) {
                /** @var TextToken $token */
                $token = $state->createToken(TextToken::class);
                $token->setValue($reader->readUntilNewLine());

                yield $token;
            }
        }
    }
}
