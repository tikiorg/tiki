<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Search_GlobalSource_CommentSource implements Search_GlobalSource_Interface
{

	function __construct()
	{
		$this->commentslib = TikiLib::lib('comments');
	}

	function getProvidedFields()
	{
		return array(
			'comment_count',
		);
	}

	function getGlobalFields()
	{
		return array();
	}

	function getData($objectType, $objectId, Search_Type_Factory_Interface $typeFactory, array $data = array())
	{
		if ($objectType == 'forum post') { 
			$forumId = $this->commentslib->get_comment_forum_id($objectId);
			$comment_count = $this->commentslib->count_comments_threads("forum:$forumId", $objectId);
		} else {
			$comment_count = $this->commentslib->count_comments("$objectType:$objectId");
		}
		return array(
			'comment_count' => $typeFactory->sortable($comment_count),
		);
	}
}

