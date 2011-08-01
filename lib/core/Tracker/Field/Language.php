<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Tracker_Field_Language extends Tracker_Field_Abstract implements Tracker_Field_Synchronizable
{
	const OPT_AUTOASSIGN = 0;

	public static function getTypes()
	{
		return array(
			'LANG' => array(
				'name' => tr('Language'),
				'description' => tr('Assign a language to the tracker item to enable multilingual trackers.'),
				'params' => array(
					'autoassign' => array(
						'name' => tr('Auto-Assign'),
						'description' => tr('Indicates if the language should be assigned as the item\'s language.'),
						'filter' => 'int',
						'options' => array(
							0 => tr('No'),
							1 => tr('Yes'),
						),
					),
				),
			),
		);
	}

	function getFieldData(array $requestData = array())
	{
		$insertId = $this->getInsertId();

		if (isset($requestData[$insertId])) {
			$selected = $requestData[$insertId];

			if ($this->isLanguageAvailable($selected)) {
				return array(
					'value' => $selected,
				);
			}
		}

		return array(
			'value' => $this->getValue(),
		);
	}

	function renderInput($context = array())
	{
		$context['languages'] = $this->getLanguages();
		return $this->renderTemplate('trackerinput/language.tpl', $context);
	}

	function renderOutput($context = array())
	{
		$selected = $this->getConfiguration('value');

		$languages = $this->getLanguages();
		$context['label'] = isset($languages[$selected]) ? $languages[$selected] : tr('None');
		return $this->renderTemplate('trackeroutput/language.tpl', $context);
	}

	function handleSave($value, $oldValue)
	{
		return array(
			'value' => $value,
		);
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
			}
		}
	}

	private function getLanguages()
	{
		return TikiLib::get_language_map();
	}

	private function isLanguageAvailable($lang)
	{
		$languages = $this->getLanguages();
		return isset($languages[$lang]);
	}

	function import($value)
	{
		return $value;
	}

	function export($value)
	{
		return $value;
	}

	function importField(array $info, array $syncInfo)
	{
		return $info;
	}
}

