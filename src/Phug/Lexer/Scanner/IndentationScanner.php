<?php

namespace Phug\Lexer\Scanner;

use Phug\Lexer;
use Phug\Lexer\ScannerInterface;
use Phug\Lexer\State;
use Phug\Lexer\Token\IndentToken;
use Phug\Lexer\Token\OutdentToken;
use Phug\LexerException;

class IndentationScanner implements ScannerInterface
{
    protected function getLevelFromIndent(State $state, $indent)
    {
        return intval(floor(mb_strlen($indent) / ($state->getIndentWidth() ?: INF)));
    }

    public function getIndentLevel(State $state, $maxLevel = INF, callable $getIndentChar = null)
    {
        $reader = $state->getReader();
        $indent = '';

        if (is_null($getIndentChar)) {
            $getIndentChar = function () use ($reader) {
                $char = null;

                if ($reader->peekIndentation()) {
                    $char = $reader->getLastPeekResult();
                    $reader->consume();
                }

                return $char;
            };
        }

        while ($indentChar = call_user_func($getIndentChar, $reader)) {
            $isTab = $indentChar === Lexer::INDENT_TAB;
            $indentStyle = $isTab ? Lexer::INDENT_TAB : Lexer::INDENT_SPACE;
            //Update the indentation style
            if (!$state->getIndentStyle()) {
                $state->setIndentStyle($indentStyle);
            }
            if ($state->getIndentStyle() !== $indentStyle) {
                if (!$state->getOption('allow_mixed_indent')) {
                    throw new LexerException(
                        'Invalid indentation, you can use tabs or spaces but not both'
                    );
                }

                $indentChar = $isTab
                    ? str_repeat(
                        Lexer::INDENT_SPACE,
                        $state->getIndentWidth() ?: 4
                    )
                    : str_repeat(
                        Lexer::INDENT_TAB,
                        $state->getIndentWidth() ?: 1
                    );
            }
            $indent .= $indentChar;
            if (
                $state->getIndentWidth() &&
                $this->getLevelFromIndent($state, $indent) >= $maxLevel
            ) {
                break;
            }
        }

        //Update the indentation width
        $length = mb_strlen($indent);
        if ($length && !$state->getIndentWidth()) {
            //We will use the pretty first indentation as our indent width
            $state->setIndentWidth($length);
        }

        return $this->getLevelFromIndent($state, $indent);
    }

    protected function setStateLevel(State $state, $indent)
    {
        $oldLevel = $state->getLevel();
        $newLevel = $this->getIndentLevel($state, INF, function () use (&$indent) {
            $char = null;

            if (mb_strlen($indent)) {
                $char = mb_substr($indent, 0, 1);
                $indent = mb_substr($indent, 1);
            }

            return $char;
        });

        $state->setLevel($newLevel);

        return $state->getLevel() - $oldLevel;
    }

    public function scan(State $state)
    {
        $reader = $state->getReader();

        //There's no indentation if we're not at the start of a line
        if ($reader->getOffset() !== 1) {
            return;
        }

        $indent = $reader->readIndentation();

        //If this is an empty line, we ignore the indentation completely.
        foreach ($state->scan(NewLineScanner::class) as $token) {
            yield $token;

            return;
        }

        //We create a token for each indentation/outdentation
        if ($this->setStateLevel($state, $indent) > 0) {
            $state->indent();

            yield $state->createToken(IndentToken::class);

            return;
        }

        while ($state->getLevel() < $state->getIndentLevel()) {
            $oldLevel = $state->getIndentLevel();
            $newLevel = $state->outdent();
            if ($newLevel < $state->getLevel()) {
                throw new LexerException(
                    'Inconsistent indentation. '.
                    'Expecting either '.
                    ($newLevel * $state->getIndentWidth()).
                    ' or '.
                    ($oldLevel * $state->getIndentWidth()).
                    ' spaces/tabs.'
                );
            }

            yield $state->createToken(OutdentToken::class);
        }
    }
}
