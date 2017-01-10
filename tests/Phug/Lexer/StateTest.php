<?php

namespace Phug\Test\Lexer;

use Phug\Lexer;
use Phug\Lexer\State;
use Phug\Lexer\Token\BlockToken;
use Phug\Reader;

/**
 * @coversDefaultClass \Phug\Lexer\State
 */
class StateTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers                   ::__construct
     * @expectedException        \InvalidArgumentException
     * @expectedExceptionMessage Configuration option `reader_class_name`
     * @expectedExceptionMessage needs to be a valid FQCN of a class
     * @expectedExceptionMessage that extends Phug\Reader
     */
    public function testBadReaderClass()
    {
        new State('p Hello', [
            'reader_class_name' => 'NotAValidClassName',
        ]);
    }

    /**
     * @covers ::__construct
     * @covers ::getReader
     */
    public function testGetReader()
    {
        $state = new State('p Hello', []);

        self::assertInstanceOf(Reader::class, $state->getReader());
    }

    /**
     * @covers ::__construct
     * @covers ::getIndentStyle
     */
    public function testGetIndentStyle()
    {
        $state = new State('p Hello', [
            'indent_style' => null,
        ]);

        self::assertSame(null, $state->getIndentStyle());
    }

    /**
     * @covers ::__construct
     * @covers ::setIndentStyle
     * @covers ::getIndentStyle
     */
    public function testSetIndentStyle()
    {
        $state = new State('p Hello', []);
        $state->setIndentStyle(Lexer::INDENT_TAB);

        self::assertSame(Lexer::INDENT_TAB, $state->getIndentStyle());
    }

    /**
     * @covers                   ::setIndentStyle
     * @expectedException        \InvalidArgumentException
     * @expectedExceptionMessage indentStyle needs to be null or one of the INDENT_* constants of the lexer
     */
    public function testSetIndentStyleException()
    {
        $state = new State('p Hello', []);
        $state->setIndentStyle(42);
    }

    /**
     * @covers ::getIndentWidth
     */
    public function testGetIndentWidth()
    {
        $state = new State('p Hello', [
            'indent_width' => null,
        ]);

        self::assertSame(null, $state->getIndentWidth());
    }

    /**
     * @covers ::setIndentWidth
     */
    public function testSetIndentWidth()
    {
        $state = new State('p Hello', [
            'indent_width' => null,
        ]);
        $state->setIndentWidth(42);

        self::assertSame(42, $state->getIndentWidth());
    }

    /**
     * @covers                   ::setIndentWidth
     * @expectedException        \InvalidArgumentException
     * @expectedExceptionMessage indentWidth needs to be null or an integer above 0
     */
    public function testSetIndentWidthException()
    {
        $state = new State('p Hello', []);
        $state->setIndentWidth(-1);
    }

    /**
     * @covers ::createToken
     */
    public function testCreateToken()
    {
        $state = new State('p Hello', []);
        $block = $state->createToken(BlockToken::class);

        self::assertInstanceOf(BlockToken::class, $block);
    }

    /**
     * @covers                   ::createToken
     * @covers                   ::throwException
     * @expectedException        \Phug\LexerException
     * @expectedExceptionMessage Failed to lex: bar
     * @expectedExceptionMessage Near: p Hello
     * @expectedExceptionMessage Line: 1
     * @expectedExceptionMessage Offset: 0
     * @expectedExceptionMessage Position: 0
     * @expectedExceptionMessage Path: foo
     */
    public function testCreateTokenException()
    {
        $state = new State('p Hello', [
            'path' => 'foo',
        ]);
        $state->createToken('bar');
    }
}
