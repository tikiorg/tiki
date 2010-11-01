<?php

class Search_ResultSet extends ArrayObject
{
	private $count;

	function __construct($result, $count)
	{
		parent::__construct($result);

		$this->count = $count;
	}

	function count()
	{
		return $this->count;
	}
}

