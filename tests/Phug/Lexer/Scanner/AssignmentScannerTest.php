<?php

namespace Phug\Test\Lexer\Scanner;

use Phug\Lexer\Token\AssignmentToken;
use Phug\Lexer\Token\AttributeEndToken;
use Phug\Lexer\Token\AttributeStartToken;
use Phug\Lexer\Token\AttributeToken;
use Phug\Test\AbstractLexerTest;

class AssignmentScannerTest extends AbstractLexerTest
{
    /**
     * @covers Phug\Lexer\Scanner\AssignmentScanner
     * @covers Phug\Lexer\Scanner\AssignmentScanner::scan
     */
    public function testScan()
    {

        /** @var AssignmentToken $tok */
        list($tok) = $this->assertTokens('&test', [
            AssignmentToken::class,
        ]);

        self::assertSame('test', $tok->getName());
    }

    public function testScanWithAttributes()
    {

        /** @var AssignmentToken $tok */
        list($tok) = $this->assertTokens('&test(a=a b=b c=c)', [
            AssignmentToken::class,
            AttributeStartToken::class,
            AttributeToken::class,
            AttributeToken::class,
            AttributeToken::class,
            AttributeEndToken::class,
        ]);

        self::assertSame('test', $tok->getName());
    }
}
