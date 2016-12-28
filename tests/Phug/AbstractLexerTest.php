<?php

namespace Phug\Test;

use Phug\Lexer;

abstract class AbstractLexerTest extends \PHPUnit_Framework_TestCase
{
    /** @var Lexer */
    protected $lexer;

    public function setUp()
    {
        parent::setUp();

        $this->lexer = $this->createLexer();
    }

    protected function createLexer()
    {
        return new Lexer();
    }

    protected function assertTokens($expression, array $classNames)
    {
        $tokens = iterator_to_array($this->lexer->lex($expression));

        self::assertEquals(
            count($tokens),
            count($classNames),
            "\n"
            .'expected ('
            .implode(', ', $classNames)
            .'), '
            ."\n"
            .'got      ('
            .implode(', ', array_map([$this->lexer, 'dump'], $tokens))
            .')'
        );

        foreach ($tokens as $i => $token) {
            $isset = isset($classNames[$i]);
            self::assertTrue($isset, "Classname at $i exists");

            if ($isset) {
                self::assertInstanceOf($classNames[$i], $token, "token is {$classNames[$i]}");
            }
        }

        return $tokens;
    }
}
