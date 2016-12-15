<?php

namespace Phug\Test;

use Phug\Lexer;
use Phug\Lexer\Token\AssignmentToken;
use Phug\Lexer\Token\AttributeEndToken;
use Phug\Lexer\Token\AttributeStartToken;
use Phug\Lexer\Token\AttributeToken;
use Phug\Lexer\Token\DoctypeToken;
use Phug\LexerException;

/**
 * @coversDefaultClass Phug\Lexer
 */
class LexerTest extends AbstractLexerTest
{

    /**
     * @covers ::lex
     */
    public function testDoctypeScan()
    {

        $this->assertTokens(
            'doctype 5',
            [DoctypeToken::class]
        );

        $this->assertTokens(
            '!!! 5',
            [DoctypeToken::class]
        );
    }
}
