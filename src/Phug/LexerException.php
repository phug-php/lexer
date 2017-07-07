<?php

namespace Phug;

use Phug\Util\Partial\PugFileLocationTrait;
use Phug\Util\PugFileLocationInterface;

/**
 * Represents an exception that is thrown during the lexical analysis.
 *
 * This exception is thrown when the lexer encounters invalid token relations
 */
class LexerException extends \Exception implements PugFileLocationInterface
{
    use PugFileLocationTrait;

    public function __construct($message, $code, $previous, $line, $offset)
    {
        $this->setPugLine($line);
        $this->setPugOffset($offset);
        parent::__construct($message, $code, $previous);
    }
}
