<?php

class Search_GlobalSource_Static implements Search_GlobalSource_Interface
{
	private $data;
	private $typeMap;

	function __construct($data, $typeMap)
	{
		$this->data = $data;
		$this->typeMap = $typeMap;
	}

	function getData($objectType, $objectId, Search_Type_Factory_Interface $typeFactory, array $data = array())
	{
		$out = array();

		foreach ($this->data["$objectType:$objectId"] as $key => $value) {
			$type = $this->typeMap[$key];
			$out[$key] = $typeFactory->$type($value);
		}

		return $out;
	}

	function getProvidedFields()
	{
		return array_keys($this->typeMap);
	}

	function getGlobalFields()
	{
		return array_keys($this->typeMap);
	}
}

