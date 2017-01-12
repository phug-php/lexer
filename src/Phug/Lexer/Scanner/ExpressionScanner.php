<?php

namespace Phug\Lexer\Scanner;

use Phug\Lexer\ScannerInterface;
use Phug\Lexer\State;
use Phug\Lexer\Token\ExpressionToken;

class ExpressionScanner implements ScannerInterface
{
    public function scan(State $state)
    {
        $reader = $state->getReader();

        if (!$reader->match('[\t ]*(?:\?|\!|\?\!)?=[\t ]*')) {
            return;
        }

        $prefix = $reader->consume();

        /** @var ExpressionToken $token */
        $token = $state->createToken(ExpressionToken::class);

        if (mb_strpos($prefix, '!') === false) {
            $token->escape();
        }

        if (mb_strpos($prefix, '?') !== false) {
            $token->uncheck();
        }

        $token->setValue($reader->readExpression(["\n", '//']));

        yield $token;
    }
}
