<?php

namespace Phug\Test\Lexer\Token;

use Phug\Lexer\Token\TagInterpolationEndToken;
use Phug\Lexer\Token\TagInterpolationStartToken;

/**
 * @coversDefaultClass \Phug\Lexer\Token\TagInterpolationStartToken
 */
class TagInterpolationStartTokenTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::<public>
     */
    public function testEnd()
    {
        $start = new TagInterpolationStartToken();
        $end = new TagInterpolationEndToken();

        self::assertSame(null, $start->getEnd());
        $start->setEnd($end);
        self::assertSame($end, $start->getEnd());
    }
}
