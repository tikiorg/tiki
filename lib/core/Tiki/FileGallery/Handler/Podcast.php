<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

namespace Tiki\FileGallery\Handler;
use Tiki\FileGallery\FileWrapper\PhysicalFile;

class Podcast implements HandlerInterface
{
	function getFileWrapper($data, $path)
	{
		global $prefs;

		return new PhysicalFile($prefs['fgal_podcast_dir'], $path);
	}
}

