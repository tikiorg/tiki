<?php

class Search_ResultSet extends ArrayObject
{
	private $count;
	private $offset;
	private $maxRecords;

	function __construct($result, $count, $offset, $maxRecords)
	{
		parent::__construct($result);

		$this->count = $count;
		$this->offset = $offset;
		$this->maxRecords = $maxRecords;
	}

	function getMaxRecords()
	{
		return $this->maxRecords;
	}

	function getOffset()
	{
		return $this->offset;
	}

	function count()
	{
		return $this->count;
	}
}

