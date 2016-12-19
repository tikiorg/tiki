<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * Handler class for IP selector:
 * 
 * Letter key ~I~
 */
class Tracker_Field_Ip extends Tracker_Field_Abstract implements Tracker_Field_Synchronizable
{
	public static function getTypes()
	{
		return array(
			'I' => array(
				'name' => tr('IP Selector'),
				'description' => tr('IP address input field.'),
				'help' => 'IP selector',
				'prefs' => array('trackerfield_ipaddress'),
				'tags' => array('basic'),
				'default' => 'n',
				'params' => array(
					'autoassign' => array(
						'name' => tr('Auto-assign'),
						'description' => tr('Automatically assign the value on creation or edit.'),
						'filter' => 'int',
						'default' => 1,
						'options' => array(
							0 => tr('None'),
							1 => tr('Creator'),
							2 => tr('Modifier'),
						),
						'legacy_index' => 0,
					),
				),
			),
		);
	}
	
	function getFieldData(array $requestData = array())
	{
		global $tiki_p_admin_trackers;
		
		$ins_id = $this->getInsertId();
		$data = $this->getItemData();
		$autoAssign = $this->getOption('autoassign');
 
		if (empty($data) && $tiki_p_admin_trackers == 'n' && $autoAssign == '1') {
			// if it is a new tracker item, ip auto assign is enabled and user doesn't
			// have $tiki_p_admin_trackers there is no information about the ip address
			// in the form so we have to get it from TikiLib::get_ip_address()
			$value = TikiLib::lib('tiki')->get_ip_address();
		} else if (isset($requestData[$ins_id])) {
			$value = $requestData[$ins_id];
		} else {
			$value = $this->getValue();
		}

		return array(
			'value' => $value,
		);
	}
	
	function renderInput($context = array())
	{
		return $this->renderTemplate("trackerinput/ip.tpl", $context);
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
}

