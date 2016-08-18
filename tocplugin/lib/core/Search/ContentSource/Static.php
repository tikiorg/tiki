<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

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

		if (is_int(key($this->data[$objectId]))) {
			foreach ($this->data[$objectId] as $entry) {
				$out[] = $this->mapData($entry, $typeFactory);
			}
		} else {
			$out = $this->mapData($this->data[$objectId], $typeFactory);
		}

		return $out;
	}

	private function mapData($data, $typeFactory)
	{
		$out = array();

		foreach ($data as $key => $value) {
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
		return array_fill_keys(array_keys($this->typeMap), true);
	}
}

