<?php

namespace Phug\Lexer\Scanner;

use Phug\Lexer;
use Phug\Lexer\ScannerInterface;
use Phug\Lexer\State;
use Phug\Lexer\Token\IndentToken;
use Phug\Lexer\Token\OutdentToken;

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
            }

            if ($level <= 0) {
                break;
            }

            foreach ($state->scan(TextScanner::class) as $token) {
                yield $token;
            }
        }
    }
}
