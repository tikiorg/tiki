<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Tiki_Profile_InstallHandler_Topic extends Tiki_Profile_InstallHandler
{
	function getData()
	{
		if ( $this->data )
			return $this->data;

		$data = $this->obj->getData();
		$data = Tiki_Profile::convertYesNo($data);

		return $this->data = $data;
	}

	function canInstall()
	{
		$data = $this->getData();

		if ( ! isset( $data['name'] ) )
			return false;

		return true;
	}

	function _install()
	{
		global $artlib;
		$data = $this->getData();

		$this->replaceReferences($data);

		require_once 'lib/articles/artlib.php';

		$id = $artlib->add_topic($data['name'], null, null, null, null);

		return $id;
	}
}
