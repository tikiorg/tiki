<?php

class Search_ContentSource_Static implements Search_ContentSource_Interface
{
	private $data;
	private $typeMap;

	function __construct(array $data, $typeMap)
	{
		$this->data = $data;
		$this->typeMap = $typeMap;
	}

	function getDocuments()
	{
		return array_keys($this->data);
	}

	function getDocument($objectId, Search_Type_Factory_Interface $typeFactory)
	{
		if (! isset($this->data[$objectId])) {
			return false;
		}

		$out = array();

		foreach( $this->data[$objectId] as $key => $value) {
			$type = $this->typeMap[$key];
			$out[$key] = $typeFactory->$type($value);
		}

		return $out;
	}

	function getProvidedFields()
	{
		return array_keys($this->typeMap);
	}
}

