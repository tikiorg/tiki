<?php

class Search_ContentSource_WikiSource implements Search_ContentSource_Interface
{
	private $db;
	private $tikilib;

	function __construct()
	{
		global $tikilib;

		$this->db = TikiDb::get();
		$this->tikilib = $tikilib;
	}

	function getDocuments()
	{
		return array_values($this->db->fetchMap('SELECT page_id, pageName FROM tiki_pages'));
	}

	function getDocument($objectId, Search_Type_Factory_Interface $typeFactory)
	{
		$info = $this->tikilib->get_page_info($objectId, true, true);

		$data = array(
			'title' => $typeFactory->sortable($info['pageName']),
			'language' => $typeFactory->identifier(empty($info['lang']) ? 'unknown' : $info['lang']),
			'modification_date' => $typeFactory->timestamp($info['lastModif']),
			'description' => $typeFactory->plaintext($info['description']),

			'wiki_content' => $typeFactory->wikitext($info['data']),

			'view_permission' => $typeFactory->identifier('tiki_p_view'),
		);

		return $data;
	}

	function getProvidedFields()
	{
		return array(
			'title',
			'language',
			'modification_date',
			'description',
			'wiki_content',
			'view_permission',
		);
	}
}

