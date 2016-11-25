<?php

namespace Phug\Lexer\Token\Partial;

trait FilterTrait
{

    private $filter = null;

    /**
     * @return string
     */
    public function getFilter()
    {
        return $this->filter;
    }

    /**
     * @param string $filter
     *
     * @return $this
     */
    public function setFilter($filter)
    {
        $this->filter = $filter;

        return $this;
    }
}
