<?php

namespace Phug\Test;

use Phug\Lexer;
use Phug\Lexer\State;
use Phug\Lexer\Scanner\TextLineScanner;

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
        include_once __DIR__ . '/MockScanner.php';

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
     * @covers ::addScanner
     */
    public function testAddScanner()
    {
        include_once __DIR__ . '/MockScanner.php';

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
     * @covers                   ::lex
     * @expectedException        \InvalidArgumentException
     * @expectedExceptionMessage state_class_name needs to be a valid Phug\Lexer\State sub class
     */
    public function testBadStateClassName()
    {
        $lexer = new Lexer([
            'state_class_name' => 'NotAValidClassName',
        ]);
        foreach ($lexer->lex('p') as $token) {
        }
    }
}
