<?php

class Search_ResultSet extends ArrayObject
{
	private $count;
	private $offset;
	private $maxRecords;

	private $highlightHelper;

	function __construct($result, $count, $offset, $maxRecords)
	{
		parent::__construct($result);

		$this->count = $count;
		$this->offset = $offset;
		$this->maxRecords = $maxRecords;
	}

	function setHighlightHelper(Zend_Filter_Interface $helper)
	{
		$this->highlightHelper = $helper;
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

	function highlight($content)
	{
		if ($this->highlightHelper) {
			// Build the content string based on heuristics
			$text = '';
			foreach ($content as $key => $value) {
				if ($key != 'object_type' // Skip internal values
				 && $key != 'object_id'
				 && ! empty($value) // Skip empty
				 && ! is_array($value) // Skip arrays, multivalues fields are not human readable
				 && ! preg_match('/^[\w-]+$/', $value)) { // Skip anything that looks like a single token
					$text .= "\n$value";
				}
			}

			if (! empty($text)) {
				return $this->highlightHelper->filter($text);
			}
		}
	}
}

