<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Search_MySql_QueryBuffer
{
	private $db;
	private $count;
	private $prefix;
	private $buffer = array();

	function __construct(TikiDb $db, $count, $prefix)
	{
		$this->db = $db;
		$this->count = max(10, (int) $count);
		$this->prefix = $prefix;
	}

	function push($block)
	{
		$this->buffer[] = $block;

		if (count($this->buffer) == $this->count) {
			$this->flush();
		}
	}

	function flush()
	{
		if (count($this->buffer) == 0) {
			return;
		}

		$this->realFlush();
	}

	function setPrefix($prefix)
	{
		if ($prefix !== $this->prefix) {
			$this->flush();
		}

		$this->prefix = $prefix;
	}

	private function realFlush()
	{
		$query = $this->prefix . implode(', ', $this->buffer);
		$this->db->queryError($query, $error);

		if ($error) {
			throw new Search_MySql_LimitReachedException(tr("Could not perform index modification: %0", $error));
		}

		$this->clear();
	}

	function clear()
	{
		$this->buffer = array();
	}
}

