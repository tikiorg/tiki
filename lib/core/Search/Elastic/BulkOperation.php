<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Search_Elastic_BulkOperation
{
	private $count = 0;
	private $limit;
	private $callback;
	private $buffer = '';

	function __construct($limit, $callback)
	{
		$this->limit = max(10, (int) $limit);
		$this->callback = $callback;
	}

	function flush()
	{
		if ($this->count > 0) {
			$callback = $this->callback;
			$callback($this->buffer);

			$this->buffer = '';
			$this->count = 0;
		}
	}

	function index($index, $type, $id, array $data)
	{
		$this->append(
			array(
				array('index' => array(
						'_index' => $index,
						'_type' => $type,
						'_id' => $id,
					)
				),
				$data,
			)
		);
	}

	function unindex($index, $type, $id)
	{
		$this->append(
			array(
				array('delete' => array(
						'_index' => $index,
						'_type' => $type,
						'_id' => $id,
					)
				),
			)
		);
	}

	private function append($lines)
	{
		$this->count += 1;
		foreach ($lines as $line) {
			$this->buffer .= json_encode($line) . "\n";
		}

		if ($this->count >= $this->limit) {
			$this->flush();
		}
	}
}

