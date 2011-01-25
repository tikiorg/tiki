<?php

class TikiDb_Table
{
	private $db;
	private $tableName;

	function __construct($db, $tableName)
	{
		$this->db = $db;
		$this->tableName = $tableName;
	}

	/**
	 * Inserts a row in the table by building the SQL query from an array of values.
	 * The target table is defined by the instance. Argument names are not validated
	 * against the schema. This is only a helper method to improve code readability.
	 *
	 * @param $values array Key-value pairs to insert.
	 */
	function insert(array $values)
	{
		$fieldDefinition = implode(', ', array_map(array($this, 'escapeIdentifier'), array_keys($values)));
		$fieldPlaceholders = rtrim(str_repeat('?, ', count($values)), ' ,');

		$query = "INSERT INTO {$this->escapeIdentifier($this->tableName)} ($fieldDefinition) VALUES ($fieldPlaceholders)";

		$this->db->query($query, array_values($values));

		return $this->db->lastInsertId();
	}

	/**
	 * Deletes a single record from the table matching the provided conditions.
	 * Conditions use exact matching. Multiple conditions will result in AND matching.
	 */
	function delete(array $conditions)
	{
		$bindvars = array();
		$query = $this->buildDelete($conditions, $bindvars) . ' LIMIT 1';

		$this->db->query($query, $bindvars);
	}

	/**
	 * Builds and performs and SQL update query on the table defined by the instance.
	 * This query will update a single record.
	 */
	function update(array $values, array $conditions)
	{
		$bindvars = array();
		$query = $this->buildUpdate($values, $conditions, $bindvars) . ' LIMIT 1';

		$this->db->query($query, $bindvars);
	}

	function updateMultiple(array $values, array $conditions)
	{
		$bindvars = array();
		$query = $this->buildUpdate($values, $conditions, $bindvars);

		$this->db->query($query, $bindvars);
	}


	/**
	 * Deletes a multiple records from the table matching the provided conditions.
	 * Conditions use exact matching. Multiple conditions will result in AND matching.
	 *
	 * The method works just like delete, except that it does not have the one record
	 * limitation.
	 */
	function deleteMultiple(array $conditions)
	{
		$bindvars = array();
		$query = $this->buildDelete($conditions, $bindvars);

		$this->db->query($query, $bindvars);
	}

	function fetchOne($field, array $conditions)
	{
		if ($result = $this->fetchRow(array($field), $conditions)) {
			return reset($result);
		}

		return false;
	}

	function fetchCount(array $conditions)
	{
		return $this->fetchOne($this->count(), $conditions);
	}

	function fetchFullRow(array $conditions)
	{
		return $this->fetchRow($this->all(), $conditions);
	}

	function fetchRow(array $fields, array $conditions)
	{
		$result = $this->fetchAll($fields, $conditions, 1, 0);
		
		return reset($result);
	}

	function fetchColumn($field, array $conditions, $numrows = -1, $offset = -1, $order = null)
	{
		if (! empty($order)) {
			$order = array($field => $order);
		}

		$result = $this->fetchAll(array($field), $conditions, $numrows, $offset, $order);

		$output = array();

		foreach ($result as $row) {
			$output[] = reset($row);
		}

		return $output;
	}

	function fetchMap($keyField, $valueField, array $conditions, $numrows = -1, $offset = -1, $order = null)
	{
		$result = $this->fetchAll(array($keyField, $valueField), $conditions, $numrows, $offset, $order);

		$map = array();

		foreach( $result as $row ) {
			$key = array_shift( $row );
			$value = array_shift( $row );

			$map[ $key ] = $value;
		}

		return $map;
	}

