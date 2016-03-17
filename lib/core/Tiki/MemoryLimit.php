<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Tiki_MemoryLimit
{
	private $initialLimit;

	function __construct($targetLimit)
	{
		$this->initialLimit = ini_get('memory_limit');
		$this->applyMinimalLimit($targetLimit);

	}

	function __destruct()
	{
		$this->applySafeLimit($this->initialLimit);
	}

	private function applyMinimalLimit($target)
	{
		$rawCurrent = $this->getRaw($this->initialLimit);
		$rawTarget = $this->getRaw($target);

		if ($rawCurrent < $rawTarget) {
			ini_set('memory_limit', $target);
		};
	}

	private function applySafeLimit($targetMemory)
	{
		$usage = memory_get_usage();
		$target = $this->getRaw($targetMemory);

		if ($usage < $target) {
			ini_set('memory_limit', $targetMemory);
		}
	}

	private function getRaw($memory_limit)
	{
		$s = trim($memory_limit);
		$last = strtolower($s{strlen($s)-1});
		switch ( $last ) {
			case 'g': $s *= 1024;
			case 'm': $s *= 1024;
			case 'k': $s *= 1024;
		}

		return $s;
	}
}

