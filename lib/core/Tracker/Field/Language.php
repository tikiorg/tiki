<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Tracker_Field_Language extends Tracker_Field_Abstract implements Tracker_Field_Synchronizable
{
	const OPT_AUTOASSIGN = 0;

	public static function getTypes()
	{
		return [
			'LANG' => [
				'name' => tr('Language'),
				'description' => tr('Assign a language to the tracker item to enable multilingual trackers.'),
				'prefs' => ['trackerfield_language', 'feature_multilingual'],
				'tags' => ['advanced'],
				'default' => 'y',
				'params' => [
					'autoassign' => [
						'name' => tr('Auto-Assign'),
						'description' => tr('Indicates if the language should be assigned as the item\'s language.'),
						'filter' => 'int',
						'options' => [
							0 => tr('No'),
							1 => tr('Yes'),
						],
						'legacy_index' => 0,
					],
				],
			],
		];
	}

	function getFieldData(array $requestData = [])
	{
		$insertId = $this->getInsertId();

		if (isset($requestData[$insertId])) {
			$selected = $requestData[$insertId];

			if ($this->isLanguageAvailable($selected)) {
				return [
					'value' => $selected,
				];
			}
		}

		return [
			'value' => $this->getValue(),
		];
	}

	function renderInput($context = [])
	{
		return $this->renderTemplate(
			'trackerinput/language.tpl',
			$context,
			[
				'languages' => $this->getLanguages(),
			]
		);
	}

	function renderOutput($context = [])
	{
		$selected = $this->getConfiguration('value');

		if ($context['list_mode'] == 'csv') {
			return $selected;
		}

		$languages = $this->getLanguages();
		return $this->renderTemplate(
			'trackeroutput/language.tpl',
			$context,
			[
				'label' => isset($languages[$selected]) ? $languages[$selected] : tr('None'),
			]
		);
	}

	function handleSave($value, $oldValue)
	{
		return [
			'value' => $value,
		];
	}

	function watchCompare($old, $new)
	{
	}

	public static function update_language($args)
	{
		$definition = Tracker_Definition::get($args['trackerId']);
		$fieldId = $definition->getLanguageField();

		if ($fieldId) {
			$old = isset($args['old_values'][$fieldId]) ? $args['old_values'][$fieldId] : null;
			$new = isset($args['values'][$fieldId]) ? $args['values'][$fieldId] : null;

			if ($old != $new) {
				$multilinguallib = TikiLib::lib('multilingual');
				$multilinguallib->updateObjectLang('trackeritem', $args['object'], $new);
				TikiLib::lib('trk')->sync_user_lang($args);
			}
		}
	}

	private function getLanguages()
	{
		$langLib = TikiLib::lib('language');
		return $langLib->get_language_map();
	}

	private function isLanguageAvailable($lang)
	{
		$languages = $this->getLanguages();
		return isset($languages[$lang]);
	}

	function importRemote($value)
	{
		return $value;
	}

	function exportRemote($value)
	{
		return $value;
	}

	function importRemoteField(array $info, array $syncInfo)
	{
		return $info;
	}

	function getDocumentPart(Search_Type_Factory_Interface $typeFactory)
	{
		$baseKey = $this->getBaseKey();
		return [
			$baseKey => $typeFactory->sortable($this->getValue()),
			'language' => $typeFactory->identifier($this->getValue()),
		];
	}

	function getProvidedFields()
	{
		$baseKey = $this->getBaseKey();
		return [$baseKey, 'language'];
	}

	function getGlobalFields()
	{
		return [];
	}
}
