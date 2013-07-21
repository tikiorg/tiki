<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * Handler class for UserSelector
 *
 * Letter key: ~u~
 *
 */
class Tracker_Field_UserSelector extends Tracker_Field_Abstract implements Tracker_Field_Synchronizable
{
	public static function getTypes()
	{
		return array(
			'u' => array(
				'name' => tr('User Selector'),
				'description' => tr('Allows the selection of a user from a list.'),
				'help' => 'User selector',
				'prefs' => array('trackerfield_userselector'),
				'tags' => array('basic'),
				'default' => 'y',
				'params' => array(
					'autoassign' => array(
						'name' => tr('Auto-Assign'),
						'description' => tr('Assign the value based on the creator or modifier.'),
						'filter' => 'int',
						'default' => 0,
						'options' => array(
							0 => tr('None'),
							1 => tr('Creator'),
							2 => tr('Modifier'),
						),
						'legacy_index' => 0,
					),
					'notify' => array(
						'name' => tr('Email Notification'),
						'description' => tr('Send an email notification to the user every time the item is modified.'),
						'filter' => 'int',
						'options' => array(
							0 => tr('No'),
							1 => tr('Yes'),
							2 => tr('Only when other users modify the item'),
						),
						'legacy_index' => 1,
					),
					'groupIds' => array(
						'name' => tr('Group IDs'),
						'description' => tr('Limit the list of users to members of specific groups.'),
						'separator' => '|',
						'filter' => 'int',
						'legacy_index' => 2,
					),
				),
			),
		);
	}

	function getFieldData(array $requestData = array())
	{
		global $tiki_p_admin_trackers, $user;

		$ins_id = $this->getInsertId();

		$data = array();

		$autoassign = (int) $this->getOption('autoassign');

		if ( isset($requestData[$ins_id])) {
			if ($autoassign == 0 || $tiki_p_admin_trackers === 'y') {
				$data['value'] = $requestData[$ins_id];
			} else {
				if ($autoassign == 2) {
					$data['value'] = $user;
				} elseif ($autoassign == 1) {
					if (!$this->getItemId() || ($this->getTrackerDefinition()->getConfiguration('userCanTakeOwnership')  == 'y' && !$this->getValue())) {
						$data['value'] = $user; // the user appropiate the item
					} else {
						$data['value'] = $this->getValue();
						// unset($data['fieldId']); hmm?
					}
				} else {
					$data['value'] = '';
				}
			}
		} elseif (! $this->getItemId() && $autoassign > 0) {
			$data['value'] = $user;
		} else {
			$data['value'] = $this->getValue(false);
		}

		return $data;
	}

	function renderInput($context = array())
	{
		global $tiki_p_admin_trackers, $user;
		$smarty = TikiLib::lib('smarty');

		$value = $this->getConfiguration('value');
		$autoassign = (int) $this->getOption('autoassign');
		if ((empty($value) && $autoassign == 1) || $autoassign == 2) {	// always use $user for last mod autoassign
			$value = $user;
		}
		if ($autoassign == 0 || $tiki_p_admin_trackers === 'y') {
			$groupIds = $this->getOption('groupIds', '');

			$smarty->loadPlugin('smarty_function_user_selector');
			return smarty_function_user_selector(
				array(
					'user' => $value,
					'id'  => 'user_selector_' . $this->getConfiguration('fieldId'),
					'select' => $value,
					'name' => $this->getConfiguration('ins_id'),
					'editable' => 'y',
					'allowNone' => $this->getConfiguration('isMandatory') === 'y' ? 'n' : 'y',
					'groupIds' => $groupIds,
				),
				$smarty
			);
		} else {
			$smarty->loadPlugin('smarty_modifier_username');
			return smarty_modifier_username($value) . '<input type="hidden" name="' . $this->getInsertId() . '" value="' . $value . '">';
		}
	}

	function renderInnerOutput($context = array())
	{
		$value = $this->getConfiguration('value');
		if (empty($value)) {
			return '';
		} else {
			TikiLib::lib('smarty')->loadPlugin('smarty_modifier_username');
			return smarty_modifier_username($value);
		}
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
		$groupIds = $this->getOption('groupIds', '');
		$groupIds = array_filter(explode('|', $groupIds));
		$groupIds = array_map('intval', $groupIds);

		$controller = new Services_RemoteController($syncInfo['provider'], 'user');
		$users = $controller->getResultLoader(
			'list_users',
			array(
				'groupIds' => $groupIds,
			)
		);

		$list = array();
		foreach ($users as $user) {
			$list[] = $user['login'];
		}

		if (count($list)) {
			$info['type'] = 'd';
			$info['options'] = implode(',', $list);
		} else {
			$info['type'] = 't';
			$info['options'] = '';
		}

		return $info;
	}

	function getDocumentPart(Search_Type_Factory_Interface $typeFactory)
	{
		$baseKey = $this->getBaseKey();
		return array(
			$baseKey => $typeFactory->identifier($this->getValue()),
		);
	}
}

