<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Search_ContentSource_ForumPostSource implements Search_ContentSource_Interface, Tiki_Profile_Writer_ReferenceProvider
{
	private $db;

	function __construct()
	{
		$this->db = TikiDb::get();
	}

	function getReferenceMap()
	{
		return array(
			'forum_id' => 'forum',
		);
	}

	function getDocuments()
	{
		global $prefs;
		if ($prefs['unified_forum_deepindexing'] == 'y') {
			$filters = array('objectType' => 'forum', 'parentId' => 0);
		} else {
			$filters = array('objectType' => 'forum');
		}
		return $this->db->table('tiki_comments')->fetchColumn('threadId', $filters);
	}

	function getDocument($objectId, Search_Type_Factory_Interface $typeFactory)
	{
		global $prefs;

		$commentslib = TikiLib::lib('comments');
		$commentslib->extras_enabled(false);
		$comment = $commentslib->get_comment($objectId);

		$root_thread_id = $commentslib->find_root($comment['parentId']);
		if ($comment['parentId']) {
			$root = $commentslib->get_comment($root_thread_id);
			if (!$comment['title']) {
				$comment['title'] = $root['title'];
			}
			$root_author = array($root['userName']);
		} else {
			$root_author = array();
		}

		$lastModification = $comment['commentDate'];
		$content = $comment['data'];
		$snippet = TikiLib::lib('tiki')->get_snippet($content);
		$author = array($comment['userName']);

		$thread = $commentslib->get_comments($comment['objectType'] . ':' . $comment['object'], $objectId, 0, 0);
		$forum_info = $commentslib->get_forum($comment['object']);
		$forum_language = $forum_info['forumLanguage'] ? $forum_info['forumLanguage'] : 'unknown';

		if ($prefs['unified_forum_deepindexing'] == 'y') {
			foreach ($thread['data'] as $reply) {
				$content .= "\n{$reply['data']}";
				$lastModification = max($lastModification, $reply['commentDate']);
				$author[] = $comment['userName'];
			}
		}

		$commentslib->extras_enabled(true);

		$data = array(
			'title' => $typeFactory->sortable($comment['title']),
			'language' => $typeFactory->identifier($forum_language),
			'creation_date' => $typeFactory->timestamp($comment['commentDate']),
			'modification_date' => $typeFactory->timestamp($lastModification),
			'contributors' => $typeFactory->multivalue(array_unique($author)),

			'forum_id' => $typeFactory->identifier($comment['object']),
			'forum_section' => $typeFactory->identifier($forum_info['section']),

			'post_content' => $typeFactory->wikitext($content),
			'post_snippet' => $typeFactory->plaintext($snippet),
			'parent_thread_id' => $typeFactory->identifier($comment['parentId']),

			'parent_object_type' => $typeFactory->identifier($comment['objectType']),
			'parent_object_id' => $typeFactory->identifier($comment['object']),
			'parent_view_permission' => $typeFactory->identifier('tiki_p_forum_read'),
			'parent_contributors' => $typeFactory->multivalue(array_unique($root_author)),
			'hits' => $typeFactory->numeric($comment['hits']),
			'root_thread_id' => $typeFactory->identifier($root_thread_id),
		);

		$forum_lastPost = $this->getForumLastPostData($objectId, $typeFactory);

		$data = array_merge($data, $forum_lastPost);

		return $data;
	}

	/**
	 * Return data array of last post for thread
	 *
	 * @param $threadId
	 * @param Search_Type_Factory_Interface $typeFactory
	 * @return array
	 * @throws Exception
	 */
	function getForumLastPostData($threadId, Search_Type_Factory_Interface $typeFactory)
	{
		$commentslib = TikiLib::lib('comments');
		$commentslib->extras_enabled(false);

		$comment = $commentslib->get_lastPost($threadId);

		$lastModification = isset($comment['commentDate']) ? $comment['commentDate'] : 0;
		$content = isset($comment['data']) ? $comment['data'] : '';
		$snippet = TikiLib::lib('tiki')->get_snippet($content);
		$author = array(isset($comment['userName']) ? $comment['userName'] : '');

		$commentslib->extras_enabled(true);

		$data = array(
			'lastpost_title' => $typeFactory->sortable(isset($comment['title']) ? $comment['title'] : ''),
			'lastpost_modification_date' => $typeFactory->timestamp($lastModification),
			'lastpost_contributors' => $typeFactory->multivalue(array_unique($author)),
			'lastpost_post_content' => $typeFactory->wikitext($content),
			'lastpost_post_snippet' => $typeFactory->plaintext($snippet),
			'lastpost_hits' => $typeFactory->numeric(isset($comment['hits']) ? $comment['hits'] : 0),
			'lastpost_thread_id' => $typeFactory->identifier(isset($comment['thread_id']) ? $comment['thread_id'] : 0),
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

			'post_content',
			'post_snippet',
			'forum_id',
			'forum_section',
			'parent_thread_id',

			'parent_view_permission',
			'parent_object_id',
			'parent_object_type',

			'root_thread_id',
			'parent_contributors',
			'hits',

			'lastpost_title',
			'lastpost_modification_date',
			'lastpost_contributors',
			'lastpost_post_content',
			'lastpost_post_snippet',
			'lastpost_hits',
			'lastpost_thread_id',
		);
	}

	function getGlobalFields()
	{
		return array(
			'title' => true,

			'post_content' => false,
		);
	}
}

