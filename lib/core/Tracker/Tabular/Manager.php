<?php
// (c) Copyright 2002-2014 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

namespace Tracker\Tabular;

class Manager
{
	private $table;

	function __construct(\TikiDb $db)
	{
		$this->table = $db->table('tiki_tabular_formats');
	}

	function getList($conditions = [])
	{
		return $this->table->fetchAll(['tabularId', 'name', 'trackerId'], $conditions, -1, -1, 'name_asc');
	}

	function getInfo($tabularId)
	{
		$info = $this->table->fetchFullRow(['tabularId' => $tabularId]);

		$info['format_descriptor'] = json_decode($info['format_descriptor'], true);
		$info['filter_descriptor'] = [];
		return $info;
	}

	function create($name, $trackerId)
	{
		return $this->table->insert([
			'name' => $name,
			'trackerId' => $trackerId,
			'format_descriptor' => '[]',
		]);
	}

	function update($tabularId, $name, array $fields)
	{
		return $this->table->update([
			'name' => $name,
			'format_descriptor' => json_encode($fields),
		], ['tabularId' => $tabularId]);
	}

	function remove($tabularId)
	{
		return $this->table->delete(['tabularId' => $tabularId]);
	}
}

