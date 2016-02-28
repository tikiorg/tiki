<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Tiki_Profile_InstallHandler_MenuOption extends Tiki_Profile_InstallHandler
{
	function getData()
	{
		if ( $this->data )
			return $this->data;

		$defaults = array(
			'type' => 'o',
			'optionId' => 0,
			'position' => 1,
			'section' => '',
			'perm' => '',
			'groups' => array(),
			'level' => 0,
			'icon' => '',
			'menuId' => 0
		);


		$data = $this->obj->getData();

		$data = array_merge($defaults, $data);

		$this->replaceReferences($data);

		if (!empty($data['menuId']) && !empty($data['url'])) {
		   $menulib = TikiLib::lib('menu');
		   $data['optionId'] = $menulib->get_option($data['menuId'], $data['url']);
		}
		return $this->data = $data;
	}

	function canInstall()
	{
		$data = $this->getData();

		if ( ! isset( $data['url'] ) || ! isset( $data['menuId'] ) )
			return false;
		return true;
	}
	function _install()
	{
		$menulib = TikiLib::lib('menu');

		$data = $this->getData();

		return $menulib->replace_menu_option($data['menuId'], $data['optionId'], $data['name'], $data['url'], $data['type'], $data['position'], $data['section'], $data['perm'], implode(',', $data['groups']), $data['level'], $data['icon']);
	}
}
