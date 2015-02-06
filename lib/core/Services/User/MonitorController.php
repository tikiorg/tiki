<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Services_User_MonitorController
{
	function setUp()
	{
		Services_Exception_Disabled::check('monitor_enabled');
		Services_Exception_Denied::checkAuth();
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

		$query->setRange($input->offset->int());

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
		];
	}

	function action_unread($input)
	{
		global $user;
		$loginlib = TikiLib::lib('login');
		$servicelib = TikiLib::lib('service');
		$tikilib = TikiLib::lib('tiki');

		$lastread = $tikilib->get_user_preference($user, 'notification_read', 1388534400); // Jan 2014, as the feature did not exist prior to this date anyway

		$userId = $loginlib->getUserId();

		$searchlib = TikiLib::lib('unifiedsearch');
		$query = $searchlib->buildQuery([
			'type' => 'activity',
		]);
		$query->filterMultivalue("critical$userId OR high$userId OR low$userId", 'stream');
		$query->filterRange($lastread, 'now');
		$query->filterMultivalue("NOT \"$user\"", 'clear_list');
		$query->setOrder('modification_date_desc');

		if ($input->nodata->int()) {
			$query->setRange(0, 1);
		} else {
			$query->setRange(0, 7);
		}
		$result = $query->search($searchlib->getIndex());

		return [
			'title' => tr('Unread Notifications'),
			'count' => count($result),
			'result' => $result,
			'timestamp' => TikiLib::lib('tiki')->now,
			'more_link' => $servicelib->getUrl([
				'controller' => 'monitor',
				'action' => 'stream',
				'from' => '-30 days',
				'to' => 'now',
				'critical' => 1,
				'high' => 1,
				'low' => 1,
			]),
		];
	}

	function action_clearall($input)
	{
		global $user;

		$tikilib = TikiLib::lib('tiki');
		$timestamp = $input->timestamp->int();

		if ($_SERVER['REQUEST_METHOD'] == 'POST' && $timestamp) {
			$tikilib->set_user_preference($user, 'notification_read', $timestamp);
		}

		return [
			'title' => tr('Mark all notifications as read'),
			'timestamp' => $timestamp ?: $tikilib->now,
		];
	}

	function action_clearone($input)
	{
		Services_Exception_Disabled::check('monitor_individual_clear');

		global $user;
		$relationlib = TikiLib::lib('relation');
		$searchlib = TikiLib::lib('unifiedsearch');

		$activity = $input->activity->int();

		if ($_SERVER['REQUEST_METHOD'] == 'POST' && $activity) {
			$relationlib->add_relation('tiki.monitor.cleared', 'user', $user, 'activity', $activity);
			$searchlib->invalidateObject('activity', $activity);
			$searchlib->processUpdateQueue();
		}
	}
}

