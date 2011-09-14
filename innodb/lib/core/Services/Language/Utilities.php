<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Services_Language_Utilities
{
	function insertTranslation($type, $source, $target)
	{
		$multilinguallib = TikiLib::lib('multilingual');
		$sourceLang = $this->getLanguage($type, $source);
		$sourceId = $this->toInternalId($type, $source);

		$targetLang = $this->getLanguage($type, $target);
		$targetId = $this->toInternalId($type, $target);

		$out = $multilinguallib->insertTranslation($type, $sourceId, $sourceLang, $targetId, $targetLang);

		return ! $out;
	}

	function detachTranslation($type, $source, $target)
	{
		$multilinguallib = TikiLib::lib('multilingual');
		$targetId = $this->toInternalId($type, $target);

		$multilinguallib->detachTranslation($type, $targetId);
	}

	function getTranslations($type, $object)
	{
		$multilinguallib = TikiLib::lib('multilingual');
		$tikilib = TikiLib::lib('tiki');

		$objId = $this->toInternalId($type, $object);

		$translations = $multilinguallib->getTrads($type, $objId);
		$languages = $tikilib->get_language_map();

		foreach ($translations as & $trans) {
			$trans['objId'] = $this->toExternalId($type, $trans['objId']);
			$trans['language'] = $languages[$trans['lang']];
		}
		
		return $translations;
	}

	function getLanguage($type, $object)
	{
		$lang = null;
		switch ($type) {
			case 'wiki page':
				$info = TikiLib::lib('tiki')->get_page_info($object);
				$lang = $info['lang'];
				break;
			case 'article':
				$info = TikiLib::lib('art')->get_article($object);
				$lang = $info['lang'];
				break;
			case 'trackeritem':
				$info = TikiLib::lib('trk')->get_tracker_item($object);
				$definition = Tracker_Definition::get($info['trackerId']);
				
				if ($field = $definition->getLanguageField()) {
					$lang = $info[$field];
				}
				break;
		}

		if (! $lang) {
			throw new Services_Exception(tr('Object has no language and cannot be translated'), 400);
		}

		return $lang;
	}

	private function toInternalId($type, $object)
	{
		if ($type == 'wiki page') {
			$tikilib = TikiLib::lib('tiki');
			return $tikilib->get_page_id_from_name($object);
		} else {
			return $object;
		}
	}

	private function toExternalId($type, $object)
	{
		if ($type == 'wiki page') {
			$tikilib = TikiLib::lib('tiki');
			return $tikilib->get_page_name_from_id($object);
		} else {
			return $object;
		}
	}
}

