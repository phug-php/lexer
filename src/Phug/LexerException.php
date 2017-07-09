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

    public function __construct($message = '', $code = 0, $previous = null, $line = null, $offset = null)
    {
        $this->setPugLine($line);
        $this->setPugOffset($offset);
        parent::__construct($message, intval($code), $previous);
    }
}
