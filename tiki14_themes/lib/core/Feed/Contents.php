<?php

class Feed_Contents
{
    public $date = 0;
    public $type;
    public $entry = array();

    public function __construct($type)
    {
        $this->type = $type;
    }
}