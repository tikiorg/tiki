<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Tiki_Profile_InstallHandler_PluginAlias extends Tiki_Profile_InstallHandler
{
	function getData()
	{
		if ( $this->data )
			return $this->data;

		$defaults = array(
			'body' => array(
				'input' => 'ignore',
				'default' => '',
				'params' => array()
			),
			'params' => array(
			),
		);

		$data = array_merge($defaults, $this->obj->getData());

		return $this->data = $data;
	}

	function canInstall()
	{
		$data = $this->getData();

		if ( ! isset( $data['name'], $data['implementation'], $data['description'] ) )
			return false;

		if ( ! is_array($data['description']) || ! is_array($data['body']) || ! is_array($data['params']) )
			return false;

		return true;
	}

	function _install()
	{
		global $tikilib;
		$data = $this->getData();

		$this->replaceReferences($data);

		$name = $data['name'];
		unset( $data['name'] );
		
		$parserlib = TikiLib::lib('parser');
		$parserlib->plugin_alias_store($name, $data);

		return $name;
	}
}
