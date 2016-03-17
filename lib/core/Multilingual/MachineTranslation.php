<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$


class Multilingual_MachineTranslation
{
	const DETECT_LANGUAGE = 'auto';

	private $implementation = 'null';

	public static function force($implementation)
	{
		$self = new self;
		$self->implementation = $implementation;

		return $self;
	}

	function __construct()
	{
		global $prefs;

		switch ($prefs['lang_machine_translate_implementation']) {
		case 'bing':
			if (! empty($prefs['lang_bing_api_client_id'])) {
				$this->implementation = 'bing';
			}
			break;

		case 'google':
			if (! empty($prefs['lang_google_api_key'])) {
				$this->implementation = 'google';
			}
			break;
		}
	}

	function getAvailableLanguages($realTranslations)
	{
		global $langmapping, $prefs;
		$usedLangs = array();
		foreach ($realTranslations as $trad) {
			$usedLangs[] = $trad['lang'];
		}

		if ($prefs['restrict_language'] === 'y' && !empty($prefs['available_languages'])) {
			$candidates = array();

			//restrict langs available for machine translation to those
			//available on the site
			foreach ($prefs['available_languages'] as $availLang) {
				$candidates[$availLang] = $langmapping[$availLang];
			}
		} else {
			$candidates = $langmapping;
		}

		//restrict langs available for machine translation to those
		//not already used for human translation
		foreach ($usedLangs as $usedLang) {
			unset($candidates[$usedLang]);
		}


		//restrict langs available for machine translation to those
		//available from Google Translate
		$proposed = array();
		$supportedLanguages = $this->getHtmlImplementation('', '')
			->getSupportedLanguages();
		foreach ($candidates as $langCandidate => $name) {
			if (isset($supportedLanguages[$langCandidate])) {
				$proposed[] = array(
					'lang' => $langCandidate,
					'langName' => is_array($name) ? reset($name) : $name,
				);
			}
		}
		return $proposed;
	}

	function getHtmlImplementation($source, $target)
	{
		global $prefs;

		$handler = null;

		switch ($this->implementation) {
		case 'bing':
			$clientId = $prefs['lang_bing_api_client_id'];
			$clientSecret = $prefs['lang_bing_api_client_secret'];
			$handler = new Multilingual_MachineTranslation_BingTranslateWrapper($clientId, $clientSecret, $source, $target);
			break;
		case 'google':
			$key = $prefs['lang_google_api_key'];
			$handler = new Multilingual_MachineTranslation_GoogleTranslateWrapper($key, $source, $target, true);
			break;
		case 'null':
		default:
			return new Multilingual_MachineTranslation_Null;
		}

		return new Multilingual_MachineTranslation_Cache($handler, $source . $target);
	}

	function getDetectImplementation($target)
	{
		return $this->getHtmlImplementation(self::DETECT_LANGUAGE, $target);
	}
}

