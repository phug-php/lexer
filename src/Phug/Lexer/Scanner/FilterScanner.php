<?php

namespace Phug\Lexer\Scanner;

use Phug\Lexer\ScannerInterface;
use Phug\Lexer\State;
use Phug\Lexer\Token\FilterToken;
use Phug\Lexer\Token\IndentToken;
use Phug\Lexer\Token\NewLineToken;
use Phug\Lexer\Token\OutdentToken;
use Phug\Lexer\Token\TextToken;

class FilterScanner implements ScannerInterface
{
    public function scan(State $state)
    {
        $reader = $state->getReader();

        foreach ($state->scanToken(
            FilterToken::class,
            ':(?<name>[a-zA-Z_][a-zA-Z0-9\-_]*(?::[a-zA-Z_][a-zA-Z0-9\-_]*)*)(?=\s|\()'
        ) as $token) {
            yield $token;

            foreach ($state->scan(AttributeScanner::class) as $subToken) {
                yield $subToken;
            }

            if ($reader->match('[\t ]')) {
                $reader->consume(1);
                /** @var TextToken $token */
                $token = $state->createToken(TextToken::class);
                $token->setValue($reader->readUntilNewLine());

                yield $token;

                continue;
            }

            $level = $state->getLevel() + 1;
            $newLevel = $level;
            $lines = [$reader->readUntilNewLine()];

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

                if (!$reader->peekNewLine()) {
                    break;
                }

                $reader->consume(1);
            }

            yield $state->createToken(NewLineToken::class);
            yield $state->createToken(IndentToken::class);

            /** @var TextToken $token */
            $token = $state->createToken(TextToken::class);
            $token->setValue(implode("\n", $lines));

            yield $token;

            if ($reader->hasLength()) {
                yield $state->createToken(NewLineToken::class);

                $state->indent($level);

                while ($state->nextOutdent() !== false) {
                    yield $state->createToken(OutdentToken::class);
                }
            }
        }
    }
}
