<?php

namespace Phug\Lexer\Token\Partial;

use SplObjectStorage;

trait AssignmentTrait
{

    private $assignments = null;

    public function getAssignments()
    {

        if (!$this->assignments) {
            $this->assignments = new SplObjectStorage;
        }

        return $this->assignments;
    }
}
