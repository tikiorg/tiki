<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Search_ContentSource_ArticleSource implements Search_ContentSource_Interface, Tiki_Profile_Writer_ReferenceProvider
{
	private $db;

	function __construct()
	{
		$this->db = TikiDb::get();
	}

	function getReferenceMap()
	{
		return array(
			'topic_id' => 'article_topic',
			'article_type' => 'article_type',
		);
	}

	function getDocuments()
	{
		return $this->db->table('tiki_articles')->fetchColumn('articleId', array());
	}

	function getDocument($objectId, Search_Type_Factory_Interface $typeFactory)
	{
		$artlib = TikiLib::lib('art');
		
		$article = $artlib->get_article($objectId, false);

		if (! $article) {
			return false;
		}

		if ($topic = $artlib->get_topic($article['topicId'])) {
			$topic_name = $topic['name'];
		} else {
			$topic_name = '';
		}

		$rss_relations = TikiLib::lib('relation')->get_object_ids_with_relations_from('article', $objectId, 'tiki.rss.source');
		$sitetitle = '';
 		$siteurl = '';
 		if ($rss_relations) {
 			$rssId = reset($rss_relations);	
 			$rssModule = TikiLib::lib('rss')->get_rss_module($rssId);
			if ($rssModule['sitetitle']) { 
 				$sitetitle = $rssModule['sitetitle'];
 			}
 			if ($rssModule['siteurl']) {
 				$siteurl = $rssModule['siteurl'];
 			}
 		}

		$data = array(
			'title' => $typeFactory->sortable($article['title']),
			'language' => $typeFactory->identifier($article['lang'] ? $article['lang'] : 'unknown'),
			'creation_date' => $typeFactory->timestamp($article['created']),
			'modification_date' => $typeFactory->timestamp($article['publishDate']),
			'contributors' => $typeFactory->multivalue(array($article['author'])),
			'description' => $typeFactory->plaintext($article['heading']),

			'sitetitle' => $typeFactory->plaintext($sitetitle),
 			'siteurl' => $typeFactory->plaintext($siteurl),

			'topic_id' => $typeFactory->identifier($article['topicId']),
			'topic_name' => $typeFactory->plaintext($topic_name),

			'article_type' => $typeFactory->identifier($article['type']),
			'article_content' => $typeFactory->wikitext($article['body']),
			'article_topline' => $typeFactory->wikitext($article['topline']),
			'article_subtitle' => $typeFactory->wikitext($article['subtitle']),
			'article_author' => $typeFactory->plaintext($article['authorName']),
			'article_linkto' => $typeFactory->plaintext($article['linkto']),

			'view_permission' => ($article['ispublished'] == 'y') ? $typeFactory->identifier('tiki_p_read_article') : $typeFactory->identifier('tiki_p_edit_article'),
			'parent_object_type' => $typeFactory->identifier('topic'),
			'parent_object_id' => $typeFactory->identifier($article['topicId']),
			'parent_view_permission' => $typeFactory->identifier('tiki_p_read_topic'),
			'published' => ($article['ispublished'] == 'y') ? $typeFactory->identifier('y') : $typeFactory->identifier('n'),
		);

		return $data;
	}

	function getProvidedFields()
	{
		return array(
			'title',
			'language',
			'creation_date',
			'modification_date',
			'contributors',
			'description',

			'sitetitle',
 			'siteurl',

			'topic_id',
			'topic_name',

			'article_content',
			'article_type',
			'article_topline',
			'article_subtitle',
			'article_author',
			'article_linkto',

			'view_permission',
			'parent_view_permission',
			'parent_object_id',
			'parent_object_type',
			'published',
		);
	}

	function getGlobalFields()
	{
		return array(
			'title' => true,
			'description' => true,

			'article_content' => false,
			'article_topline' => false,
			'article_subtitle' => false,
			'article_author' => false,
		);
	}
}

