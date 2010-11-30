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
		return array_values($this->db->fetchMap('SELECT postId x, postId FROM tiki_blog_posts'));
	}

	function getDocument($objectId, Search_Type_Factory_Interface $typeFactory)
	{
		global $bloglib; require_once 'lib/blogs/bloglib.php';
		
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
}

