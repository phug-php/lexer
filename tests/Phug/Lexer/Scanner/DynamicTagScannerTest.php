<?php

namespace Phug\Test\Lexer\Scanner;

use Phug\Lexer\Token\ExpressionToken;
use Phug\Lexer\Token\InterpolationEndToken;
use Phug\Lexer\Token\InterpolationStartToken;
use Phug\Test\AbstractLexerTest;

class DynamicTagScannerTest extends AbstractLexerTest
{
    /**
     * @covers \Phug\Lexer\Scanner\DynamicTagScanner
     * @covers \Phug\Lexer\Scanner\DynamicTagScanner::scan
     */
    public function testUsualTagName()
    {
        /** @var ExpressionToken $tok */
        list(, $tok) = $this->assertTokens('#{"some-tag-name"}', [
            InterpolationStartToken::class,
            ExpressionToken::class,
            InterpolationEndToken::class,
        ]);

        self::assertSame('"some-tag-name"', $tok->getValue());
        self::assertTrue($tok->isEscaped());
        self::assertTrue($tok->isChecked());

        /** @var ExpressionToken $tok */
        list(, $tok) = $this->assertTokens('!#{"<b>"}', [
            InterpolationStartToken::class,
            ExpressionToken::class,
            InterpolationEndToken::class,
        ]);

        self::assertSame('"<b>"', $tok->getValue());
        self::assertFalse($tok->isEscaped());
        self::assertTrue($tok->isChecked());
    }
}
