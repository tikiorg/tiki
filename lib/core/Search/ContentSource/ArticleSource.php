<?php

class Search_ContentSource_ArticleSource implements Search_ContentSource_Interface
{
	private $db;

	function __construct()
	{
		$this->db = TikiDb::get();
	}

	function getDocuments()
	{
		return array_values($this->db->fetchMap('SELECT articleId x, articleId FROM tiki_articles'));
	}

	function getDocument($objectId, Search_Type_Factory_Interface $typeFactory)
	{
		global $artlib; require_once 'lib/articles/artlib.php';
		
		$article = $artlib->get_article($objectId, false);

		$data = array(
			'title' => $typeFactory->sortable($article['title']),
			'language' => $typeFactory->identifier($article['lang'] ? $article['lang'] : 'unknown'),
			'modification_date' => $typeFactory->timestamp($article['publishDate']),
			'contributors' => $typeFactory->multivalue(array($article['author'])),
			'description' => $typeFactory->wikitext($article['heading']),

			'topic_id' => $typeFactory->identifier($article['topicId']),
			'article_content' => $typeFactory->wikitext($article['body']),
			'article_topline' => $typeFactory->wikitext($article['topline']),
			'article_subtitle' => $typeFactory->wikitext($article['subtitle']),

			'view_permission' => $typeFactory->identifier('tiki_p_read_article'),
			'parent_object_type' => $typeFactory->identifier('topic'),
			'parent_object_id' => $typeFactory->identifier($article['topicId']),
			'parent_view_permission' => $typeFactory->identifier('tiki_p_read_topic'),
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

			'topic_id',
			'article_content',
			'article_topline',
			'article_subtitle',

			'view_permission',
			'parent_view_permission',
			'parent_object_id',
			'parent_object_type',
		);
	}
}

