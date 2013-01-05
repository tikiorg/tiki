<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Tiki_Profile_InstallHandler_BlogPost extends Tiki_Profile_InstallHandler
{
	function getData()
	{
		if ( $this->data )
			return $this->data;

		$defaults = array(
			'title' => 'Title',
			'private' => 'n',
			'user' => '',
		);

		$data = array_merge($defaults, $this->obj->getData());

		$data = Tiki_Profile::convertYesNo($data);

		return $this->data = $data;
	}

	function canInstall()
	{
		$data = $this->getData();
		if ( ! isset( $data['blog'] ) )
			return false;
		if ( ! isset( $data['content'] ) )
			return false;

		return true;
	}

	function _install()
	{
		global $bloglib;
		if ( ! $bloglib ) require_once 'lib/blogs/bloglib.php';

		$data = $this->getData();

		$this->replaceReferences($data);

		if ( isset( $data['blog'] ) && empty( $data['user'] ) ) {
			global $bloglib, $tikilib;
			if ( ! $bloglib ) require_once 'lib/blogs/bloglib.php';

			$result = $tikilib->query("SELECT `user` FROM `tiki_blogs` WHERE `blogId` = ?", array( $data['blog'] ));

			if ( $row = $result->fetchRow() ) {
				$data['user'] = $row['user'];
			}
		}

		$entryId = $bloglib->blog_post($data['blog'], $data['content'], $data['excerpt'], $data['user'], $data['title'], '', $data['private']);

		return $entryId;
	}
}
