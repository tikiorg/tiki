<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
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
		);
	}

	function getGlobalFields()
	{
		return array();
	}

	function getData($objectType, $objectId, Search_Type_Factory_Interface $typeFactory, array $data = array())
	{
		$relations = array();

		$from = $this->relationlib->get_relations_from($objectType, $objectId);
		foreach ($from as $rel) {
			$relations[] = Search_Query_Relation::token($rel['relation'], $rel['type'], $rel['itemId']);
		}

		$to = $this->relationlib->get_relations_to($objectType, $objectId);
		foreach ($to as $rel) {
			$relations[] = Search_Query_Relation::token($rel['relation'] . '.invert', $rel['type'], $rel['itemId']);
		}

		return array(
			'relations' => $typeFactory->multivalue($relations),
		);
	}
}

