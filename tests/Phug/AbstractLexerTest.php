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
        
        return new Lexer;
    }
    
    protected function assertTokens($expression, array $classNames)
    {

        return self::assertGenerator($this->lexer->lex($expression), $classNames);
    }

    protected static function assertGenerator(\Generator $generator, array $classNames)
    {

        $data = iterator_to_array($generator);

        self::assertEquals(
            count($data),
            count($classNames),
            'same amount of values ('
            .implode(', ', array_map('get_class', $data))
            .' vs. '
            .implode(', ', $classNames)
            .')'
        );

        foreach ($data as $i => $item) {

            $isset = isset($classNames[$i]);
            self::assertTrue($isset, "class name exists at $i");

            if ($isset) {
                self::assertInstanceOf($classNames[$i], $item, "token is {$classNames[$i]}");
            }
        }

        return $data;
    }
}
