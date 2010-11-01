<?php

class Search_Index_Memory implements Search_Index_Interface
{
	private $data = array();
	private $lastQuery;
	private $lastOrder;

	function addDocument(array $data)
	{
		$this->data[] = $data;
	}

	function find(Search_Expr_Interface $query, Search_Query_Order $sortOrder)
	{
		$this->lastQuery = $query;
		$this->lastOrder = $sortOrder;
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

	/**
	 * For test purposes.
	 */
	function getLastQuery()
	{
		return $this->lastQuery;
	}

	/**
	 * For test purposes.
	 */
	function getLastOrder()
	{
		return $this->lastOrder;
	}
}

