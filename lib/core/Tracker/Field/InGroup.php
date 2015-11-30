<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
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
		return array(
			'N' => array(
				'name' => tr('In Group'),
				'description' => tr('Indicates if the user associated with the item is member of a specified group.'),
				'readonly' => true,
				'help' => 'In Group Field',				
				'prefs' => array('trackerfield_ingroup'),
				'tags' => array('advanced'),
				'default' => 'n',
				'params' => array(
					'groupName' => array(
						'name' => tr('Group Name'),
						'description' => tr('Name of the group to verify'),
						'filter' => 'groupname',
						'legacy_index' => 0,
					),
					'type' => array(
						'name' => tr('Display'),
						'description' => tr('How to display the result'),
						'filter' => 'alpha',
						'options' => array(
							'' => tr('Yes/No'),
							'date' => tr('Join date'),
							'expire'=>tr('Expiration date')
						),
						'legacy_index' => 1,
					),
				),
			),
		);
	}

	function getFieldData(array $requestData = array())
	{
		return array();
	}

	function renderInput($context = array())
	{
		$this->renderOutput($context);	// read only
	}
	
	function renderOutput($context = array())
	{
		$trklib = TikiLib::lib('trk');
		$itemUser = $trklib->get_item_creator($this->getConfiguration('trackerId'), $this->getItemId());
		
		if (!empty($itemUser)) {
			if (!isset($trklib->tracker_infocache['users_group'][$this->getOption('groupName')])) {
				$userlib = TikiLib::lib('user');
				$trklib->tracker_infocache['users_group'][$this->getOption('groupName')] = $userlib->get_users_created_group($this->getOption('groupName'), null, true);
			}
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
		}
		
		if ($this->getOption('type') === 'date' || $this->getOption('type') == 'expire') {
			if (!empty($value)) {
				return TikiLib::lib('tiki')->get_short_date($value);
			}
		} else {
			return tra($value);
		}
	}
}

