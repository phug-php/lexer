<?php

namespace Phug\Test\Lexer\Scanner;

use Phug\Lexer\Token\BlockToken;
use Phug\Lexer\Token\CodeToken;
use Phug\Lexer\Token\CommentToken;
use Phug\Lexer\Token\EachToken;
use Phug\Lexer\Token\ExpressionToken;
use Phug\Lexer\Token\IndentToken;
use Phug\Lexer\Token\NewLineToken;
use Phug\Lexer\Token\OutdentToken;
use Phug\Lexer\Token\TagToken;
use Phug\Lexer\Token\TextToken;
use Phug\Test\AbstractLexerTest;

class BlockScannerTest extends AbstractLexerTest
{
    /**
     * @covers \Phug\Lexer\Scanner\BlockScanner
     * @covers \Phug\Lexer\Scanner\BlockScanner::scan
     */
    public function testScan()
    {
        /** @var BlockToken $tok */
        list($tok) = $this->assertTokens('block some-block', [
            BlockToken::class,
        ]);

        self::assertSame('some-block', $tok->getName());
        self::assertSame('replace', $tok->getMode());

        /** @var BlockToken $tok */
        list($tok) = $this->assertTokens('block append some-block', [
            BlockToken::class,
        ]);

        self::assertSame('some-block', $tok->getName());
        self::assertSame('append', $tok->getMode());

        /** @var BlockToken $tok */
        list($tok) = $this->assertTokens('block prepend some-block', [
            BlockToken::class,
        ]);

        self::assertSame('some-block', $tok->getName());
        self::assertSame('prepend', $tok->getMode());

        /** @var BlockToken $tok */
        list($tok) = $this->assertTokens('block replace some-block', [
            BlockToken::class,
        ]);

        self::assertSame('some-block', $tok->getName());
        self::assertSame('replace', $tok->getMode());

        /** @var BlockToken $tok */
        list($tok) = $this->assertTokens('append some-block', [
            BlockToken::class,
        ]);

        self::assertSame('some-block', $tok->getName());
        self::assertSame('append', $tok->getMode());

        /** @var BlockToken $tok */
        list($tok) = $this->assertTokens('prepend some-block', [
            BlockToken::class,
        ]);

        self::assertSame('some-block', $tok->getName());
        self::assertSame('prepend', $tok->getMode());

        /** @var BlockToken $tok */
        list($tok) = $this->assertTokens('replace some-block', [
            BlockToken::class,
        ]);

        self::assertSame('some-block', $tok->getName());
        self::assertSame('replace', $tok->getMode());

        /** @var BlockToken $tok */
        list($tok) = $this->assertTokens('block', [
            BlockToken::class,
        ]);

        self::assertNull($tok->getName());
        self::assertSame('replace', $tok->getMode());
    }

    /**
     * @covers \Phug\Lexer\Scanner\BlockScanner
     * @covers \Phug\Lexer\Scanner\BlockScanner::scan
     */
    public function testCodeBlock()
    {
        $code = '-
  list = ["uno", "dos", "tres",
          "cuatro", "cinco", "seis"];
//- Without a block, the element is accepted and no code is generated
-
each item in list
  -
    string = item.charAt(0)
    
      .toUpperCase() +
    item.slice(1);
  li= string';

        $this->assertTokens($code, [
            CodeToken::class,
            NewLineToken::class,
            IndentToken::class,
            TextToken::class,
            NewLineToken::class,
            OutdentToken::class,
            CommentToken::class,
            TextToken::class,
            NewLineToken::class,
            CodeToken::class,
            NewLineToken::class,
            EachToken::class,
            NewLineToken::class,
            IndentToken::class,
            CodeToken::class,
            NewLineToken::class,
            IndentToken::class,
            TextToken::class,
            NewLineToken::class,
            OutdentToken::class,
            TagToken::class,
            ExpressionToken::class,
        ]);
    }
}
