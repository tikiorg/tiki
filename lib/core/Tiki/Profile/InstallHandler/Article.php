<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Tiki_Profile_InstallHandler_Article extends Tiki_Profile_InstallHandler
{
	function getData()
	{
		if ( $this->data )
			return $this->data;

		$data = $this->obj->getData();

		$defaults = array(
			'author' => 'Anonymous',
			'heading' => '',
			'publication_date' => time(),
			'expiration_date' => time() + 3600*24*365,
			'type' => 'Article',
			'topline' => '',
			'subtitle' => '',
			'link_to' => '',
			'language' => 'en',
		);

		$data = array_merge($defaults, $data);

		return $this->data = $data;
	}

	function canInstall()
	{
		$data = $this->getData();

		if ( ! isset( $data['title'], $data['topic'], $data['body'] ) )
			return false;

		return true;
	}

	function _install()
	{
		global $artlib;
		$data = $this->getData();

		$this->replaceReferences($data);

		require_once 'lib/articles/artlib.php';

		$dateConverter = new Tiki_Profile_DateConverter;

		$id = $artlib->replace_article(
			$data['title'],
			$data['author'],
			$data['topic'],
			'n',
			null,
			null,
			null,
			null,
			$data['heading'],
			$data['body'],
			$dateConverter->convert($data['publication_date']),
			$dateConverter->convert($data['expiration_date']),
			'admin',
			0,
			0,
			0,
			$data['type'],
			$data['topline'],
			$data['subtitle'],
			$data['link_to'],
			null,
			$data['language']
		);

		return $id;
	}
}
