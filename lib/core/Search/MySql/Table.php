<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Search_MySql_Table extends TikiDb_Table
{
	private $definition = false;

	function drop()
	{
		$table = $this->escapeIdentifier($this->tableName);
		$this->db->query("DROP TABLE IF EXISTS $table");
		$this->definition = false;
	}

	function exists()
	{
		$tables = $this->db->listTables();
		return in_array($this->tableName, $tables);
	}

	function ensureHasField($fieldName, $type, array $extra)
	{
		$this->loadDefinition();

		if (! isset($this->definition[$fieldName])) {
			$this->addField($fieldName, $type);
			
			if (in_array('index', $extra)) {
				$this->addIndex($fieldName);
			}

			if (in_array('fulltext', $extra)) {
				$this->addFullText($fieldName);
			}

			$this->definition[$fieldName] = $type;
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
	}

	private function addField($fieldName, $type)
	{
		$table = $this->escapeIdentifier($this->tableName);
		$fieldName = $this->escapeIdentifier($fieldName);
		$this->db->query("ALTER TABLE $table ADD COLUMN $fieldName $type");
	}

	private function addIndex($fieldName)
	{
		$table = $this->escapeIdentifier($this->tableName);
		$fieldName = $this->escapeIdentifier($fieldName);
		$this->db->query("ALTER TABLE $table ADD INDEX ($fieldName)");
	}

	private function addFullText($fieldName)
	{
		$table = $this->escapeIdentifier($this->tableName);
		$fieldName = $this->escapeIdentifier($fieldName);
		$this->db->query("ALTER TABLE $table ADD FULLTEXT INDEX ($fieldName)");
	}
}

