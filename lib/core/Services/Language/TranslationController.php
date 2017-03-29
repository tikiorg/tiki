<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$
//
// TranslationController manages translations of objects (for example translations of a wiki page)

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

	/**
	 * List translations relation of an instance of an object type (eg: translations of a wiki page)
	 *
	 * @param $input
	 *
	 * @return array Array of objects linked together as translations of each other
	 *
	 * @throws Services_Exception
	 */
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
			'title' => tr('Manage translations'),
			'type' => $type,
			'source' => $object,
			'filters' => $this->getSearchFilters($type, $object),
			'translations' => $this->utilities->getTranslations($type, $object),
			'canAttach' => $this->canAttach($type, $object),
			'canDetach' => $this->canDetach($type, $object),
		);
	}

	/**
	 * Attach (link) translations for objects (eg: wiki pages)
	 *
	 * @param $input Instances of object types, eg: wiki pages
	 *
	 * @return Forward to utility action to perform the attaching
	 *
	 * @throws Services_Exception
	 */
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
			throw new Services_Exception(tr('You do not have permission to attach the selected translations'), 403);
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

	/**
	 * Detach (unlink) translations for objects (eg: wiki pages)
	 *
	 * @param $input Instances of object types, eg: wiki pages
	 *
	 * @return Forward to utility action to perform the deattaching
	 *
	 * @throws Services_Exception
	 */
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
			throw new Services_Exception(tr('You do not have permission to detach the selected translations'), 403);
		}

		if (! $confirmed) {
			return array(
				'title' => tr('Manage translations'),
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

	/**
	 * Machine translation of an object (eg: a wiki page)
	 *
	 * @param $input An object, eg: a wiki page
	 *
	 * @return action Forward to utility action to perform the attaching
	 */
	function action_translate($input)
	{
		Services_Exception_Disabled::check('feature_machine_translation');

		global $prefs;

		$content = $input->content->rawhtml_unsafe();
		if (!empty($input->lang->text())) {
			$lang = $input->lang->text();
		} else {
			$lang = $prefs['language'];
		}

		$factory = new Multilingual_MachineTranslation;
		$impl = $factory->getDetectImplementation($lang);

		$content = $impl->translateText($content);

		return array(
			'content' => $content,
			'target' => $lang
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
		$langLib = TikiLib::lib('language');
		$languages = $langLib->get_language_map();

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

	/**
	 * Private function to determine if user is allowed to attach (link) translations for objects (eg: wiki pages)
	 *
	 * @param string $type object type, eg: wiki page
	 * @param int $object an instance of object type, eg: a wiki page
	 *
	 * @return
	 */
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

	/**
	 * Private function to determine if user is allowed to detach (unlink) translations for objects (eg: wiki pages)
	 *
	 * @param string $type object type, eg: wiki page
	 * @param int $object an instance of object type, eg: a wiki page
	 *
	 * @return
	 */
	private function canDetach($type, $object)
	{
		$perms = Perms::get($type, $object);
		return $perms->detach_translation;
	}

}

