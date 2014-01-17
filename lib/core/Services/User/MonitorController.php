<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Services_User_MonitorController
{
	function setUp()
	{
		Services_Exception_Disabled::check('monitor_enabled');
		if (! $GLOBALS['user']) {
			throw new Services_Exception_Denied(tr('Authentication required'));
		}
	}

	function action_object($input)
	{
		global $user;

		$type = $input->type->text();
		$object = $input->object->text();

		$objectlib = TikiLib::lib('object');
		$title = $objectlib->get_title($type, $object);

		$monitorlib = TikiLib::lib('monitor');

		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$options = $monitorlib->getOptions($user, $type, $object);
			foreach ($options as $option) {
				$key = $option['hash'];
				$selected = $input->notification->{$key}->word();

				if ($option['priority'] != $selected) {
					$monitorlib->replacePriority($user, $option['event'], $option['target'], $selected);
				}
			}
		}

		return array(
			'type' => $type,
			'object' => $object,
			'title' => tr('Notifications for %0', $title),
			'options' => $monitorlib->getOptions($user, $type, $object),
			'priorities' => $monitorlib->getPriorities(),
		);
	}
}

