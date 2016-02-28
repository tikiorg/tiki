<?php
// (c) Copyright 2002-2014 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Yaml_Directives
{
	protected $path;
	protected $filter;

	public function __construct(Yaml_Filter_FilterInterface $filter = null, $path = null)
	{
		if (is_null($path)) {
			$this->path = TIKI_PATH;
		} else {
			$this->path = $path;
		}

		$this->filter = $filter;
	}

	public function setPath($path)
	{
		$this->path = $path;
	}

	public function getPath()
	{
		return $this->path;
	}

	protected function applyDirective($directive, &$value, $key)
	{
		$class = "Yaml_Directive_" . ucfirst($directive);
		if (!class_exists($class, true)) {
			return;
		}
		$directive = new $class();
		$directive->process($value, $key, array('path' => $this->path));
	}

	protected function directiveFromValue($value)
	{
		if (is_array($value)) {
			$value = array_values($value)[0];
		}
		$directive = substr($value, 1, strpos($value, " ") - 1);
		return $directive;
	}

	protected function valueIsDirective($value)
	{
		$testValue = $value;
		if (is_array($value) &&  !empty($value)) {
			$testValue = array_values($value)[0];
		}

		if (is_string($testValue) && (strncmp('!', $testValue, 1) == 0)) {
			// Wiki syntax can often start with ! for titles and so the following checks are needed to reduce conflict possibility with YAML user-defined data type extensions syntax
			if (!ctype_lower(substr($testValue, 1, 1))) {
				return false;
			}
	
			$class = "Yaml_Directive_" . ucfirst($this->directiveFromValue($testValue)); 
			if (!class_exists($class)) {
				return false;
			}

			return true;
		}

		return false;
	}

	protected function map(&$value, $key)
	{
		if ($this->valueIsDirective($value)) {
			if ($this->filter instanceof Yaml_Filter_FilterInterface) {
				$this->filter->filter($value);
			}
			$this->applyDirective($this->directiveFromValue($value), $value, $key);
		} else {
			if (is_array($value)) {
				array_walk($value, array($this, "map"));
			}
		}
	}

	public function process(&$yaml)
	{
		array_walk($yaml, array($this, "map"));
	}
}
