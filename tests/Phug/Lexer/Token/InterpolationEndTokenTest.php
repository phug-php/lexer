<?php

namespace Phug\Test\Lexer\Token;

use Phug\Lexer\Token\InterpolationEndToken;
use Phug\Lexer\Token\InterpolationStartToken;

/**
 * @coversDefaultClass \Phug\Lexer\Token\InterpolationEndToken
 */
class InterpolationEndTokenTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::<public>
     */
    public function testStart()
    {
        $start = new InterpolationStartToken();
        $end = new InterpolationEndToken();

        self::assertSame(null, $end->getStart());
        $end->setStart($start);
        self::assertSame($start, $end->getStart());
    }
}
