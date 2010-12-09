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
		global $wikilib; require_once 'lib/wiki/wikilib.php';

		$info = $this->tikilib->get_page_info($objectId, true, true);

		$contributors = $wikilib->get_contributors($objectId, $info['user'], false);
		if (! in_array($info['user'], $contributors)) {
			$contributors[] = $info['user'];
		}

		$data = array(
			'title' => $typeFactory->sortable($info['pageName']),
			'language' => $typeFactory->identifier(empty($info['lang']) ? 'unknown' : $info['lang']),
			'modification_date' => $typeFactory->timestamp($info['lastModif']),
			'description' => $typeFactory->plaintext($info['description']),
			'contributors' => $typeFactory->multivalue($contributors),

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
			'contributors',

			'wiki_content',

			'view_permission',
		);
	}
}

