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

	function expr($string, $arguments)
	{
		return new TikiDb_Expr($string, $arguments);
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

	function like($value)
	{
		return $this->expr('$$ LIKE ?', array($value));
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

