<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Search_ContentSource_ForumPostSource implements Search_ContentSource_Interface
{
	private $db;

	function __construct()
	{
		$this->db = TikiDb::get();
	}

	function getDocuments()
	{
		return $this->db->table('tiki_comments')->fetchColumn('threadId', array(
			'objectType' => 'forum',
			'parentId' => 0,
		));
	}

	function getDocument($objectId, Search_Type_Factory_Interface $typeFactory)
	{
		$commentslib = TikiLib::lib('comments');
		$comment = $commentslib->get_comment($objectId);

		$lastModification = $comment['commentDate'];
		$content = $comment['data'];
		$author = array($comment['userName']);

		$thread = $commentslib->get_comments($comment['objectType'] . ':' . $comment['object'], $objectId, 0, 0);
		foreach ($thread['data'] as $reply) {
			$content .= "\n{$reply['data']}";
			$lastModification = max($lastModification, $reply['commentDate']);
			$author[] = $comment['userName'];
		}

		$data = array(
			'title' => $typeFactory->sortable($comment['title']),
			'language' => $typeFactory->identifier('unknown'),
			'modification_date' => $typeFactory->timestamp($lastModification),
			'contributors' => $typeFactory->multivalue(array_unique($author)),

			'post_content' => $typeFactory->wikitext($content),

			'parent_object_type' => $typeFactory->identifier($comment['objectType']),
			'parent_object_id' => $typeFactory->identifier($comment['object']),
			'parent_view_permission' => $typeFactory->identifier('tiki_p_forum_read'),
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

			'parent_view_permission',
			'parent_object_id',
			'parent_object_type',
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

