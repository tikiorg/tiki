<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Yaml_Directive_Include
{
	protected $props;

	public function process(&$value, $key, $props)
	{
		$this->props = $props;

		if (is_array($value)) {
			foreach ($value as $definition) {
				if ($this->conditionalInclude($definition, $value, $key)) {
					return;
				}
			}
		} else {
			$this->conditionalInclude($value, $value, $key);
		}
	}

	protected function conditionalInclude($definition, &$value, $key)
	{
		$parsed = $this->parse($definition);

		$yamlFile = $parsed[0];

		if (count($parsed) == 1) {
			$result = true;
		} else {
			if (count($parsed) == 2) {
				$leftValue = $parsed[1];
				$operation = 'eq';
				$rightValue = $parsed[2];
			} else {
				$leftValue = $parsed[1];
				$operation = $parsed[2];
				$rightValue = $parsed[3];
			}

			$result = $this->checkCondition($operation, $leftValue, $rightValue);
		}

		if ($result) {
			$yaml = Horde_Yaml::load(file_get_contents($this->props['path'] . '/' . $yamlFile));
			if (is_array($yaml) && (count($yaml) == 1) && array_key_exists($key, $yaml)) {
				$value = $yaml[$key];
			} else {
				$value = $yaml;
			}
		}
		return $result;
	}

	protected function parse($str)
	{
		$str = trim(substr($str, strlen('!include')));
		$parts = explode(" ", $str);
		return $parts;
	}

	/**
	 * @param $operation
	 * @param $leftValue
	 * @param $rightValue
	 * @return bool
	 */
	protected function checkCondition($operation, $leftValue, $rightValue)
	{
		switch ($operation) {
			case 'eq':
				return ($leftValue == $rightValue);
			case 'neq':
				return ($leftValue != $rightValue);
			case 'lt':
				return ($leftValue < $rightValue);
			case 'le':
				return ($leftValue <= $rightValue);
			case 'gt':
				return ($leftValue > $rightValue);
			case 'ge':
				return ($leftValue >= $rightValue);
			default:
				return false;
		}
	}
} 
