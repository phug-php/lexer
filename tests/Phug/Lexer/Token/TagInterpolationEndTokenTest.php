<?php

namespace Phug\Test\Lexer\Token;

use Phug\Lexer\Token\TagInterpolationEndToken;
use Phug\Lexer\Token\TagInterpolationStartToken;

/**
 * @coversDefaultClass \Phug\Lexer\Token\TagInterpolationEndToken
 */
class TagInterpolationEndTokenTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::<public>
     */
    public function testStart()
    {
        $start = new TagInterpolationStartToken();
        $end = new TagInterpolationEndToken();

        self::assertSame(null, $end->getStart());
        $end->setStart($start);
        self::assertSame($start, $end->getStart());
    }
}
