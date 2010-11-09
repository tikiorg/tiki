<?php

class Search_ContentSource_WikiSource implements Search_ContentSource_Interface
{
	function getDocuments()
	{
		$db = TikiDb::get();
		return array_values($db->fetchMap('SELECT page_id, pageName FROM tiki_pages'));
	}

	function getDocument($objectId, Search_Type_Factory_Interface $typeFactory)
	{
		global $tikilib;

		$info = $tikilib->get_page_info($objectId);

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
}

