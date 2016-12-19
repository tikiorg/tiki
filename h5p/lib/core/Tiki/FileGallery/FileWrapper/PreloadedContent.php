<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

namespace Tiki\FileGallery\FileWrapper;

class PreloadedContent implements WrapperInterface
{
	private $data;

	private $temporaryFile = false;

	function __construct($data)
	{
		$this->data = $data;
	}

	function __destruct()
	{
		if (false !== $this->temporaryFile) {
			\unlink($this->temporaryFile);
		}
	}

	function getReadableFile()
	{
		if (false !== $this->temporaryFile) {
			return $this->temporaryFile;
		}

		$sIniUploadTmpDir = \ini_get('upload_tmp_dir');
		if (!empty($sIniUploadTmpDir)) {
			$sTmpDir = \ini_get('upload_tmp_dir');
		} else {
			$sTmpDir = '/tmp';
		}

		$this->temporaryFile = $tmpfname = \tempnam($sTmpDir, 'wiki_');
		@\file_put_contents($tmpfname, $this->data);
		return $tmpfname;
	}

	function getContents()
	{
		return $this->data;
	}
}

