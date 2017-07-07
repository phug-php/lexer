<?php

namespace Phug\Test;

use Exception;
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

    protected function expectMessageToBeThrown($message)
    {
        if (method_exists($this, 'expectExceptionMessage')) {
            $this->expectExceptionMessage($message);

            return;
        }

        $this->setExpectedException(Exception::class, $message, null);
    }

    protected function createLexer()
    {
        return new Lexer();
    }

    protected function filterTokenClass($className)
    {
        $className = ltrim($className, '\\');
        switch ($className) {
            case 'Phug\\Lexer\\Token\\IndentToken':
                return '[->]';
            case 'Phug\\Lexer\\Token\\OutdentToken':
                return '[<-]';
            case 'Phug\\Lexer\\Token\\NewLineToken':
                return '[\\n]';
            default:
                return preg_replace('/^(Phug\\\\.+)Token$/', '[$1]', $className);
        }
    }

    protected function assertTokens($expression, array $classNames, Lexer $lexer = null)
    {
        $tokens = iterator_to_array(($lexer ?: $this->lexer)->lex($expression));

        self::assertSame(
            count($tokens),
            count($classNames),
            "\n"
            .'expected ('
            .implode(', ', array_map([$this, 'filterTokenClass'], $classNames))
            .'), '
            ."\n"
            .'got      ('
            .implode(', ', array_map('trim', array_map([$this->lexer, 'dump'], $tokens)))
            .')'
        );

        foreach ($tokens as $i => $token) {
            $isset = isset($classNames[$i]);
            self::assertTrue($isset, "Classname at $i exists");

            if ($isset) {
                self::assertInstanceOf($classNames[$i], $token, "token[$i] should be {$classNames[$i]}");
            }
        }

        return $tokens;
    }
}
