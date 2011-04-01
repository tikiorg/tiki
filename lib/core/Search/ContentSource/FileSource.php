<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Search_ContentSource_FileSource implements Search_ContentSource_Interface
{
	private $db;

	function __construct()
	{
		$this->db = TikiDb::get();
	}

	function getDocuments()
	{
		return $this->db->table('tiki_files')->fetchColumn('fileId', array());
	}

	function getDocument($objectId, Search_Type_Factory_Interface $typeFactory)
	{
		$filegallib = Tikilib::lib('filegal');
		
		$file = $filegallib->get_file_info($objectId, true, false);

		$data = array(
			'title' => $typeFactory->sortable(empty($file['name'])?$file['filename']:$file['name']),
			'language' => $typeFactory->identifier('unknown'),
			'modification_date' => $typeFactory->timestamp($file['lastModif']),
			'contributors' => $typeFactory->multivalue(array_unique(array($file['author'], $file['user'], $file['lastModifUser']))),
			'description' => $typeFactory->plaintext($file['description']),
			'filename' => $typeFactory->identifier($file['filename']),

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
			'filename',

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
			'title' => true,
			'description' => true,
			'filename' => true,

			'file_comment' => false,
			'file_content' => false,
		);
	}
}

