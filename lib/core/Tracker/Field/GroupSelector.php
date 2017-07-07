<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * Handler class for GroupSelector
 * 
 * Letter key: ~g~
 *
 */
class Tracker_Field_GroupSelector extends Tracker_Field_Abstract implements Tracker_Field_Filterable
{
	public static function getTypes()
	{
		return array(
			'g' => array(
				'name' => tr('Group Selector'),
				'description' => tr('Allows a selection from a specified list of user groups.'),
				'help' => 'Group selector',				
				'prefs' => array('trackerfield_groupselector'),
				'tags' => array('advanced'),
				'default' => 'n',
				'params' => array(
					'autoassign' => array(
						'name' => tr('Auto-Assign'),
						'description' => tr('Determines if any group should be automatically assigned to the field.'),
						'filter' => 'int',
						'options' => array(
							0 => tr('None'),
							1 => tr('Creator'),
							2 => tr('Modifier'),
						),
						'legacy_index' => 0,
					),
					'groupId' => array(
						'name' => tr('Group Filter'),
						'description' => tr('Limit listed groups to those including the specified group.'),
						'filter' => 'int',
						'legacy_index' => 1,
					),
					'userGroups' => array(
						'name' => tr('User Groups'),
						'description' => tr('Show groups user belongs to instead of the ones user has permission to see.'),
						'filter' => 'int',
						'options' => array(
							0 => tr('No'),
							1 => tr('Yes'),
						),
						'default' => 0,
						'legacy_index' => 4,
					),
					'assign' => array(
						'name' => tr('Assign to the group'),
						'description' => tr('For no auto-assigned field, the user (user selector if it exists, or user) will be assigned to the group and it will be his or her default group. The group must have the user choice setting activated.'),
						'filter' => 'int',
						'options' => array(
							0 => tr('None'),
							1 => tr('Assign'),
						),
						'default' => 0,
						'legacy_index' => 2,
					),
					'notify' => array(
						'name' => tr('Email Notification'),
						'description' => tr('Add selected group to group monitor the item. Group watches feature must be enabled.'),
						'filter' => 'int',
						'options' => array(
							0 => tr('No'),
							1 => tr('Yes'),
						),
						'legacy_index' => 3,
					)
				),
			),
		);
	}

	function getFieldData(array $requestData = array())
	{
		global $tiki_p_admin_trackers, $group, $user;
		
		$ins_id = $this->getInsertId();

		$data = array();
		
		$groupId = $this->getOption('groupId');
		if (empty($groupId)) {
			if( $this->getOption('userGroups') ) {
				$data['list'] = array_keys(TikiLib::lib('user')->get_user_groups_inclusion($user));
				sort($data['list']);
			} else {
				$data['list'] = TikiLib::lib('user')->list_all_groups_with_permission();
			}
		} else {
			$group_info = TikiLib::lib('user')->get_groupId_info($groupId);
			$data['list'] =	TikiLib::lib('user')->get_including_groups($group_info['groupName']);
		}

		if ( isset($requestData[$ins_id])) {
			if ($this->getOption('autoassign') < 1 || $tiki_p_admin_trackers === 'y') {
				$data['value'] = in_array($requestData[$ins_id], $data['list'])? $requestData[$ins_id]: '';
			} else {
				if ($this->getOption('autoassign') == 2) {
					$data['defvalue'] = $group;
					$data['value'] = $group;
				} elseif ($this->getOption('autoassign') == 1) {
					$data['value'] = $group;
				} else {
					$data['value'] = '';
				}
			}
		} else {
			$data['defvalue'] = $group;
			$data['value'] = $this->getValue();		
		}
		
		return $data;
	}
	
	function renderInput($context = array())
	{
		return $this->renderTemplate('trackerinput/groupselector.tpl', $context);
	}

