<?php

namespace Phug\Test\Lexer\Scanner;

use Phug\Lexer\Token\ClassToken;
use Phug\Lexer\Token\MixinCallToken;
use Phug\Test\AbstractLexerTest;

class MixinCallScannerTest extends AbstractLexerTest
{
    /**
     * @covers Phug\Lexer\Scanner\MixinCallScanner
     * @covers Phug\Lexer\Scanner\MixinCallScanner::scan
     */
    public function testMixinCall()
    {
        /** @var MixinCallToken $tok */
        list($tok) = $this->assertTokens('+a', [
            MixinCallToken::class,
        ]);

        self::assertSame('a', $tok->getName());

        /** @var MixinCallToken $tok */
        list($mixin, $class) = $this->assertTokens('+foo.bar', [
            MixinCallToken::class,
            ClassToken::class,
        ]);

        self::assertSame('foo', $mixin->getName());
        self::assertSame('bar', $class->getName());
    }
}
