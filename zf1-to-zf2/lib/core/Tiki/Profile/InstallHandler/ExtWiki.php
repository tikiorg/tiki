<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Tiki_Profile_InstallHandler_ExtWiki extends Tiki_Profile_InstallHandler
{
	function getData()
	{
		$data = $this->obj->getData();

		return $data;
	}

	function canInstall()
	{
		$data = $this->getData();
		if ( ! isset( $data['name'], $data['url'] ) )
			return false;

		return true;
	}

	function _install()
	{
		$adminlib = TikiLib::lib('admin');

		$data = $this->getData();

		$this->replaceReferences($data);

		$adminlib->replace_extwiki(null, $data['url'], $data['name']);

		return $data['name'];
	}
}
