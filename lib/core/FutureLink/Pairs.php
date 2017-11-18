<?php

class FutureLink_Pairs
{
	public $entry = [];
	public $length = 0;

	public function add(FutureLink_Pair $metadata)
	{
		$this->entry[] = $metadata;
		$this->length++;
	}
}
