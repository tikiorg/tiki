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
		$query = $this->buildDelete($conditions) . ' LIMIT 1';

		$this->db->query($query, array_values($conditions));
	}

	/**
	 * Builds and performs and SQL update query on the table defined by the instance.
	 * This query will update a single record.
	 */
	function update(array $values, array $conditions)
	{
		$query = $this->buildUpdate($values, $conditions) . ' LIMIT 1';

		$this->db->query($query, array_merge(array_values($values), array_values($conditions)));
	}

	function updateMultiple(array $values, array $conditions)
	{
		$query = $this->buildUpdate($values, $conditions);

		$this->db->query($query, array_merge(array_values($values), array_values($conditions)));
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
		$query = $this->buildDelete($conditions);

		$this->db->query($query, array_values($conditions));
	}

	private function buildDelete(array $conditions)
	{
		$query = "DELETE FROM {$this->escapeIdentifier($this->tableName)}";
		$query .= $this->buildConditions($conditions);

		return $query;
	}

	private function buildConditions(array $conditions)
	{ 
		$query = " WHERE 1=1";

		foreach ($conditions as $key => $value) {
			$field = $this->escapeIdentifier($key);
			if (empty($value)) {
				$query .= " AND ($field = ? OR $field IS NULL)";
			} else {
				$query .= " AND $field = ?";
			}
		}

		return $query;
	}

	private function buildUpdate(array $values, array $conditions)
	{
		$query = "UPDATE {$this->escapeIdentifier($this->tableName)} SET ";

		foreach ($values as $key => $value) {
			$field = $this->escapeIdentifier($key);
			$query .= "$field = ?, ";
		}

		$query = rtrim($query, ' ,') . $this->buildConditions($conditions);

		return $query;
	}

	private function escapeIdentifier($identifier)
	{
		return "`$identifier`";
	}
}

