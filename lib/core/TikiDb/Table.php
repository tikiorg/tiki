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

	function insert(array $parameters)
	{
		$fieldDefinition = implode(', ', array_map(array($this, 'escapeIdentifier'), array_keys($parameters)));
		$fieldPlaceholders = rtrim(str_repeat('?, ', count($parameters)), ' ,');

		$query = "INSERT INTO {$this->escapeIdentifier($this->tableName)} ($fieldDefinition) VALUES ($fieldPlaceholders)";

		$this->db->query($query, array_values($parameters));

		return $this->db->lastInsertId();
	}

	private function escapeIdentifier($identifier)
	{
		return "`$identifier`";
	}
}

