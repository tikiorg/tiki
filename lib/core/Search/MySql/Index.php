<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Search_MySql_Index implements Search_Index_Interface
{
	private $db;
	private $table;
	private $builder;

	private $providedMappings = array();

	function __construct(TikiDb $db, $index)
	{
		$this->table = new Search_MySql_Table($db, $index);
		$this->builder = new Search_MySql_QueryBuilder($db);
	}

	function destroy()
	{
		$this->table->drop();
		return true;
	}

	function exists()
	{
		return $this->table->exists();
	}

	function addDocument(array $data)
	{
		foreach ($data as $key => $value) {
			$this->handleField($key, $value);
		}

		$data = array_map(
			function ($entry) {
				return $entry->getValue();
			}, $data
		);

		$this->table->insert($data);
	}

	private function handleField($name, $value)
	{
		if ($value instanceof Search_Type_Whole) {
			$this->table->ensureHasField($name, 'VARCHAR(200)', array('index'));
		} elseif ($value instanceof Search_Type_PlainShortText) {
			$this->table->ensureHasField($name, 'VARCHAR(300)', array('index', 'fulltext'));
		} elseif ($value instanceof Search_Type_PlainText) {
			$this->table->ensureHasField($name, 'TEXT', array('fulltext'));
		} elseif ($value instanceof Search_Type_WikiText) {
			$this->table->ensureHasField($name, 'TEXT', array('fulltext'));
		} elseif ($value instanceof Search_Type_MultivalueText) {
			$this->table->ensureHasField($name, 'TEXT', array('fulltext'));
		} elseif ($value instanceof Search_Type_Timestamp) {
			$this->table->ensureHasField($name, 'DATETIME', array('index'));
		} else {
			throw new Exception('Unsupported type: ' . get_class($value));
		}
	}

	function endUpdate()
	{
	}

	function optimize()
	{
	}

	function invalidateMultiple(array $objectList)
	{
		foreach ($objectList as $object) {
			$this->table->deleteMultiple($object);
		}
	}

	function find(Search_Query_Interface $query, $resultStart, $resultCount)
	{
		$order = $this->getOrderClause($query);

		$condition = $this->builder->build($query->getExpr());
		$conditions = array(
			$this->table->expr($condition),
		);
		$count = $this->table->fetchCount($conditions);
		$entries = $this->table->fetchAll($this->table->all(), $conditions, $resultCount, $resultStart, $order);

		$resultSet = new Search_ResultSet($entries, $count, $resultStart, $resultCount);
		$resultSet->setHighlightHelper(new Search_MySql_HighlightHelper($query->getExpr()));

		return $resultSet;
	}

	private function getOrderClause($query)
	{
		$order = $query->getSortOrder();

		if ($order->getField() == Search_Query_Order::FIELD_SCORE) {
			return null;
		}

		if ($order->getMode() == Search_Query_Order::MODE_NUMERIC) {
			return $this->table->expr("CAST(`{$order->getField()}` as SIGNED) {$order->getOrder()}");
		} else {
			return $this->table->expr("`{$order->getField()}` {$order->getOrder()}");
		}
	}

	function getTypeFactory()
	{
		return new Search_MySql_TypeFactory;
	}
}

