<?php

class Search_GlobalSource_FreeTagSource implements Search_GlobalSource_Interface
{
	private $freetaglib;

	function __construct()
	{
		global $freetaglib; require_once 'lib/freetag/freetaglib.php';
		$this->freetaglib = $freetaglib;
	}

	function getProvidedFields()
	{
		return array('freetags', 'freetags_text');
	}

	function getData($objectType, $objectId, Search_Type_Factory_Interface $typeFactory, array $data = array())
	{
		$tags = $this->freetaglib->get_tags_on_object($objectId, $objectType);

		$textual = array();
		$ids = array();

		foreach ($tags['data'] as $entry) {
			$textual[] = $entry['tag'];
			$ids[] = $entry['tagId'];
		}

		return array(
			'freetags' => $typeFactory->multivalue($ids),
			'freetags_text' => $typeFactory->plaintext(implode(' ', $textual)),
		);
	}
}

