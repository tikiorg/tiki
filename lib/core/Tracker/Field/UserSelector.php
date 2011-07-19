<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
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
class Tracker_Field_UserSelector extends Tracker_Field_Abstract
{
	public static function getTypes()
	{
		return array(
			'u' => array(
				'name' => tr('User Selector'),
				'description' => tr('Allows the selection of a user from a list.'),
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
					),
					'notify' => array(
						'name' => tr('Email Notification'),
						'description' => tr('Send an email notification to the user every time the item is modified.'),
						'filter' => 'int',
						'options' => array(
							0 => tr('No'),
							1 => tr('Yes'),
						),
					),
					'groupIds' => array(
						'name' => tr('Group IDs'),
						'description' => tr('Limit the list of users to members of specific groups.'),
						'separator' => '|',
						'filter' => 'int',
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
		
		if ( isset($requestData[$ins_id])) {
			if ($this->getOption(0) < 1 || $tiki_p_admin_trackers === 'y') {
				$data['value'] = $requestData[$ins_id];
			} else {
				if ($this->getOption(0) == 2) {
					$data['value'] = $user;
				} elseif ($this->getOption(0) == 1) {
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
		if ($value === false && ($this->getOption(0) == 1 || $this->getOption(0) == 2)) {
			$value = $user;
		}
		
		if ($this->getOption(0) == 0 || $tiki_p_admin_trackers === 'y') {
			$groupIds = '';
			if ($this->getOption(2)) {
				$groupIds = $this->getOption(2);
			}

			require_once $smarty->_get_plugin_filepath('function', 'user_selector');
			return smarty_function_user_selector(
					array(	'user' => $value,
							'id'  => 'user_selector_' . $this->getConfiguration('fieldId'),
							'select' => $value,
							'name' => $this->getInsertId(),
							'editable' => 'y',
							'allowNone' => $this->getConfiguration('isMandatory') === 'y' ? 'n' : 'y',
							'groupIds' => $groupIds,
					), $smarty);
		} else {
			require_once $smarty->_get_plugin_filepath('modifier', 'username');
			return smarty_modifier_username( $value ) . '<input type="hidden" name="' . $this->getInsertId() . '" value="' . $value . '">';
		}
	}

	function renderInnerOutput($context = array())
	{
		$value = $this->getConfiguration('value');
		if (empty($value)) {
			return '';
		} else {
			require_once TikiLib::lib('smarty')->_get_plugin_filepath('modifier', 'username');
			return smarty_modifier_username( $value );
		}
	}

}

