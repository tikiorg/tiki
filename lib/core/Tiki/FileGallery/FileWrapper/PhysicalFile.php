<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

namespace Tiki\FileGallery\FileWrapper;

class PhysicalFile implements WrapperInterface
{
	private $path;
	private $basePath;

	function __construct($basePath, $path)
	{
		$this->basePath = rtrim($basePath, '/\\');
		$this->path = $path;
	}

	function getReadableFile()
	{
		$savedir = $this->basePath;
		return $savedir . '/' . $this->path;
	}

	function getContents()
	{
		$tmpfname = $this->basePath . '/' . $this->path;

		return \file_get_contents($tmpfname);
	}
}

