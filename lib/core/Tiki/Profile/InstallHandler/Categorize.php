<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Tiki_Profile_InstallHandler_Categorize extends Tiki_Profile_InstallHandler
{
	private $type;
	private $object;
	private $categories = array();

	function fetchData()
	{
		$data = $this->obj->getData();

		if (isset($data['type'])) {
			$this->type = $data['type'];
		}

		if (isset($data['object'])) {
			$this->object = $data['object'];
		}

		if (isset($data['categories'])) {
			$this->categories = (array) $data['categories'];
		}
	}

	function canInstall()
	{
		$this->fetchData();

		if (empty($this->type) || empty($this->object)) {
			return false;
		}

		return true;
	}

	function _install()
	{
		global $tikilib;
		$this->fetchData();
		$this->replaceReferences($this->type);
		$this->replaceReferences($this->object);
		$this->replaceReferences($this->categories);
		
		$categlib = TikiLib::lib('categ');

		$type = Tiki_Profile_Installer::convertType($this->type);
		$object = Tiki_Profile_Installer::convertObject($type, $this->object);

		foreach ($this->categories as $categId) {
			$categlib->categorize_any($type, $object, $categId);
		}

		return true;
	}
}