	function handleSave($value, $oldValue)
	{
		global $prefs, $user;

		if ($this->getOption('autoassign') && is_null($oldValue)) {
			$definition = $this->getTrackerDefinition();
			if ($prefs['groupTracker'] == 'y' && $definition->isEnabled('autoCreateGroup')) {
				$value = TikiLib::lib('trk')->groupName($definition->getInformation(), $this->getItemId());
			}
		}
		if ($this->getOption('assign')) {
			$creators = TikiLib::lib('trk')->get_item_creators($this->getConfiguration('trackerId'), $this->getItemId());
			if (empty($creators)) {
				$creators = array($user);
			}
			$ginfo = TikiLib::lib('user')->get_group_info($value);
			foreach( $creators as $creator ) {
				if ($ginfo['userChoice'] == 'y') {
					TikiLib::lib('user')->assign_user_to_group($creator, $value);
					TikiLib::lib('user')->set_default_group($creator, $value);
				}
			}
		}

		if( $this->getOption('notify') && $prefs['feature_group_watches'] == 'y' ) {
			$objectId = $this->getItemId();
			$watchEvent = 'tracker_item_modified';
			$objectType = 'tracker '.$this->getConfiguration('trackerId');

			$tikilib = TikiLib::lib('tiki');
			$old_watches = $tikilib->get_groups_watching($objectId, $watchEvent, $objectType);

			foreach( $old_watches as $key => $group ) {
				if( $group != $value ) {
					$tikilib->remove_group_watch($group, $watchEvent, $objectId, $objectType);
				}
			}

			if( !empty($value) && !in_array($value, $old_watches) ) {
				$trackerInfo = $this->getTrackerDefinition()->getInformation();
				$objectName = $trackerInfo['name'];
				$objectHref = 'tiki-view_tracker_item.php?trackerId='.$this->getConfiguration('trackerId').'&itemId='.$this->getItemId();
				$tikilib->add_group_watch($value, $watchEvent, $objectId, $objectType, $objectName, $objectHref);
			}
		}

		return array(
			'value' => $value,
		);
	}

	function getDocumentPart(Search_Type_Factory_Interface $typeFactory)
	{
		$baseKey = $this->getBaseKey();

		$value = $this->getValue();

		return array(
			$baseKey => $typeFactory->identifier($value),
			"{$baseKey}_text" => $typeFactory->sortable($value),
		);

	}

	function getFilterCollection()
	{
		$userlib = TikiLib::lib('user');
		$groups = $userlib->list_all_groups_with_permission();
		$groups = $userlib->get_group_info($groups);

		$possibilities = array();
		foreach ($groups as $group) {
			$possibilities[$group['groupName']] = $group['groupName'];
		}
		$possibilities['-Blank (no data)-'] = tr('-Blank (no data)-');

		$filters = new Tracker\Filter\Collection($this->getTrackerDefinition());
		$permName = $this->getConfiguration('permName');
		$name = $this->getConfiguration('name');
		$baseKey = $this->getBaseKey();

		$filters->addNew($permName, 'dropdown')
			->setLabel($name)
			->setControl(new Tracker\Filter\Control\DropDown("tf_{$permName}_dd", $possibilities))
			->setApplyCondition(function ($control, Search_Query $query) use ($baseKey) {
				$value = $control->getValue();

				if ($value === '-Blank (no data)-') {
					$query->filterIdentifier('', $baseKey.'_text');
				} elseif ($value) {
					$query->filterIdentifier($value, $baseKey);
				}
			});
		
		$filters->addNew($permName, 'multiselect')
			->setLabel($name)
			->setControl(new Tracker\Filter\Control\MultiSelect("tf_{$permName}_ms", $possibilities))
			->setApplyCondition(function ($control, Search_Query $query) use ($permName, $baseKey) {
				$values = $control->getValues();

				if (! empty($values)) {
					$sub = $query->getSubQuery("ms_$permName");

					foreach ($values as $v) {
						if ($v === '-Blank (no data)-') {
							$sub->filterIdentifier('', $baseKey.'_text');
						} elseif ($v) {
							$sub->filterIdentifier((string) $v, $baseKey);
						}
					}
				}
			});
		
		return $filters;
	}
}

