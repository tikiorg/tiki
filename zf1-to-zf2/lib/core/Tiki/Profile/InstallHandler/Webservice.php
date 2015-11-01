<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Tiki_Profile_InstallHandler_Webservice extends Tiki_Profile_InstallHandler
{
	function getData()
	{
		if ( $this->data )
			return $this->data;

		$defaults = array(
			'schema_version' => null,
			'schema_documentation' => null,
		);

		$data = array_merge($defaults, $this->obj->getData());

		return $this->data = $data;
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
		global $tikilib;
		$data = $this->getData();

		$this->replaceReferences($data);

		require_once 'lib/webservicelib.php';

		$ws = Tiki_Webservice::create($data['name']);
		$ws->url = $data['url'];
		$ws->body = $data['body'];
		$ws->schemaVersion = $data['schema_version'];
		$ws->schemaDocumentation = $data['schema_documentation'];
		$ws->save();

		return $ws->getName();
	}
}
