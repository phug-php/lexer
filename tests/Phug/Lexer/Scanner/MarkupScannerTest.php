<?php

namespace Phug\Test\Lexer\Scanner;

use Phug\Lexer;
use Phug\Lexer\Scanner\MarkupScanner;
use Phug\Lexer\State;
use Phug\Lexer\Token\CodeToken;
use Phug\Lexer\Token\ExpansionToken;
use Phug\Lexer\Token\ExpressionToken;
use Phug\Lexer\Token\IndentToken;
use Phug\Lexer\Token\InterpolationEndToken;
use Phug\Lexer\Token\InterpolationStartToken;
use Phug\Lexer\Token\NewLineToken;
use Phug\Lexer\Token\TagToken;
use Phug\Lexer\Token\TextToken;
use Phug\Test\AbstractLexerTest;

class MarkupScannerTest extends AbstractLexerTest
{
    /**
     * @covers \Phug\Lexer\Scanner\MarkupScanner
     * @covers \Phug\Lexer\Scanner\MarkupScanner::scan
     * @covers \Phug\Lexer\Analyzer\LineAnalyzer::<public>
     */
    public function testRawMarkup()
    {
        $template = '<a></a>';
        /** @var TextToken $tok */
        list($tok) = $this->assertTokens($template, [
            TextToken::class,
        ]);

        self::assertFalse($tok->isEscaped());
        self::assertSame($template, $tok->getValue());

        $template = "<ul id='aa'>\n  <li class='foo'>item</li>\n</ul>";
        /** @var TextToken $tok */
        list($tok) = $this->assertTokens($template, [
            TextToken::class,
        ]);

        self::assertFalse($tok->isEscaped());
        self::assertSame($template, $tok->getValue());

        $template = <<<'EOT'
- var version = 1449104952939

<ul>
  <li>foo</li>
  <li>bar</li>
  <li>baz</li>
</ul>

<!--build:js /js/app.min.js?v=#{version}-->
<!--endbuild-->

p You can <em>embed</em> html as well.
p: <strong>Even</strong> as the body of a block expansion.
EOT;
        $this->assertTokens($template, [
            CodeToken::class,
            TextToken::class,
            NewLineToken::class,
            NewLineToken::class,
            TextToken::class,
            InterpolationStartToken::class,
            ExpressionToken::class,
            InterpolationEndToken::class,
            TextToken::class,
            NewLineToken::class,
            TagToken::class,
            TextToken::class,
            NewLineToken::class,
            TagToken::class,
            ExpansionToken::class,
            TextToken::class,
        ]);

        /*
         * /!\ Caution, this may not be the definitive behavior as pugjs handle it differently.
         * @see https://github.com/phug-php/phug/issues/34
         */
        $this->assertTokens("div\n  <div> Foo\n  Bar\n  </div>", [
            TagToken::class,
            NewLineToken::class,
            IndentToken::class,
            TextToken::class,
            NewLineToken::class,
            TextToken::class,
        ]);
    }

    /**
     * @covers \Phug\Lexer\Scanner\MarkupScanner
     * @covers \Phug\Lexer\Scanner\MarkupScanner::scan
     * @covers \Phug\Lexer\Analyzer\LineAnalyzer::<public>
     */
    public function testRawMarkupQuit()
    {
        $state = new State(new Lexer(), 'p', []);
        $scanners = [
            'markup' => MarkupScanner::class,
        ];
        $tokens = [];
        foreach ($state->loopScan($scanners) as $token) {
            $tokens[] = $token;
        }

        self::assertSame(0, count($tokens));
    }
}
