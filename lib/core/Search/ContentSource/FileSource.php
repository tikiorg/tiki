<?php

class Search_ContentSource_FileSource implements Search_ContentSource_Interface
{
	private $db;

	function __construct()
	{
		$this->db = TikiDb::get();
	}

	function getDocuments()
	{
		return array_values($this->db->fetchMap('SELECT fileId x, fileId FROM tiki_files'));
	}

	function getDocument($objectId, Search_Type_Factory_Interface $typeFactory)
	{
		global $filegallib; require_once 'lib/filegals/filegallib.php';
		
		$file = $filegallib->get_file_info($objectId, true, false);

		$data = array(
			'title' => $typeFactory->sortable($file['name']),
			'language' => $typeFactory->identifier('unknown'),
			'modification_date' => $typeFactory->timestamp($file['lastModif']),
			'contributors' => $typeFactory->multivalue(array_unique(array($file['author'], $file['user'], $file['lastModifUser']))),
			'description' => $typeFactory->plaintext($file['description']),

			'gallery_id' => $typeFactory->identifier($file['galleryId']),
			'file_comment' => $typeFactory->plaintext($file['comment']),
			'file_content' => $typeFactory->plaintext($file['search_data']),

			'parent_object_type' => $typeFactory->identifier('file gallery'),
			'parent_object_id' => $typeFactory->identifier($file['galleryId']),
			'parent_view_permission' => $typeFactory->identifier('tiki_p_download_files'),
		);

		return $data;
	}

	function getProvidedFields()
	{
		return array(
			'title',
			'language',
			'modification_date',
			'contributors',
			'description',

			'gallery_id',
			'file_comment',
			'file_content',

			'parent_view_permission',
			'parent_object_id',
			'parent_object_type',
		);
	}
	
	function getGlobalFields()
	{
		return array(
			'title',
			'description',

			'file_comment',
			'file_content',
		);
	}
}

