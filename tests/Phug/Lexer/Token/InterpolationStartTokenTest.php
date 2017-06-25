<?php

namespace Phug\Test\Lexer\Token;

use Phug\Lexer\Token\InterpolationEndToken;
use Phug\Lexer\Token\InterpolationStartToken;

/**
 * @coversDefaultClass \Phug\Lexer\Token\InterpolationStartToken
 */
class InterpolationStartTokenTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::<public>
     */
    public function testEnd()
    {
        $start = new InterpolationStartToken();
        $end = new InterpolationEndToken();

        self::assertSame(null, $start->getEnd());
        $start->setEnd($end);
        self::assertSame($end, $start->getEnd());
    }
}
