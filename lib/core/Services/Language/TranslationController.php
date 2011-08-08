<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Services_Language_TranslationController
{
	private $utilities;

	function __construct()
	{
		$this->utilities = new Services_Language_Utilities;
	}

	function setUp()
	{
		global $prefs;

		if ($prefs['feature_multilingual'] != 'y') {
			throw new Services_Exception(tr('Feature Disabled'), 403);
		}
	}

	function action_manage($input)
	{
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
			'translations' => $this->utilities->getTranslations($type, $object),
			'canAttach' => $this->canAttach($type, $object),
			'canDetach' => $this->canDetach($type, $object),
		);
	}

	function action_attach($input)
	{
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

		$succeeded = $this->utilities->insertTranslation($type, $source, $target);

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

		$this->utilities->detachTranslation($type, $source, $target);

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
		$translations = $this->utilities->getTranslations($type, $object);
		$languages = TikiLib::get_language_map();

		foreach ($translations as $trans) {
			unset($languages[$trans['lang']]);
		}

		unset($languages[$this->utilities->getLanguage($type, $object)]);

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

}

