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
            $getIndentChar = [$reader, 'peekIndentation'];
        }

        while ($indentChar = call_user_func($getIndentChar, $reader)) {
            $reader->consume(1);
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
            $indentWidth = $state->getIndentWidth();
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

        $state->setLevel($this->getIndentLevel($state, INF, function () use (&$indent) {
            if (mb_strlen($indent)) {
                $char = mb_substr($indent, 0, 1);
                $indent = mb_substr($indent, 1);

                return $char;
            }

            return null;
        }));

        if ($state->getLevel() > $oldLevel + 1) {
            $state->setLevel($oldLevel + 1);
        }

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

        $levels = $this->setStateLevel($state, $indent);

        //Unchanged levels
        if ($levels === 0) {
            return;
        }

        //We create a token for each indentation/outdentation
        $type = $levels > 0 ? IndentToken::class : OutdentToken::class;
        $levels = abs($levels);

        while ($levels--) {
            yield $state->createToken($type);
        }
    }
}
