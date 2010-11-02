<?php

class Search_Index_Memory implements Search_Index_Interface
{
	private $data = array();
	private $lastQuery;
	private $lastOrder;
	private $lastStart;
	private $lastCount;

	function addDocument(array $data)
	{
		$this->data[] = $data;
	}

	function invalidateMultiple(array $objectList)
	{
		$toRemove = array();

		foreach ($objectList as $object) {
			foreach ($this->data as $key => $entry) {
				if ($entry['object_type']->getValue() == $object['object_type']
					&& $entry['object_id']->getValue() == $object['object_id']) {

					$toRemove[] = $key;
				}
			}
		}

		foreach ($toRemove as $key) {
			unset($this->data[$key]);
		}

		$this->data = array_values($this->data);
	}

	function find(Search_Expr_Interface $query, Search_Query_Order $sortOrder, $resultStart, $resultCount)
	{
		$this->lastQuery = $query;
		$this->lastOrder = $sortOrder;
		$this->lastStart = $resultStart;
		$this->lastCount = $resultCount;
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

	/**
	 * For test purposes.
	 */
	function getLastStart()
	{
		return $this->lastStart;
	}

	/**
	 * For test purposes.
	 */
	function getLastCount()
	{
		return $this->lastCount;
	}
}

