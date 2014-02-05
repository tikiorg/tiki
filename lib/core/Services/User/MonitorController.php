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

	function action_stream($input)
	{
		$loginlib = TikiLib::lib('login');

		$userId = $loginlib->getUserId();

		$critical = $input->critical->int();
		$high = $input->high->int();
		$low = $input->low->int();

		$quantity = $input->quantity->int();

		$from = $input->from->text();
		$to = $input->to->text();

		if (! $critical && ! $high && ! $low) {
			throw new Services_Exception_NotFound;
		}

		$searchlib = TikiLib::lib('unifiedsearch');
		$query = $searchlib->buildQuery([
			'type' => 'activity',
		]);
		$query->setOrder('modification_date_desc');

		$sub = $query->getSubQuery('optional');
		if ($critical) {
			$sub->filterMultivalue("critical$userId", "stream");
		}
		if ($high) {
			$sub->filterMultivalue("high$userId", "stream");
		}
		if ($low) {
			$sub->filterMultivalue("low$userId", "stream");
		}

		if ($from && $to) {
			$query->filterRange($from, $to);
		}

		if ($quantity) {
			$query->setRange(0, $quantity);
		} else {
			$query->setRange($input->offset->int());
		}

		$result = $query->search($searchlib->getIndex());

		if (! $result->count()) {
			throw new Services_Exception(tr('No notifications.'), 404);
		}

		// Hacking around the horrible code generating urls in pagination
		$_GET = [
			'critical' => $critical, 'high' => $high, 'low' => $low,
			'from' => $from, 'to' => $to,
		];
		$service = ['controller' => 'monitor', 'action' => 'stream'];

		global $prefs;
		$servicelib = TikiLib::lib('service');
		if ($prefs['feature_sefurl'] == 'y') {
			$_SERVER['PHP_SELF'] = $servicelib->getUrl($service);
		} else {
			$_GET += $service;
			$_SERVER['PHP_SELF'] = 'tiki-ajax_services.php';
		}

		return [
			'title' => tr('Notifications'),
			'result' => $result,
			'quantity' => $quantity,
			'more_link' => $servicelib->getUrl($_GET + $service),
		];
	}
}

