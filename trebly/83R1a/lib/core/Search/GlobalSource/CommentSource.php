<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: CommentSource.php 36377 2011-08-22 13:56:56Z lphuberdeau $

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
		return array(
			'comment_count' => $typeFactory->sortable($this->commentslib->count_comments("$objectType:$objectId")),
		);
	}
}

