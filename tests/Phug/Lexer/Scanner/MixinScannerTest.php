<?php

namespace Phug\Test\Lexer\Scanner;

use Phug\Lexer\Token\MixinToken;
use Phug\Test\AbstractLexerTest;

class MixinScannerTest extends AbstractLexerTest
{
    /**
     * @covers Phug\Lexer\Scanner\MixinScanner
     * @covers Phug\Lexer\Scanner\MixinScanner::scan
     */
    public function testMixinCall()
    {
        /* @var MixinToken $tok */
        list($tok) = $this->assertTokens('mixin a', [
            MixinToken::class,
        ]);

        self::assertSame('a', $tok->getName());
    }
}
