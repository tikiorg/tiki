<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * Handler class for InGroup
 *
 * Letter key: ~N~
 *
 */
class Tracker_Field_InGroup extends Tracker_Field_Abstract
{
	public static function getTypes()
	{
		return [
			'N' => [
				'name' => tr('In Group'),
				'description' => tr('Indicates if the user associated with the item is member of a specified group.'),
				'readonly' => true,
				'help' => 'In Group Field',
				'prefs' => ['trackerfield_ingroup'],
				'tags' => ['advanced'],
				'default' => 'n',
				'params' => [
					'groupName' => [
						'name' => tr('Group Name'),
						'description' => tr('Name of the group to verify'),
						'filter' => 'groupname',
						'legacy_index' => 0,
					],
					'type' => [
						'name' => tr('Display'),
						'description' => tr('How to display the result'),
						'filter' => 'alpha',
						'options' => [
							'' => tr('Yes/No'),
							'date' => tr('Join date'),
							'expire' => tr('Expiration date')
						],
						'legacy_index' => 1,
					],
				],
			],
		];
	}

	function getFieldData(array $requestData = [])
	{
		return [];
	}

	function renderInput($context = [])
	{
		$this->renderOutput($context);	// read only
	}

	function renderOutput($context = [])
	{
		$trklib = TikiLib::lib('trk');
		$itemUsers = $trklib->get_item_creators($this->getConfiguration('trackerId'), $this->getItemId());

		if (! empty($itemUsers)) {
			if (! isset($trklib->tracker_infocache['users_group'][$this->getOption('groupName')])) {
				$userlib = TikiLib::lib('user');
				$trklib->tracker_infocache['users_group'][$this->getOption('groupName')] = $userlib->get_users_created_group($this->getOption('groupName'), null, true);
			}
			foreach ($itemUsers as $itemUser) {
				if (isset($trklib->tracker_infocache['users_group'][$this->getOption('groupName')][$itemUser])) {
					if ($this->getOption('type') == 'date') {
						$value = $trklib->tracker_infocache['users_group'][$this->getOption('groupName')][$itemUser]['created'];
					} elseif ($this->getOption('type') == 'expire') {
						$value = $trklib->tracker_infocache['users_group'][$this->getOption('groupName')][$itemUser]['expire'];
					} else {
						$value = 'Yes';
					}
				} else {
					if ($this->getOption('type') == 'date' || $this->getOption('type') == 'expire') {
						$value = '';
					} else {
						$value = 'No';
					}
				}
				if (! empty($value) && $value != 'No') {
					break;
				}
			}
		}

		if ($this->getOption('type') === 'date' || $this->getOption('type') == 'expire') {
			if (! empty($value)) {
				return TikiLib::lib('tiki')->get_short_date($value);
			}
		} else {
			return tra($value);
		}
	}
}
