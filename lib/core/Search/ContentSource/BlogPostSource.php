<?php

class Search_ContentSource_BlogPostSource implements Search_ContentSource_Interface
{
	private $db;

	function __construct()
	{
		$this->db = TikiDb::get();
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

