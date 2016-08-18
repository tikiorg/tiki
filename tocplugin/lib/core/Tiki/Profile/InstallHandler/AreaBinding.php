<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Tiki_Profile_InstallHandler_AreaBinding extends Tiki_Profile_InstallHandler
{
	function getData()
	{
		if ( $this->data ) {
			return $this->data;
		}

		$defaults = array();
		$data = array_merge($defaults, $this->obj->getData());

		$data = Tiki_Profile::convertYesNo($data);

		return $this->data = $data;
	}

	function canInstall()
	{
		$data = $this->getData();
		if ( ! isset($data['category'], $data['perspective']) ) {
			return false;
		}

		return true;
	}

	function _install()
	{
		$areaslib = TikiLib::lib('areas');

		$data = $this->getData();

		$this->replaceReferences($data);

		$areaslib->bind_area($data['category'], $data['perspective']);

		return "{$data['category']}-{$data['perspective']}";
	}
}
