<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Search_MySql_Table extends TikiDb_Table
{
	private $definition = false;
	private $indexes = array();
	private $exists = null;

	function drop()
	{
		$table = $this->escapeIdentifier($this->tableName);
		$this->db->query("DROP TABLE IF EXISTS $table");
		$this->definition = false;
		$this->exists = false;
	}

	function exists()
	{
		if (is_null($this->exists)) {
			$tables = $this->db->listTables();
			$this->exists = in_array($this->tableName, $tables);
		}

		return $this->exists;
	}

	function ensureHasField($fieldName, $type)
	{
		$this->loadDefinition();

		if (! isset($this->definition[$fieldName])) {
			$this->addField($fieldName, $type);
			$this->definition[$fieldName] = $type;
		}
	}

	function hasIndex($fieldName, $type)
	{
		$this->loadDefinition();

		$indexName = $fieldName . '_' . $type;
		return isset($this->indexes[$indexName]);
	}

	function ensureHasIndex($fieldName, $type)
	{
		$this->loadDefinition();

		$indexName = $fieldName . '_' . $type;

		// Static MySQL limit on 64 indexes per table
		if (! isset($this->indexes[$indexName]) && count($this->indexes) < 64) {
			if ($type == 'fulltext') {
				$this->addFullText($fieldName);
			} elseif ($type == 'index') {
				$this->addIndex($fieldName);
			}

			$this->indexes[$indexName] = true;
		}
	}

	private function loadDefinition()
	{
		if (! empty($definition)) {
			return;
		}

		if (! $this->exists()) {
			$this->createTable();
			$this->loadDefinition();
		}

		$table = $this->escapeIdentifier($this->tableName);
		$result = $this->db->fetchAll("DESC $table");
		$this->definition = array();
		foreach ($result as $row) {
			$this->definition[$row['Field']] = $row['Type'];
		}

		$result = $this->db->fetchAll("SHOW INDEXES FROM $table");
		$this->indexes = array();
		foreach ($result as $row) {
			$this->indexes[$row['Key_name']] = true;
		}
	}

	private function createTable()
	{
		$table = $this->escapeIdentifier($this->tableName);
		$this->db->query("CREATE TABLE IF NOT EXISTS $table (
			`id` INT NOT NULL AUTO_INCREMENT,
			`object_type` VARCHAR(15) NOT NULL,
			`object_id` VARCHAR(300) NOT NULL,
			PRIMARY KEY(`id`),
			INDEX (`object_type`, `object_id`)
		) ENGINE=MyISAM");
		$this->exists = true;
	}

	private function addField($fieldName, $type)
	{
		$table = $this->escapeIdentifier($this->tableName);
		$fieldName = $this->escapeIdentifier($fieldName);
		$this->db->queryError("ALTER TABLE $table ADD COLUMN $fieldName $type", $error);

		if ($error) {
			throw new Search_MySql_LimitReachedException(tr("Database too large for index type. Limit reached."));
		}
	}

	private function addIndex($fieldName)
	{
		$table = $this->escapeIdentifier($this->tableName);
		$indexName = $this->escapeIdentifier($fieldName . '_index');
		$fieldName = $this->escapeIdentifier($fieldName);
		$this->db->queryError("ALTER TABLE $table ADD INDEX $indexName ($fieldName)", $error);

		if ($error) {
			throw new Search_MySql_LimitReachedException(tr("Too many indexes required. Limit reached."));
		}
	}

	private function addFullText($fieldName)
	{
		$table = $this->escapeIdentifier($this->tableName);
		$indexName = $this->escapeIdentifier($fieldName . '_fulltext');
		$fieldName = $this->escapeIdentifier($fieldName);
		$this->db->queryError("ALTER TABLE $table ADD FULLTEXT INDEX $indexName ($fieldName)", $error);

		if ($error) {
			throw new Search_MySql_LimitReachedException(tr("Too many indexes required. Limit reached."));
		}
	}
}

