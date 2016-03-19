<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Search_GlobalSource_RelationSource implements Search_GlobalSource_Interface
{
	private $relationlib;

	function __construct()
	{
		$this->relationlib = TikiLib::lib('relation');
	}

	function getProvidedFields()
	{
		return array(
			'relations',
			'relation_types',
		);
	}

	function getGlobalFields()
	{
		return array();
	}

	function getData($objectType, $objectId, Search_Type_Factory_Interface $typeFactory, array $data = array())
	{

		if (isset($data['relations']) || isset($data['relation_types'])) {
			return array();
		}

		$relations = array();

		$types = array();

		$from = $this->relationlib->get_relations_from($objectType, $objectId);
		foreach ($from as $rel) {
			$relations[] = Search_Query_Relation::token($rel['relation'], $rel['type'], $rel['itemId']);
			$types[] = $rel['relation'];
		}

		$to = $this->relationlib->get_relations_to($objectType, $objectId);
		foreach ($to as $rel) {
			$relations[] = Search_Query_Relation::token($rel['relation'] . '.invert', $rel['type'], $rel['itemId']);
			$types[] = $rel['relation'] . '.invert';
		}

		//take the type array and get a count of each indiv. type
		$type_count = array_count_values($types);
		$rel_count = array();
		foreach ($type_count as $key=>$val) {
			//instead of returning an assoc. array, format to "relation:count" format for input in index
			$rel_count[] = $key . ":" . $val;
		}

		return array(
			'relations' => $typeFactory->multivalue($relations),
			'relation_types' => $typeFactory->multivalue(array_unique($types)),
			'relation_count' => $typeFactory->multivalue($rel_count),
		);
	}
}

