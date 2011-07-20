<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class TikiFilter_PrepareInput
{
	private $delimiter;

	function __construct($delimiter)
	{
		$this->delimiter = $delimiter;
	}

	function prepare(array $input)
	{
		$output = array();

		foreach ($input as $key => $value) {
			if (strpos($key, $this->delimiter) === false ) {
				$output[$key] = $value;
			} else {
				list ($base, $remain) = explode($this->delimiter, $key, 2);

				if (! isset($output[$base]) || ! is_array($output[$base])) {
					$output[$base] = array();
				}

				$output[$base][$remain] = $value;
			}
		}

		foreach ($output as $key => & $value) {
			if (is_array($value)) {
				$value = $this->prepare($value);
			}
		}

		return $output;
	}
}

