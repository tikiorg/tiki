<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Tiki_Profile_InstallHandler_Rss extends Tiki_Profile_InstallHandler
{
	function getData()
	{
		if ( $this->data )
			return $this->data;

		$data = $this->obj->getData();
		$data = Tiki_Profile::convertLists($data, array('show' => 'y'), true);

		$defaults = array(
			'description' => null,
			'refresh' => 30,
			'show_title' => 'n',
			'show_publication_date' => 'n',
		);

		$data = array_merge($defaults, $data);
		$data = Tiki_Profile::convertYesNo($data);

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
		global $rsslib;
		$data = $this->getData();

		$this->replaceReferences($data);

		require_once 'lib/rss/rsslib.php';

		if ( $rsslib->replace_rss_module(0, $data['name'], $data['description'], $data['url'], $data['refresh'], $data['show_title'], $data['show_publication_date']) ) {

			$id = (int) $rsslib->getOne("SELECT MAX(`rssId`) FROM `tiki_rss_modules`");
			return $id;
		}
	}
}
