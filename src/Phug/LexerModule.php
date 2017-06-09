<?php

namespace Phug;

use Phug\Util\AbstractModule;
use Phug\Util\ModulesContainerInterface;

class LexerModule extends AbstractModule implements LexerModuleInterface
{
    public function injectLexer(Lexer $lexer)
    {
        return $lexer;
    }

    public function plug(ModulesContainerInterface $parent)
    {
        parent::plug($this->injectLexer($parent));
    }
}
