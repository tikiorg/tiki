<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Tiki_Profile_InstallHandler_WebserviceTemplate extends Tiki_Profile_InstallHandler
{
	function getData()
	{
		if ( $this->data )
			return $this->data;

		$defaults = array();

		$data = array_merge($defaults, $this->obj->getData());

		return $this->data = $data;
	}

	function canInstall()
	{
		$data = $this->getData();

		if ( ! isset( $data['name'], $data['engine'], $data['output'], $data['content'] ) )
			return false;

		return true;
	}

	function _install()
	{
		global $tikilib;
		$data = $this->getData();

		$this->replaceReferences($data);

		require_once 'lib/webservicelib.php';

		$ws = Tiki_Webservice::getService($data['webservice']);
		$template = $ws->addTemplate($data['name']);
		$template->engine = $data['engine'];
		$template->output = $data['output'];
		$template->content = $data['content'];
		$template->save();

		return $template->name;
	}
}
