<?php

namespace Phug\Test;

use Phug\Lexer;
use Phug\Lexer\Scanner\TextLineScanner;
use Phug\Lexer\State;
use Phug\Lexer\Token\AttributeEndToken;
use Phug\Lexer\Token\AttributeStartToken;
use Phug\Lexer\Token\AttributeToken;
use Phug\Lexer\Token\BlockToken;
use Phug\Lexer\Token\ExpressionToken;
use Phug\Lexer\Token\IndentToken;
use Phug\Lexer\Token\NewLineToken;
use Phug\Lexer\Token\OutdentToken;
use Phug\Lexer\Token\TextToken;

/**
 * @coversDefaultClass \Phug\Lexer
 */
class LexerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct
     * @covers ::getScanners
     */
    public function testGetScanners()
    {
        $lexer = new Lexer([
            'scanners' => [
                'indent' => TextLineScanner::class,
            ],
        ]);
        $indent = $lexer->getScanners()['indent'];

        self::assertSame(TextLineScanner::class, $indent);
    }

    /**
     * @covers                   ::getState
     * @expectedException        \RuntimeException
     * @expectedExceptionMessage Failed to get state: No lexing process active. Use the `lex()`-method
     */
    public function testGetStateException()
    {
        $lexer = new Lexer();
        $lexer->getState();
    }

    /**
     * @covers ::lex
     * @covers ::getState
     */
    public function testGetState()
    {
        include_once __DIR__.'/MockScanner.php';

        $mock = new MockScanner();
        $lexer = new Lexer([
            'scanners' => [
                'tag' => $mock,
            ],
        ]);
        $mock->setLexer($lexer);

        foreach ($lexer->lex('p') as $token) {
        }

        self::assertInstanceOf(State::class, $mock->getState());
    }

    /**
     * @covers ::filterScanner
     * @covers ::addScanner
     */
    public function testAddScanner()
    {
        include_once __DIR__.'/MockScanner.php';

        $lexer = new Lexer();
        $self = $lexer->addScanner('foo', MockScanner::class);
        $scanners = $lexer->getScanners();

        self::assertSame(MockScanner::class, end($scanners));
        self::assertSame($lexer, $self);

        $lexer = new Lexer();
        $self = $lexer->addScanner('foo', MockScanner::class, true);
        $scanners = $lexer->getScanners();
        foreach ($scanners as $scanner) {
            break;
        }

        self::assertSame(MockScanner::class, $scanner);
        self::assertSame($lexer, $self);
    }

    /**
     * @covers                   ::filterScanner
     * @expectedException        \InvalidArgumentException
     * @expectedExceptionMessage Scanner NotAValidClassName is not a valid
     * @expectedExceptionMessage Phug\Lexer\ScannerInterface instance or extended class
     * @expectedExceptionMessage instance or extended class
     */
    public function testFilterScanner()
    {
        $lexer = new Lexer();
        $lexer->addScanner('foo', 'NotAValidClassName');
        foreach ($lexer->lex('p') as $token) {
        }
    }

    /**
     * @covers                   ::lex
     * @expectedException        \InvalidArgumentException
     * @expectedExceptionMessage state_class_name needs to be a valid
     * @expectedExceptionMessage Phug\Lexer\State sub class
     */
    public function testBadStateClassName()
    {
        $lexer = new Lexer([
            'state_class_name' => 'NotAValidClassName',
        ]);
        foreach ($lexer->lex('p') as $token) {
        }
    }

    /**
     * @covers ::dump
     * @covers ::dumpToken
     * @covers ::getTokenName
     */
    public function testDump()
    {
        $lexer = new Lexer();
        $attr = new AttributeToken();
        $attr->setName('foo');
        $attr->setValue('bar');
        $text = new TextToken();
        $text->setValue('bla');
        $exp = new ExpressionToken();
        $exp->setValue('$foo');

        self::assertSame('[)]', $lexer->dump(new AttributeEndToken()));
        self::assertSame('[(]', $lexer->dump(new AttributeStartToken()));
        self::assertSame('[Attr foo=bar (unescaped, checked)]', $lexer->dump($attr));
        self::assertSame('[Expr $foo (unescaped, checked)]', $lexer->dump($exp));
        self::assertSame('[->]', $lexer->dump(new IndentToken()));
        self::assertSame('[<-]', $lexer->dump(new OutdentToken()));
        self::assertSame("[\\n]\n", $lexer->dump(new NewLineToken()));
        self::assertSame('[Text bla]', $lexer->dump($text));
        self::assertSame('[Phug\Lexer\Token\Block]', $lexer->dump(new BlockToken()));
        self::assertSame('[Phug\Lexer\Token\Tag][Text Hello]', $lexer->dump('p Hello'));
    }
}
