<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Services_Language_TranslationController
{
	function action_manage($input)
	{
		global $prefs;

		if ($prefs['feature_multilingual'] != 'y') {
			throw new Services_Exception(tr('Feature Disabled'), 403);
		}

		$type = $input->type->text();
		$objectFilter = $this->getObjectFilter($type);

		if (! $objectFilter) {
			throw new Services_Exception(tr('Translation not supported for the specified object type'), 400);
		}

		$object = $input->source->$objectFilter();

		if (! $object) {
			throw new Services_Exception(tr('No source provided'), 400);
		}

		return array(
			'type' => $type,
			'source' => $object,
			'filters' => $this->getSearchFilters($type, $object),
			'translations' => $this->getTranslations($type, $object),
			'canAttach' => $this->canAttach($type, $object),
			'canDetach' => $this->canDetach($type, $object),
		);
	}

	function action_attach($input)
	{
		global $prefs;

		if ($prefs['feature_multilingual'] != 'y') {
			throw new Services_Exception(tr('Feature Disabled'), 403);
		}

		$type = $input->type->text();
		$objectFilter = $this->getObjectFilter($type);

		if (! $objectFilter) {
			throw new Services_Exception(tr('Translation not supported for the specified object type'), 400);
		}

		$source = $input->source->$objectFilter();
		$target = $input->target->none();
		$target = end(explode(':', $target, 2));
		$target = TikiFilter::get($objectFilter)->filter($target);

		if (! $source || ! $target) {
			throw new Services_Exception(tr('No source or target provided'), 400);
		}

		if (! $this->canAttach($type, $source) || ! $this->canAttach($type, $target)) {
			throw new Services_Exception(tr('Not allowed to attach the selected translations'), 403);
		}

		$succeeded = $this->insertTranslation($type, $source, $target);

		if (! $succeeded) {
			throw new Services_Exception(tr('Could not attach the translations.'), 409);
		}

		return array(
			'FORWARD' => array(
				'action' => 'manage',
				'type' => $type,
				'source' => $source,
			),
		);
	}

	function action_detach($input)
	{
		global $prefs;

		if ($prefs['feature_multilingual'] != 'y') {
			throw new Services_Exception(tr('Feature Disabled'), 403);
		}

		$type = $input->type->text();
		$objectFilter = $this->getObjectFilter($type);
		$confirmed = $input->confirm->int();

		if (! $objectFilter) {
			throw new Services_Exception(tr('Translation not supported for the specified object type'), 400);
		}

		$source = $input->source->$objectFilter();
		$target = $input->target->$objectFilter();

		if (! $source || ! $target) {
			throw new Services_Exception(tr('No source or target provided'), 400);
		}

		if (! $this->canDetach($type, $source) || ! $this->canDetach($type, $target)) {
			throw new Services_Exception(tr('Not allowed to detach the selected translations'), 403);
		}

		if (! $confirmed) {
			return array(
				'type' => $type,
				'source' => $source,
				'target' => $target,
			);
		}

		$this->detachTranslation($type, $source, $target);

		return array(
			'FORWARD' => array(
				'action' => 'manage',
				'type' => $type,
				'source' => $source,
			),
		);
	}

	private function getObjectFilter($type)
	{
		switch ($type) {
			case 'wiki page':
				return 'pagename';
			case 'article':
			case 'trackeritem':
				return 'int';
		}
	}

	private function getSearchFilters($type, $object)
	{
		$translations = $this->getTranslations($type, $object);
		$languages = TikiLib::get_language_map();

		foreach ($translations as $trans) {
			unset($languages[$trans['lang']]);
		}

		unset($languages[$this->getLanguage($type, $object)]);

		$language = '"' . implode('" OR "', array_keys($languages)) . '"';
		if ($language == '""') {
			$language = null;
		}

		$filters = array(
			'type' => $type,
			'language' => $language,
		);

		if ($type == 'trackeritem') {
			$info = TikiLib::lib('trk')->get_tracker_item($object);
			$filters['tracker_id'] = $info['trackerId'];
		}

		return $filters;
	}

	private function getTranslations($type, $object)
	{
		$multilinguallib = TikiLib::lib('multilingual');
		$tikilib = TikiLib::lib('tiki');

		$objId = $this->toInternalId($type, $object);

		$translations = $multilinguallib->getTrads($type, $objId);
		$languages = $tikilib->get_language_map();

		foreach ($translations as & $trans) {
			if ($type == 'wiki page') {
				$trans['objId'] = $tikilib->get_page_name_from_id($trans['objId']);
			}

			$trans['language'] = $languages[$trans['lang']];
		}
		
		return $translations;
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

	private function canAttach($type, $object)
	{
		global $prefs, $user;
		$perms = Perms::get($type, $object);

		if ($type == 'wiki page' && $perms->edit) {
			return true;
		}

		if ($type == 'article' && $perms->edit_article) {
			return true;
		}

		if ($type == 'wiki page' && $prefs['wiki_creator_admin'] == 'y' && $user) {
			$info = TikiLib::lib('tiki')->get_page_info($object);
			return $info['creator'] == $user;
		}

		if ($type == 'article' && $user) {
			$artlib = TikiLib::lib('art');
			$info = $artlib->get_article($object);
			return $info['author'] == $user && $info['creator_edit'] == 'y';
		}

		return $perms->admin;
	}

	private function canDetach($type, $object)
	{
		$perms = Perms::get($type, $object);
		return $perms->detach_translation;
	}

	private function insertTranslation($type, $source, $target)
	{
		$multilinguallib = TikiLib::lib('multilingual');
		$sourceLang = $this->getLanguage($type, $source);
		$sourceId = $this->toInternalId($type, $source);

		$targetLang = $this->getLanguage($type, $target);
		$targetId = $this->toInternalId($type, $target);

		$out = $multilinguallib->insertTranslation($type, $sourceId, $sourceLang, $targetId, $targetLang);

		return ! $out;
	}

	private function detachTranslation($type, $source, $target)
	{
		$multilinguallib = TikiLib::lib('multilingual');
		$targetId = $this->toInternalId($type, $target);

		$multilinguallib->detachTranslation($type, $targetId);
	}

	private function getLanguage($type, $object)
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
}

