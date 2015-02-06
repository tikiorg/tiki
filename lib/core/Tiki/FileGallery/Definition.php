<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

namespace Tiki\FileGallery;

class Definition
{
	private $info;

	function __construct($info)
	{
		$this->info = $info;
		$this->handler = $this->getHandler($info);
	}

	function getFileWrapper($data, $path)
	{
		return $this->handler->getFileWrapper($data, $path);
	}

	function delete($data, $path)
	{
		$this->handler->delete($data, $path);
	}

	function getInfo()
	{
		return $this->info;
	}

	private function getHandler($info)
	{
		switch ($info['type']) {
		case 'podcast':
		case 'vidcast':
			return new Handler\PodCast();
		case 'system':
		default:
			return new Handler\System();
		}
	}
}

