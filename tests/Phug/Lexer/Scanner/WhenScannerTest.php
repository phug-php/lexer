<?php

namespace Phug\Test\Lexer\Scanner;

use Phug\Lexer\Token\ExpansionToken;
use Phug\Lexer\Token\TagToken;
use Phug\Lexer\Token\TextToken;
use Phug\Lexer\Token\WhenToken;

class WhenScannerTest extends AbstractControlStatementScannerTest
{

    protected function getTokenClassName()
    {

        return WhenToken::class;
    }

    protected function getStatementName()
    {

        return 'when';
    }

    public function testDefault()
    {

        /** @var WhenToken $tok */
        list($tok) = $this->assertTokens('default: p Do something', [
            WhenToken::class,
            ExpansionToken::class,
            TagToken::class,
            TextToken::class
        ]);

        self::assertEquals('default', $tok->getName());
        self::assertEquals('', $tok->getSubject());
    }
}