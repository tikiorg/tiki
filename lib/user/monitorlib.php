<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class MonitorLib
{
	private $queue = [];

	/**
	 * Provides the list of priorities available for notifications.
	 */
	function getPriorities()
	{
		return array(
			'none' => ['label' => '', 'description' => null],
			'critical' => ['label' => tr('Critical'), 'description' => tr('Immediate notification by email.')],
			'high' => ['label' => tr('High'), 'description' => tr('Sent to you with the next periodic digest.')],
			'low' => ['label' => tr('Low'), 'description' => tr('Included in your personalized recent changes feed.')],
		);
	}

	/**
	 * Provides the complete list of notifications that can affect a
	 * specific object in the system, including all of it's supported
	 * structures, like translation sets.
	 *
	 * @param user login name
	 * @param type standard object type
	 * @param object full itemId
	 */
	function getOptions($user, $type, $object)
	{
		global $prefs;

		$tikilib = TikiLib::lib('tiki');
		$userId = $tikilib->get_user_id($user);

		// Events applicable for this object
		$events = $this->getApplicableEvents($type);
		$options = [];

		// Include object directly
		$options[] = $this->gatherOptions($userId, $events, $type, $object);
		
		// Include translation set
		if ($this->hasMultilingual($type)) {
			// Using fake types - wiki page -> wiki page trans
			//                    article   -> article trans
			$options[] = $this->gatherOptions($userId, $events, "$type trans", $object);
		}

		// Include any category and parent category
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

		// Global / Catch-all always applicable, except for tiki.save, which would
		// cause too much noise.
		unset($events['tiki.save']); // Disallow global watch-all
		$options[] = $this->gatherOptions($userId, $events, 'global', null);

		return call_user_func_array('array_merge', $options);
	}

	/**
	 * Replaces the current priority for an event/target pair, for a specific user.
	 */
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
		$currentUser = TikiLib::lib('login')->getUserId();

		$targets = $this->collectTargets($args);

		$table = $this->table();
		$results = $table->fetchAll(['priority', 'userId'], [
			'event' => $table->in($events),
			'target' => $table->in($targets),
			'userId' => $table->not($currentUser),
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

	/**
	 * Create an option set for each event in the list.
	 * Collects the appropriate object information for adequate display.
	 */
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

		list($realType, $objectId) = $this->cleanObjectId($type, $object);

		$title = $objectlib->get_title($realType, $object);

		$target = "$type:$objectId";

		// For multilingual targets, collect all targets in the set as the event
		// is bound for a single page, but needs to be displayed for all other pages
		// as well to explain why the notification occurs.
		if (substr($type, -6) == ' trans') {
			$title = tr('translations of %0', $title);
			$fetchTargets = $this->getMultilingualTargets($realType, $objectId);
			$isTranslation = true;
		} else {
			$fetchTargets = [];
			$isTranslation = false;
		}

		$fetchTargets[] = $target;

		return array(
			'type' => $type,
			'object' => $objectId,
			'target' => $target,
			'title' => $title,
			'isContainer' => $isTranslation || $realType == 'category',
			'fetchTargets' => $fetchTargets,
		);
	}

	private function cleanObjectId($type, $object)
	{
		// Hash must be short, so never use page names or such, use IDs
		if ($type == 'wiki page' || $type == 'wiki page trans') {
			$tikilib = TikiLib::lib('tiki');
			$object = $tikilib->get_page_id_from_name($object);
		}

		if (substr($type, -6) == ' trans') {
			$type = substr($type, 0, -6);
		}

		return [$type, (int) $object];
	}

	private function createOption($userId, $eventName, $label, $objectInfo)
	{
		$table = $this->table();
		$conditions = [
			'userId' => $userId,
			'event' => $eventName,
			'target' => $table->in($objectInfo['fetchTargets']),
		];
		// Always fetch the oldest target possible, there would rarely be multiple
		// But a case where two translation sets would be join could have multiple
		// monitors active, only display the oldest one.
		$active = $table->fetchRow(['target', 'priority'], $conditions, [
			'monitorId' => 'ASC',
		]);

		// Because of the above rule, the active target may not be the requested one
		// Still display everything as it is the requested one
		$realTarget = $active ? $active['target'] : $objectInfo['target'];
		return array(
			'priority' => $active ? $active['priority'] : 'none',
			'event' => $eventName,
			'target' => $realTarget,
			'hash' => md5($eventName . $realTarget),
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
				'tiki.wiki.save' => ['global' => false, 'label' => tr('Page modified')],
				'tiki.wiki.create' => ['global' => true, 'label' => tr('Page created')],
			];
		default:
			return [];
		}
	}

	/**
	 * Method used to enumerate all targets being triggered by an event.
	 * Used to generate a single lookup query on event trigger.
	 */
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
		
		list($type, $objectId) = $this->cleanObjectId($type, $object);
		$targets[] = 'global';
		$targets[] = "$type:$objectId";

		if ($this->hasMultilingual($type)) {
			$targets = array_merge($targets, $this->getMultilingualTargets($type, $objectId));
		}

		return $targets;
	}

	private function table()
	{
		return TikiDb::get()->table('tiki_user_monitors');
	}

	/**
	 * Ontain the list of email addresses and preferred language for each
	 * user id to whom the notification email must be sent.
	 */
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

	/**
	 * Renders the body of the email and inline any applicable CSS.
	 */
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

	private function hasMultilingual($type)
	{
		global $prefs;
		return $prefs['feature_multilingual'] == 'y' && in_array($type, ['wiki page', 'article']);
	}

	private function getMultilingualTargets($type, $objectId)
	{
		$targets = [];
		$multilingual = TikiLib::lib('multilingual');
		foreach ($multilingual->getTrads($type, $objectId) as $row) {
			$targets[] = "$type trans:{$row['objId']}";
		}

		return $targets;
	}
}

