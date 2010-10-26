<?php

class Search_Index_Memory implements Search_Index_Interface
{
	private $data = array();

	function addDocument(array $data)
	{
		$this->data[] = $data;
	}

	function rawQuery($query)
	{
		return array();
	}

	function getTypeFactory()
	{
		return new Search_Type_Factory_Lucene;
	}

	/**
	 * For test purposes.
	 */
	function size()
	{
		return count($this->data);
	}

	/**
	 * For test purposes.
	 */
	function getDocument($index)
	{
		return $this->data[$index];
	}
}

