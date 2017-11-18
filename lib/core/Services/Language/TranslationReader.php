<?php
// (c) Copyright 2002-2017 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$


class TranslationReader
{
	/**
	 * Creates a TranslationReader with the file to be translated. this
	 * class can recognize JSON and CSV files.
	 * @param [type] $filename [description]
	 */
	public function __construct($filename)
	{
		$this->filename = $filename;
	}

	/**
	 * Return an associative array with translations. The array key is an
	 * english word and the value is the word translation.
	 *
	 * @return array translations
	 */
	public function getArray()
	{
		$ext = null;
		$valid = is_string($this->filename)
			&& file_exists($this->filename)
			&& preg_match('/\.([a-z]{3,})$/', $this->filename, $ext)
			&& ! empty($ext[1])
			&& method_exists($this, $ext[1] . "Read");

		if (! $valid) {
			return null;
		}

		$method = $ext[1] . "Read";
		return call_user_func([$this, $method]);
	}

	private function csvRead()
	{
		$handle = fopen($this->filename, 'r');
		$header = fgetcsv($handle);
		$translations = [];

		$source_index = array_search('en', $header) ?: 0;
		$target_index = $source_index > 0 ? 0 : 1;

		while (($row = fgetcsv($handle))) {
			$source = $row[ $source_index ];
			$target = $row[ $target_index ];
			$translations[ $source ] = $target;
		}

		return $translations;
	}

	private function jsonRead()
	{
		$content = file_get_contents('temp/' . $_FILES['language_file']['name']);
		return json_decode($content, true);
	}
}
