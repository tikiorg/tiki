<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class MonitorLib
{
	private $queue = [];

	function getPriorities()
	{
		return array(
			'none' => ['label' => '', 'description' => null],
			'critical' => ['label' => tr('Critical'), 'description' => tr('Immediate notification by email.')],
			'high' => ['label' => tr('High'), 'description' => tr('Sent to you with the next periodic digest.')],
			'low' => ['label' => tr('Low'), 'description' => tr('Included in your personalized recent changes feed.')],
		);
	}

	function getOptions($user, $type, $object)
	{
		global $prefs;

		$tikilib = TikiLib::lib('tiki');
		$userId = $tikilib->get_user_id($user);

		$events = $this->getApplicableEvents($type);
		$options = [];

		$options[] = $this->gatherOptions($userId, $events, $type, $object);

		if ($prefs['feature_categories'] == 'y') {
			$categlib = TikiLib::lib('categ');
			$categories = $categlib->get_object_categories($type, $object);
			$parents = $categlib->get_with_parents($categories);

			foreach ($parents as $categoryId) {
				$perms = Perms::get('category', $categoryId);
				if ($perms->view_category) {
					$options[] = array_map(function ($item) use ($categories) {
						$item['isParent'] = ! in_array($item['object'], $categories);
						return $item;
					}, $this->gatherOptions($userId, $events, 'category', $categoryId));
				}
			}
		}

		unset($events['tiki.save']); // Disallow global watch-all
		$options[] = $this->gatherOptions($userId, $events, 'global', null);

		return call_user_func_array('array_merge', $options);
	}

	function replacePriority($user, $event, $target, $priority)
	{
		$tikilib = TikiLib::lib('tiki');
		$userId = $tikilib->get_user_id($user);

		if ($userId === -1 || ! $userId) {
			return false;
		}

		$priorities = $this->getPriorities();
		if (! isset($priorities[$priority])) {
			return false;
		}

		$table = $this->table();

		$base = ['userId' => $userId, 'target' => $target, 'event' => $event];

		if ($priority === 'none') {
			$table->delete($base);
		} else {
			$table->insertOrUpdate(['priority' => $priority], $base);
		}

		return true;
	}

	/**
	 * Bind all events required to process notifications.
	 * One event is bound per active event type to collect the
	 * notifications to be sent out. A final event is sent out on
	 * shutdown to process the queued notifications.
	 */
	function bindEvents(Tiki_Event_Manager $events)
	{
		$events->bind('tiki.process.shutdown', function () {
			$this->finalEvent();
		});

		$db = TikiDb::get();
		foreach ($db->fetchAll('SELECT DISTINCT event FROM tiki_user_monitors') as $row) {
			$event = $row['event'];
			$events->bind($event, function ($args, $originalEvent) use ($event) {
				$this->handleEvent($args, $originalEvent, $event);
			});
		}
	}

	private function handleEvent($args, $originalEvent, $registeredEvent)
	{
		if (! isset($args['type']) || ! isset($args['object'])) {
			return;
		}

		$eventId = $args['EVENT_ID'];

		// Handle newly encountered events
		if (! isset($this->queue[$eventId])) {
			$this->queue[$eventId] = [
				'event' => $originalEvent,
				'arguments' => $args,
				'events' => [],
			];
		}

		$this->queue[$eventId]['events'][] = $registeredEvent;
	}

	private function finalEvent()
	{
		$queue = $this->queue;
		$this->queue = [];

		$activitylib = TikiLib::lib('activity');

		$tx = TikiDb::get()->begin();

		// TODO : Implement sendTo
		// TODO : Shrink large events / truncate content ? 

		$mailQueue = [];

		foreach ($queue as $item) {
			list($args, $sendTo) = $this->finalHandleEvent($item['arguments'], $item['events']);

			if ($args) {
				$activitylib->recordEvent($item['event'], $args);
			}

			if (! empty($sendTo)) {
				$recipients = $this->getRecipients($sendTo);

				foreach ($recipients as $recipient) {
					$key = "{$args['EVENT_ID']}-{$recipient['language']}";

					if (! isset($mailQueue[$key])) {
						$mailQueue[$key] = ['language' => $recipient['language'], 'event' => $item['event'], 'args' => $args, 'emails' => []];
					}

					$mailQueue[$key]['emails'][] = $recipient['email'];
				}
			}
		}

		$tx->commit();

		foreach ($mailQueue as $mail) {
			$title = $this->renderTitle($mail);
			$content = $this->renderContent($mail);
			foreach ($mail['emails'] as $email) {
				$this->sendMail($email, $title, $content);
			}
		}
	}
	
	private function finalHandleEvent($args, $events)
	{
		$targets = $this->collectTargets($args);

		$table = $this->table();
		$results = $table->fetchAll(['priority', 'userId'], [
			'event' => $table->in($events),
			'target' => $table->in($targets),
		]);

		if (empty($results)) {
			return [null, []];
		}

		$sendTo = [];
		$args['stream'] = isset($args['stream']) ? (array) $args['stream'] : [];

		foreach ($results as $row) {
			// Add entries to the named streams, each user will have a few of those
			$priority = $row['priority'];
			$args['stream'][] = $priority . $row['userId'];

			if ($priority == 'critical') {
				$sendTo[] = $row['userId'];
			}
		}

		return [$args, array_unique($sendTo)];
	}

	private function gatherOptions($userId, $events, $type, $object)
	{
		if ($object) {
			$objectInfo = $this->getObjectInfo($type, $object);
		} else {
			$objectInfo = array(
				'type' => 'global',
				'target' => 'global',
				'title' => tr('Anywhere'),
				'isContainer' => true,
			);
		}

		$options = [];

		$isContainer = $objectInfo['isContainer'];
		foreach ($events as $eventName => $info) {
			if ($isContainer || ! $info['global']) {
				$options[] = $this->createOption($userId, $eventName, $info['label'], $objectInfo);
			}
		}

		return $options;
	}

	private function getObjectInfo($type, $object)
	{
		$objectlib = TikiLib::lib('object');

		$title = $objectlib->get_title($type, $object);

		list($type, $object) = $this->cleanObjectId($type, $object);

		return array(
			'type' => $type,
			'object' => $object,
			'target' => "$type:$object",
			'title' => $title,
			'isContainer' => $type == 'category',
		);
	}

	private function cleanObjectId($type, $object)
	{
		// Hash must be short, so never use page names or such, use IDs
		if ($type == 'wiki page') {
			$tikilib = TikiLib::lib('tiki');
			$object = $tikilib->get_page_id_from_name($object);
		}

		return [$type, (int) $object];
	}

	private function createOption($userId, $eventName, $label, $objectInfo)
	{
		$conditions = ['userId' => $userId, 'event' => $eventName, 'target' => $objectInfo['target']];
		$active = $this->table()->fetchOne('priority', $conditions);

		return array(
			'priority' => $active ?: 'none',
			'event' => $eventName,
			'target' => $objectInfo['target'],
			'hash' => md5($eventName . $objectInfo['target']),
			'type' => $objectInfo['type'],
			'object' => $objectInfo['object'],
			'description' => $objectInfo['isContainer']
				? tr('%0 in %1', $label, $objectInfo['title'])
				: tr('%0 for %1', $label, $objectInfo['title']),
		);
	}

	private function getApplicableEvents($type)
	{
		switch ($type) {
		case 'wiki page':
			return [
				'tiki.save' => ['global' => false, 'label' => tr('Any activity')],
				'tiki.wiki.save' => ['global' => true, 'label' => tr('Page modified')],
				'tiki.wiki.create' => ['global' => true, 'label' => tr('Page created')],
			];
		default:
			return [];
		}
	}

	private function collectTargets($args)
	{
		global $prefs;

		$type = $args['type'];
		$object = $args['object'];

		if ($prefs['feature_categories'] == 'y') {
			$categlib = TikiLib::lib('categ');
			$categories = $categlib->get_object_categories($type, $object);
			$categories = $categlib->get_with_parents($categories);
			$targets = array_map(function ($categoryId) {
				return "category:$categoryId";
			}, $categories);
		}
		
		list($type, $object) = $this->cleanObjectId($type, $object);
		$targets[] = 'global';
		$targets[] = "$type:$object";

		return $targets;
	}

	private function table()
	{
		return TikiDb::get()->table('tiki_user_monitors');
	}

	private function getRecipients($sendTo)
	{
		global $prefs;
		$db = TikiDb::get();
		$bindvars = [$prefs['site_language']];
		$condition = $db->in('userId', $sendTo, $bindvars);

		$result = $db->fetchAll("
			SELECT email, IFNULL(p.value, ?) language
			FROM users_users u
				LEFT JOIN tiki_user_preferences p ON u.login = p.user AND p.prefName = 'language'
			WHERE $condition
		", $bindvars);

		return $result;
	}

	private function renderTitle($mail)
	{
		// FIXME : Needs a better title
		return tra('Notification', $mail['language']);
	}

	private function renderContent($mail)
	{
		$smarty = TikiLib::lib('smarty');
		$activity = $mail['args'];
		$activity['event_type'] = $mail['event'];
		$smarty->assign('monitor', $activity);
		TikiLib::setExternalContext(true);
		$html = $smarty->fetchLang($mail['language'], 'monitor/notification_email_body.tpl');
		TikiLib::setExternalContext(false);
		$css = $this->collectCss();

		$processor = new \TijsVerkoyen\CssToInlineStyles\CssToInlineStyles($html, $css);

		$html = $processor->convert();
		return $html;
	}

	private function collectCss()
	{
		static $css;
		if ($css) {
			return $css;
		}

		$cachelib = TikiLib::lib('cache');
		if ($css = $cachelib->getCached('email_css')) {
			return $css;
		}

		$headerlib = TikiLib::lib('header');
		$files = $headerlib->get_css_files();
		$contents = array_map(function ($file) {
			if ($file{0} == '/') {
				return file_get_contents($file);
			} elseif (substr($file, 0, 4) == 'http') {
				return TikiLib::lib('tiki')->httprequest($file);
			} else {
				return file_get_contents(TIKI_PATH . '/' . $file);
			}
		}, $files);

		$css = implode("\n\n", $contents);
		$cachelib->cacheItem('email_css', $css);
		return $css;
	}

	private function sendMail($email, $title, $html)
	{
		require_once 'lib/webmail/tikimaillib.php';
		$mail = new TikiMail;
		$mail->setSubject($title);
		$mail->setHtml($html);
		$mail->send($email);
	}
}

