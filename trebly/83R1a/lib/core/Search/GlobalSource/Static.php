<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: Static.php 33195 2011-03-02 17:43:40Z changi67 $

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
		return array_fill_keys(array_keys($this->typeMap), true);
	}
}

