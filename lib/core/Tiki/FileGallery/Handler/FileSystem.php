<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

namespace Tiki\FileGallery\Handler;
use Tiki\FileGallery\FileWrapper\PhysicalFile;

class FileSystem implements HandlerInterface
{
	private $directory;

	function __construct($directory)
	{
		$this->directory = $directory;
		$this->directory = rtrim($directory, '/\\');
	}

	function getFileWrapper($data, $path)
	{
		return new PhysicalFile($this->directory, $path);
	}

	function delete($data, $path)
	{
		$full = "{$this->directory}/$path";

		if ($path && is_writable($full)) {
			unlink($full);
		}
	}
}

