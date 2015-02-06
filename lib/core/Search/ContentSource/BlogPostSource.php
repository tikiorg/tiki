<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Search_ContentSource_BlogPostSource implements Search_ContentSource_Interface, Tiki_Profile_Writer_ReferenceProvider
{
	private $db;

	function __construct()
	{
		$this->db = TikiDb::get();
	}

	function getReferenceMap()
	{
		return array(
			'blog_id' => 'blog',
		);
	}

	function getDocuments()
	{
		return $this->db->table('tiki_blog_posts')->fetchColumn('postId', array());
	}

	function getDocument($objectId, Search_Type_Factory_Interface $typeFactory)
	{
		$bloglib = TikiLib::lib('blog');
		
		$post = $bloglib->get_post($objectId);

		$data = array(
			'title' => $typeFactory->sortable($post['title']),
			'language' => $typeFactory->identifier('unknown'),
			'modification_date' => $typeFactory->timestamp($post['created']),
			'contributors' => $typeFactory->multivalue(array($post['user'])),

			'blog_id' => $typeFactory->identifier($post['blogId']),
			'blog_excerpt' => $typeFactory->wikitext($post['excerpt']),
			'blog_content' => $typeFactory->wikitext($post['data']),

			'parent_object_type' => $typeFactory->identifier('blog'),
			'parent_object_id' => $typeFactory->identifier($post['blogId']),
			'view_permission' => $typeFactory->identifier('tiki_p_read_blog'),
			'parent_view_permission' => $typeFactory->identifier('tiki_p_read_blog'),
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

			'blog_id',
			'blog_excerpt',
			'blog_content',

			'view_permission',
			'parent_view_permission',
			'parent_object_id',
			'parent_object_type',
		);
	}

	function getGlobalFields()
	{
		return array(
			'title' => true,

			'blog_excerpt' => false,
			'blog_content' => false,
		);
	}
}

