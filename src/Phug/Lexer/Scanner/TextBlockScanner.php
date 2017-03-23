<?php

namespace Phug\Lexer\Scanner;

use Phug\Lexer\ScannerInterface;
use Phug\Lexer\State;
use Phug\Lexer\Token\IndentToken;
use Phug\Lexer\Token\NewLineToken;
use Phug\Lexer\Token\OutdentToken;
use Phug\Lexer\Token\TextToken;

class TextBlockScanner implements ScannerInterface
{
    protected function createBlockTokens(State $state, array $lines)
    {
        $reader = $state->getReader();
        /**
         * @var TextToken $token
         */
        $token = $state->createToken(TextToken::class);
        $token->setValue(implode("\n", $lines));

        yield $token;

        if ($reader->getLength()) {
            yield $state->createToken(NewLineToken::class);

            foreach ($state->getIndentsStepsDown() as $level) {
                yield $state->createToken(OutdentToken::class);
            }
        }
    }

    public function scan(State $state)
    {
        $reader = $state->getReader();
        $level = null;
        $lines = [];

        foreach ($state->scan(TextScanner::class) as $token) {
            yield $token;
        }

        foreach ($state->loopScan([NewLineScanner::class, IndentationScanner::class]) as $token) {
            yield $token;

            if ($token instanceof OutdentToken) {
                break;
            }
            if ($token instanceof IndentToken) {
                $level = $state->getLevel();
                $lines[] = $reader->readUntilNewLine();
                if ($reader->peekNewLine()) {
                    $reader->consume(1);
                }

                break;
            }
        }

        while ($level && $reader->hasLength()) {
            $indentationScanner = new IndentationScanner();
            $newLevel = $indentationScanner->getIndentLevel($state, $level);
            if ($newLevel < $level) {
                if ($reader->match('[ \t]*\n')) {
                    $reader->consume(mb_strlen($reader->getMatch(0)));
                    $lines[] = '';

                    continue;
                }

                $state->setLevel($newLevel);

                break;
            }

            $lines[] = $reader->readUntilNewLine();
            if ($reader->peekNewLine()) {
                $reader->consume(1);
            }
        }

        if (count($lines)) {
            foreach ($this->createBlockTokens($state, $lines) as $token) {
                yield $token;
            }
        }
    }
}
