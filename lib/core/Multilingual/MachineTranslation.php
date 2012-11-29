<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

 
class Multilingual_MachineTranslation
{
	private $implementation = 'null';
	private $key;

	public static function force($implementation, $key)
	{
		$self = new self;
		$self->implementation = $implementation;
		$self->key = $key;

		return $self;
	}

	function __construct()
	{
		global $prefs;

		switch ($prefs['lang_machine_translate_implementation']) {
		case 'google':
			if (! empty($prefs['lang_google_api_key'])) {
				$this->implementation = 'google';
				$this->key = $prefs['lang_google_api_key'];
			}
			break;
		}
	}

	function getHtmlImplementation($source, $target)
	{
		switch ($this->implementation) {
		case 'google':
			return new Multilingual_MachineTranslation_GoogleTranslateWrapper($this->key, $source, $target, true);
		case 'null':
		default:
			return new Multilingual_MachineTranslation_Null;
		}
	}

	function getWikiImplementation($source, $target)
	{
		switch ($this->implementation) {
		case 'google':
			return new Multilingual_MachineTranslation_GoogleTranslateWrapper($this->key, $source, $target, false);
		case 'null':
		default:
			return new Multilingual_MachineTranslation_Null;
		}
	}
}
