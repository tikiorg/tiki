<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Search_ContentSource_CommentSource implements Search_ContentSource_Interface
{
	private $types;
	private $db;
	private $permissionMap;

	function __construct($types)
	{
		$this->types = $types;

		$this->db = TikiDb::get();

		$this->permissionMap = TikiLib::lib('object')->map_object_type_to_permission();
	}

	function getDocuments()
	{
		$comments = $this->db->table('tiki_comments');

		return $comments->fetchColumn(
			'threadId',
			array(
				'objectType' => $comments->in($this->types),
			)
		);
	}

	function getDocument($objectId, Search_Type_Factory_Interface $typeFactory)
	{
		$commentslib = TikiLib::lib('comments');
		$comment = $commentslib->get_comment($objectId);

		$url = $commentslib->getHref($comment['objectType'], $comment['object'], $objectId);
		$url = str_replace('&amp;', '&', $url);

		$data = array(
			'title' => $typeFactory->sortable($comment['title']),
			'language' => $typeFactory->identifier('unknown'),
			'creation_date' => $typeFactory->timestamp($comment['commentDate']),
			'modification_date' => $typeFactory->timestamp($comment['commentDate']),
			'contributors' => $typeFactory->multivalue(array($comment['userName'])),

			'comment_content' => $typeFactory->wikitext($comment['data']),
			'parent_thread_id' => $typeFactory->identifier($comment['parentId']),

			'parent_object_type' => $typeFactory->identifier($comment['objectType']),
			'parent_object_id' => $typeFactory->identifier($comment['object']),
			'parent_view_permission' => $typeFactory->identifier($this->getParentPermissionForType($comment['objectType'])),

			'global_view_permission' => $typeFactory->identifier('tiki_p_read_comments'),

			'url' => $typeFactory->identifier($url),
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
			'url',

			'comment_content',
			'parent_thread_id',

			'parent_view_permission',
			'parent_object_id',
			'parent_object_type',

			'global_view_permission',
		);
	}

	function getGlobalFields()
	{
		return array(
			'title' => true,

			'comment_content' => false,
		);
	}

	private function getParentPermissionForType($type)
	{
		if (isset($this->permissionMap[$type])) {
			return $this->permissionMap[$type];
		}
	}
}

