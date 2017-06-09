<?php

namespace Phug\Test;

use Phug\Lexer;
use Phug\LexerModule;

/**
 * @coversDefaultClass Phug\LexerModule
 */
class LexerModuleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::<public>
     */
    public function testModule()
    {
        $copy = null;
        $module = new LexerModule();
        $module->onPlug(function ($lex) use (&$copy) {
            $copy = $lex;
        });
        $lexer = new Lexer([
            'modules' => [$module],
        ]);
        self::assertSame($lexer, $copy);
    }
}
