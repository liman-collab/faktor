<?php

namespace FSProVendor\WPDesk\Forms;

interface Escaper
{
    /**
     * @param mixed $value
     *
     * @return string
     */
    public function escape($value);
}
