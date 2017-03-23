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

            while ($state->nextOutdent() !== false) {
                yield $state->createToken(OutdentToken::class);
            }
        }
    }

    public function appendBlockLines(array &$lines, State $state)
    {
        $reader = $state->getReader();
        $level = $state->getLevel();

        while ($reader->hasLength()) {
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
    }

    public function scan(State $state)
    {
        $level = null;
        $reader = $state->getReader();
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
                $lines[] = $reader->readUntilNewLine();
                if ($reader->peekNewLine()) {
                    $reader->consume(1);
                }

                break;
            }
        }

        if (count($lines)) {
            $this->appendBlockLines($lines, $state);
            foreach ($this->createBlockTokens($state, $lines) as $token) {
                yield $token;
            }
        }
    }
}
