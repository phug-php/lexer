<?php

namespace Phug\Test\Lexer;

use Phug\Lexer;
use Phug\Lexer\Token\AssignmentToken;
use Phug\Lexer\TokenInterface;


abstract class TokenTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @covers Phug\Lexer\Token\AssignmentToken
     * @covers Phug\Lexer\Token\AssignmentToken::setName
     * @covers Phug\Lexer\Token\AssignmentToken::getName
     */
    public function testAssignmentToken()
    {

        /** @var AssignmentToken $tok */
        $tok = $this->createAndTestToken(AssignmentToken::class);
        $tok->setName('some name');

        self::assertEquals('some name', $tok->getName());
    }

    protected function createAndTestToken($className)
    {

        $line = mt_rand(0, 100);
        $offset = mt_rand(0, 100);
        $level = mt_rand(0, 8);

        /** @var TokenInterface $tok */
        $tok = new $className($line, $offset, $level);

        self::assertEquals($line, $tok->getLine(), "{$className}->getLine");
        self::assertEquals($offset, $tok->getOffset(), "{$className}->getOffset");
        self::assertEquals($offset, $tok->getLevel(), "{$className}->getLevel");

        return $tok;
    }
}
