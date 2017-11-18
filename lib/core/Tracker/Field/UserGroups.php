<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * Handler class for UserGroups
 *
 * Letter key: ~usergroups~
 *
 */
class Tracker_Field_UserGroups extends Tracker_Field_Abstract
{
	public static function getTypes()
	{
		return [
			'usergroups' => [
				'name' => tr('User Groups'),
				'description' => tr('Displays the list of groups for the user associated with the tracker items.'),
				'help' => 'User Groups',
				'prefs' => ['trackerfield_usergroups'],
				'tags' => ['advanced'],
				'default' => 'n',
				'params' => [
				],
			],
		];
	}

	function getFieldData(array $requestData = [])
	{
		$itemId = $this->getItemId();

		$value = [];

		if ($itemId) {
			$itemUsers = $this->getTrackerDefinition()->getItemUsers($itemId);

			if (! empty($itemUsers)) {
				$tikilib = TikiLib::lib('tiki');
				foreach ($itemUsers as $itemUser) {
					$value = array_merge($value, array_diff($tikilib->get_user_groups($itemUser), ['Registered', 'Anonymous']));
				}
			}
		}

		return ['value' => $value];
	}

	function renderInput($context = [])
	{
		return $this->renderOutput($context);
	}

	function renderOutput($context = [])
	{
		return $this->renderTemplate('trackeroutput/usergroups.tpl', $context);
	}

	function getDocumentPart(Search_Type_Factory_Interface $typeFactory)
	{
		$baseKey = $this->getBaseKey();
		$data = $this->getFieldData();
		$listtext = implode(' ', $data['value']);

		return [
			$baseKey => $typeFactory->multivalue($data['value']),
			"{$baseKey}_text" => $typeFactory->plaintext($listtext),
		];
	}

	function getProvidedFields()
	{
		$baseKey = $this->getBaseKey();
		return [$baseKey];
	}

	function getGlobalFields()
	{
		$baseKey = $this->getBaseKey();
		return [$baseKey => true];
	}
}
