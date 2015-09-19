<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
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
class Tracker_Field_UserSelector extends Tracker_Field_Abstract implements Tracker_Field_Synchronizable, Tracker_Field_Exportable
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
					'canChangeGroupIds' => array(
						'name' => tr('Groups that can modify autoassigned values'),
						'description' => tr('List of group IDs who can change this field, even without tracker_admin permission.'),
						'separator' => '|',
						'filter' => 'int',
					),
					'showRealname' => array(
						'name' => tr('Show real name if possible'),
						'description' => tr('Show real name if possible'),
						'filter' => 'int',
						'options' => array(
							0 => tr('No'),
							1 => tr('Yes'),
						),
						'default' => 0,
					),
				),
			),
		);
	}

	function getFieldData(array $requestData = array())
	{
		global $user, $prefs;

		$ins_id = $this->getInsertId();

		$data = array();

		$autoassign = (int) $this->getOption('autoassign');

		if ( isset($requestData[$ins_id])) {
			if ($autoassign == 0 || $this->canChangeValue()) {
				$auser = $requestData[$ins_id];
				$userlib = TikiLib::lib('user');
				if (! $auser || $userlib->user_exists($auser)) {
					$data['value'] = $auser;
				} else {
					if ($prefs['user_selector_realnames_tracker'] == 'y' && $this->getOption('showRealname')) {
						$finalusers = $userlib->find_best_user(array($auser), '', 'login');
						if (!empty($finalusers[0])) {
							$data['value'] = $finalusers[0];
						}
					}
					if (empty($data['value'])) {
						$data['value'] = $this->getValue();
						TikiLib::lib('errorreport')->report(tr('User "%0" not found', $auser));
					}
				}
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
		} else {
			$data['value'] = $this->getValue(false);
		}

		return $data;
	}

	function renderInput($context = array())
	{
		global $user, $prefs;
		$smarty = TikiLib::lib('smarty');

		$value = $this->getConfiguration('value');
		$autoassign = (int) $this->getOption('autoassign');
		if ((empty($value) && $autoassign == 1) || $autoassign == 2) {	// always use $user for last mod autoassign
			$value = $user;
		}
		if ($autoassign == 0 || $this->canChangeValue()) {
			$groupIds = $this->getOption('groupIds', '');

			if ($prefs['user_selector_realnames_tracker'] === 'y' && $this->getOption('showRealname')) {
				$smarty->loadPlugin('smarty_modifier_username');
				$name = smarty_modifier_username($value);
				$realnames = 'y';
			} else {
				$name = $value;
				$realnames = 'n';
			}

			$smarty->loadPlugin('smarty_function_user_selector');
			return smarty_function_user_selector(
				array(
					'user' => $name,
					'id'  => 'user_selector_' . $this->getConfiguration('fieldId'),
					'select' => $value,
					'name' => $this->getConfiguration('ins_id'),
					'editable' => 'y',
					'allowNone' => 'y',
					'groupIds' => $groupIds,
					'realnames' => $realnames,
				),
				$smarty
			);
		} else {
			if ($this->getOption('showRealname')) {
				$smarty->loadPlugin('smarty_modifier_username');
				$out = smarty_modifier_username($value);
			} else {
				$out = $value; 
			}	
			return $out . '<input type="hidden" name="' . $this->getInsertId() . '" value="' . $value . '">';
		}
	}

	function renderInnerOutput($context = array())
	{
		$value = $this->getConfiguration('value');
		if (empty($value)) {
			return '';
		} else {
			if ($this->getOption('showRealname')) {
				TikiLib::lib('smarty')->loadPlugin('smarty_modifier_username');
				return smarty_modifier_username($value);
			} else {
				return $value;
			}
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

	/**
	 * called from action_clone_item - sets to current user if autoassign == 1 or 2 (Creator or Modifier)
	 */
	function handleClone()
	{
		global $user;

		$value =  $this->getValue('');
		$autoassign = (int) $this->getOption('autoassign');

		if ($autoassign === 1 || $autoassign === 2) {
			$value = $user;
		}

		return array(
			'value' => $value,
		);

	}

	function getTabularSchema()
	{
		$permName = $this->getConfiguration('permName');
		$baseKey = $this->getBaseKey();
		$name = $this->getConfiguration('name');

		$schema = new Tracker\Tabular\Schema($this->getTrackerDefinition());

		$schema->addNew($permName, 'userlink')
			->setLabel($name)
			->setPlainReplacement('username')
			->setRenderTransform(function ($value) {
				$smarty = TikiLib::lib('smarty');
				$smarty->loadPlugin('smarty_modifier_userlink');

				if ($value) {
					return smarty_modifier_userlink($value);
				}
			})
			;

		$schema->addNew($permName, 'realname')
			->setLabel($name)
			->setReadOnly(true)
			->setRenderTransform(function ($value) {
				$smarty = TikiLib::lib('smarty');
				$smarty->loadPlugin('smarty_modifier_username');

				if ($value) {
					return smarty_modifier_username($value, true, false, false);
				}
			})
			;

		$schema->addNew($permName, 'username-itemlink')
			->setLabel($name)
			->setPlainReplacement('username')
			->addQuerySource('itemId', 'object_id')
			->setRenderTransform(function ($value, $extra) {
				$smarty = TikiLib::lib('smarty');
				$smarty->loadPlugin('smarty_function_object_link');

				if ($value) {
					return smarty_function_object_link([
						'type' => 'trackeritem',
						'id' => $extra['itemId'],
						'title' => $value,
					], $smarty);
				}
			})
			;

		$schema->addNew($permName, 'realname-itemlink')
			->setLabel($name)
			->setPlainReplacement('realname')
			->addQuerySource('itemId', 'object_id')
			->setRenderTransform(function ($value, $extra) {
				$smarty = TikiLib::lib('smarty');
				$smarty->loadPlugin('smarty_function_object_link');
				$smarty->loadPlugin('smarty_modifier_username');

				if ($value) {
					return smarty_function_object_link([
						'type' => 'trackeritem',
						'id' => $extra['itemId'],
						'title' => smarty_modifier_username($value, true, false, false),
					], $smarty);
				}
			})
			;

		$schema->addNew($permName, 'username')
			->setLabel($name)
			->setRenderTransform(function ($value) {
				return $value;
			})
			->setParseIntoTransform(function (& $info, $value) use ($permName) {
				$info['fields'][$permName] = $value;
			})
			;

		return $schema;
	}

	/** Checks if the current user can modify the value even if autoassigned usually
	 *
	 * @return boolean
	 */
	private function canChangeValue()
	{
		$groupsCanChangeValue = $this->getOption('canChangeGroupIds');
		if ($groupsCanChangeValue) {
			global $user;

			foreach ($groupsCanChangeValue as $groupId) {
				$groupName = TikiDb::get()->table('users_groups')->fetchOne('groupName', ['id' => $groupId]);
				if ($groupName && TikiLib::lib('user')->user_is_in_group($user, $groupName)) {
					return true;
				}
			}
		}
		$perms = Perms::get('tracker', $this->getConfiguration('trackerId'));

		return $perms->admin_trackers;
	}
}

