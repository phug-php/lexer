<?php

namespace Phug\Test\Lexer\Scanner;

use Phug\Lexer\Token\IndentToken;
use Phug\Lexer\Token\NewLineToken;
use Phug\Lexer\Token\TagInterpolationEndToken;
use Phug\Lexer\Token\TagInterpolationStartToken;
use Phug\Lexer\Token\TagToken;
use Phug\Lexer\Token\TextToken;
use Phug\Test\AbstractLexerTest;

class TextLineScannerTest extends AbstractLexerTest
{
    /**
     * @covers \Phug\Lexer\Scanner\TextLineScanner
     * @covers \Phug\Lexer\Scanner\TextLineScanner::scan
     */
    public function testScan()
    {
        /**
         * @var TextToken $tok
         */
        list(, , , $tok) = $this->assertTokens("p\n  | Hello", [
            TagToken::class,
            NewLineToken::class,
            IndentToken::class,
            TextToken::class,
        ]);

        self::assertFalse($tok->isEscaped());

        /**
         * @var TextToken $tok
         */
        list(, , , $tok) = $this->assertTokens("p\n  !| Hello", [
            TagToken::class,
            NewLineToken::class,
            IndentToken::class,
            TextToken::class,
        ]);

        self::assertTrue($tok->isEscaped());
    }

    /**
     * @covers \Phug\Lexer\Scanner\TextLineScanner
     * @covers \Phug\Lexer\Scanner\TextLineScanner::scan
     * @covers \Phug\Lexer\Scanner\TextScanner
     * @covers \Phug\Lexer\Scanner\TextScanner::scan
     * @covers \Phug\Lexer\Scanner\InterpolationScanner
     * @covers \Phug\Lexer\Scanner\InterpolationScanner::scanInterpolation
     * @covers \Phug\Lexer\Scanner\InterpolationScanner::scan
     */
    public function testScanQuit()
    {
        $this->assertTokens('p Hello', [
            TagToken::class,
            TextToken::class,
        ]);
        $this->assertTokens('p Hello #[strong world]!', [
            TagToken::class,
            TextToken::class,
            TagInterpolationStartToken::class,
            TagToken::class,
            TextToken::class,
            TagInterpolationEndToken::class,
            TextToken::class,
        ]);
    }
}
