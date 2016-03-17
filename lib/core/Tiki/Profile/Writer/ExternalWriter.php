<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Tiki_Profile_Writer_ExternalWriter
{
	private $dataPath;
	private $files;
	private $hashes = array();

	function __construct($dataPath)
	{
		$this->dataPath = $dataPath;
		$files = array_filter(
			scandir($dataPath),
			function ($file) {
				return $file{0} != '.';
			}
		);

		$this->files = array_fill_keys($files, null);
	}

	function write($file, $content)
	{
		$this->files[$file] = $content;
		$this->hashes[$file] = null;
	}

	function apply()
	{
		foreach (array_filter($this->files) as $file => $content) {
			$hash = sha1($content);
			if ($hash != $this->hashes[$file]) {
				file_put_contents("{$this->dataPath}/$file", $content);
			}
		}
	}

	function getFiles()
	{
		foreach ($this->files as $file => $content) {
			if (is_null($content)) {
				$this->files[$file] = file_get_contents("{$this->dataPath}/$file");
				$this->hashes[$file] = sha1($this->files[$file]);
			}
		}

		return $this->files;
	}
}
