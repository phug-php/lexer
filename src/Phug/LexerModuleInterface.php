<?php

namespace Phug;

use Phug\Util\ModuleInterface;

interface LexerModuleInterface extends ModuleInterface
{
    public function injectLexer(Lexer $lexer);
}
