<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

namespace Tiki\FileGallery\Handler;
use Tiki\FileGallery\FileWrapper\PreloadedContent;

class System implements HandlerInterface
{
	private $real;

	function __construct()
	{
		global $prefs;

		if ($prefs['fgal_use_db'] == 'n') {
			$this->real = new FileSystem($prefs['fgal_use_dir']);
		} else {
			$this->real = new Preloaded;
		}
	}

	function getFileWrapper($data, $path)
	{
		return $this->real->getFileWrapper($data, $path);
	}

	function delete($data, $path)
	{
		return $this->real->delete($data, $path);
	}
}

