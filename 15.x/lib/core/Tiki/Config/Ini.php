<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Tiki_Config_Ini extends Zend\Config\Reader\Ini
{

	const SECTION_SEPARATOR = ':';
	const SECTION_EXTENDS_KEY = ';extends';

	protected $filterSection = null;

	public function setFilterSection($filter)
	{
		$this->filterSection = $filter;
	}

	/**
	 * Process data from the parsed ini file.
	 *
	 * @param  array $data
	 * @return array
	 */
	protected function process(array $data)
	{
		$data = $this->preProcessSectionInheritance($data);
		$config = parent::process($data);
		$config = $this->posProcessSectionInheritance($config);

		if ( !is_null($this->filterSection) ){
			if (array_key_exists($this->filterSection, $config)){
				return $config[$this->filterSection];
			} else {
				return array();
			}
		}

		return $config;
	}

	protected function preProcessSectionInheritance(array $data)
	{
		$result = array();

		foreach($data as $key => $value){
			$tokens = explode(self::SECTION_SEPARATOR, $key);
			$section = trim($tokens[0]);
			if ( count($tokens) == 2 && is_array($value)){
				$value[self::SECTION_EXTENDS_KEY] = trim($tokens[1]);
			}
			$result[$section] = $value;
		}
		return $result;
	}

	protected function posProcessSectionInheritance(array $config)
	{
		$result = array();

		foreach($config as $key => $value){
			if (is_array($value) && array_key_exists(self::SECTION_EXTENDS_KEY, $value)){
				$value = $this->resolveSectionInheritance($config, $key);
			}
			$result[$key] = $value;
		}
		return $result;
	}

	protected function resolveSectionInheritance($config, $section)
	{
		$result = array();

		if (array_key_exists(self::SECTION_EXTENDS_KEY, $config[$section])){
			$parentSection = $config[$section][self::SECTION_EXTENDS_KEY];
			unset($config[$section][self::SECTION_EXTENDS_KEY]);
			$result = $this->resolveSectionInheritance($config, $parentSection);
		}
		$result = array_replace_recursive($result, $config[$section]);

		return $result;
	}
}