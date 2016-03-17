<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
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

	static function delimiter($delimiter)
	{
		$me = new self($delimiter);
		return $me;
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

	function flatten($values, &$newValues = array(), $prefix = '')
	{
		foreach ($values as $key => $value) {
			if (is_array($value) || is_object($value)) {
				$newPrefix = $prefix.$key.$this->delimiter;
				$newValue = $this->flatten($value, $newValues, $newPrefix, $this->delimiter);
				$newValues =& $newValue;
			} else {
				$newValues[$prefix.$key] = $value;
			}
		}

		return $newValues;
	}

	function toString($values, &$newValues = array(), $prefex = '')
	{
		$flatArray = self::flatten($values, $newValues, $prefex);

		$output = '';

		foreach ($flatArray as $key => $value) {
			$output .= urlencode($key) . ':' . urlencode($value) . "\n";
		}

		return $output;
	}

	function prepareFromString($input = '')
	{
		$stringArray = explode("\n", $input);

		$flatArray = array();

		foreach ($stringArray as $string) {
			$string = explode(":", $string);
			if (isset($string[0], $string[1])) {
				$flatArray[urldecode($string[0])] = urldecode($string[1]);
			}
		}

		return self::prepare($flatArray);
	}
}