	function fetchAll(array $fields, array $conditions, $numrows = -1, $offset = -1, $orderClause = null)
	{
		$bindvars = array();

		$fieldDescription = '';

		foreach ($fields as $f) {
			if ($f instanceof TikiDB_Expr) {
				$fieldDescription .= $f->getQueryPart(null);
				$bindvars = array_merge($bindvars, $f->getValues());
			} else {
				$fieldDescription .= $this->escapeIdentifier($f);
			}

			$fieldDescription .= ', ';
		}

		$query = 'SELECT ' . rtrim($fieldDescription, ', ') . ' FROM ' . $this->escapeIdentifier($this->tableName);
		$query .= $this->buildConditions($conditions, $bindvars);
		$query .= $this->buildOrderClause($orderClause);

		return $this->db->fetchAll($query, $bindvars, $numrows, $offset);
	}

	function expr($string, $arguments = array())
	{
		return new TikiDb_Expr($string, $arguments);
	}

	function all()
	{
		return array($this->expr('*'));
	}

	function count()
	{
		return $this->expr('COUNT(*)');
	}

	function sum($field)
	{
		return $this->expr("SUM(`$field`)");
	}

	function max($field)
	{
		return $this->expr("MAX(`$field`)");
	}

	function increment($count)
	{
		return $this->expr('$$ + ?', array($count));
	}

	function decrement($count)
	{
		return $this->expr('$$ - ?', array($count));
	}

	function greaterThan($value)
	{
		return $this->expr('$$ > ?', array($value));
	}

	function lesserThan($value)
	{
		return $this->expr('$$ < ?', array($value));
	}

	function not($value)
	{
		return $this->expr('$$ <> ?', array($value));
	}

	function like($value)
	{
		return $this->expr('$$ LIKE ?', array($value));
	}

	function unlike($value)
	{
		return $this->expr('$$ NOT LIKE ?', array($value));
	}

	function exactly($value)
	{
		return $this->expr('BINARY $$ = ?', array($value));
	}

	function in(array $values)
	{
		if (empty($values)) {
			return $this->expr('1=0', array());
		} else {
			return $this->expr('$$ IN(' . rtrim(str_repeat('?, ', count($values)), ', ') . ')', $values);
		}
	}

	private function buildDelete(array $conditions, & $bindvars)
	{
		$query = "DELETE FROM {$this->escapeIdentifier($this->tableName)}";
		$query .= $this->buildConditions($conditions, $bindvars);

		return $query;
	}

	private function buildConditions(array $conditions, & $bindvars)
	{ 
		$query = " WHERE 1=1";

		foreach ($conditions as $key => $value) {
			$field = $this->escapeIdentifier($key);
			if ($value instanceof TikiDb_Expr) {
				$query .= " AND {$value->getQueryPart($field)}";
				$bindvars = array_merge($bindvars, $value->getValues());
			} elseif (empty($value)) {
				$query .= " AND ($field = ? OR $field IS NULL)";
				$bindvars[] = $value;
			} else {
				$query .= " AND $field = ?";
				$bindvars[] = $value;
			}
		}

		return $query;
	}

	private function buildOrderClause($orderClause)
	{
		if ($orderClause instanceof TikiDb_Expr) {
			return ' ORDER BY ' . $orderClause->getQueryPart();
		} elseif (is_array($orderClause) && ! empty($orderClause)) {
			$part = ' ORDER BY ';

			foreach ($orderClause as $key => $direction) {
				$part .= "`$key` $direction, ";
			}

			return rtrim($part, ', ');
		}
	}

	private function buildUpdate(array $values, array $conditions, & $bindvars)
	{
		$query = "UPDATE {$this->escapeIdentifier($this->tableName)} SET ";

		foreach ($values as $key => $value) {
			$field = $this->escapeIdentifier($key);
			if ($value instanceof TikiDb_Expr) {
				$query .= "$field = {$value->getQueryPart($field)}, ";
				$bindvars = array_merge($bindvars, $value->getValues());
			} else {
				$query .= "$field = ?, ";
				$bindvars[] = $value;
			}
		}

		$query = rtrim($query, ' ,') . $this->buildConditions($conditions, $bindvars);

		return $query;
	}

	private function escapeIdentifier($identifier)
	{
		return "`$identifier`";
	}
}

