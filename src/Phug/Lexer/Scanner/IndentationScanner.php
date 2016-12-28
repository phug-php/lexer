<?php

namespace Phug\Lexer\Scanner;

use Phug\Lexer;
use Phug\Lexer\ScannerInterface;
use Phug\Lexer\State;
use Phug\Lexer\Token\IndentToken;
use Phug\Lexer\Token\OutdentToken;

class IndentationScanner implements ScannerInterface
{
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

        $oldLevel = $state->getLevel();
        if ($indent === null) {
            $state->setLevel(0);
        } else {
            $spaces = mb_strpos($indent, ' ') !== false;
            $tabs = mb_strpos($indent, "\t") !== false;
            $mixed = $spaces && $tabs;

            if ($mixed) {
                switch ($state->getIndentStyle()) {
                    case Lexer::INDENT_SPACE:
                    default:
                        //Convert tabs to spaces based on indentWidth
                        $indent = str_replace(Lexer::INDENT_TAB, str_repeat(
                            Lexer::INDENT_SPACE,
                            $state->getIndentWidth() ?: 4
                        ), $spaces);
                        $tabs = false;
                        $mixed = false;
                        break;
                    case Lexer::INDENT_TAB:
                        //Convert spaces to tabs based on indentWidth
                        $indent = str_replace(Lexer::INDENT_SPACE, str_repeat(
                            Lexer::INDENT_TAB,
                            $state->getIndentWidth() ?: 1
                        ), $spaces);
                        $spaces = false;
                        $mixed = false;
                        break;
                }
            }

            //Update the indentation style
            if (!$state->getIndentStyle()) {
                $state->setIndentStyle($tabs ? Lexer::INDENT_TAB : Lexer::INDENT_SPACE);
            }

            //Update the indentation width
            if (!$state->getIndentWidth()) {
                //We will use the pretty first indentation as our indent width
                $state->setIndentWidth(mb_strlen($indent));
            }

            $state->setLevel(intval(round(mb_strlen($indent) / $state->getIndentWidth())));

            if ($state->getLevel() > $oldLevel + 1) {
                $state->setLevel($oldLevel + 1);
            }
        }

        $levels = $state->getLevel() - $oldLevel;

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
