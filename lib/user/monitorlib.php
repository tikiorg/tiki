<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
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
		static $priorities;
		if ($priorities) {
			return $priorities;
		}

		$priorities = array(
			'none' => ['label' => '', 'description' => null],
			'critical' => ['label' => tr('Critical'), 'description' => tr('Immediate notification by email.'), 'class' => 'label-danger'],
			'high' => ['label' => tr('High'), 'description' => tr('Will be sent to you with the next periodic digest.'), 'class' => 'label-warning'],
			'low' => ['label' => tr('Low'), 'description' => tr('Included in your personalized recent changes feed.'), 'class' => 'label-info'],
		);

		global $prefs;
		if ($prefs['monitor_digest'] != 'y') {
			unset($priorities['high']);
		}

		return $priorities;
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

		if ($prefs['feature_wiki_structure'] == 'y' && $type == 'wiki page') {
			$structlib = TikiLib::lib('struct');
			$structures = $structlib->get_page_structures($object);
			foreach ($structures as $row) {
				$path = $structlib->get_structure_path($row['req_page_ref_id']);
				$path = array_reverse($path);
				foreach ($path as $level => $entry) {
					$options[] = $this->gatherOptions($userId, $events, 'structure', $entry['page_ref_id'], $this->getStructureLabel($level, $entry));
				}
			}
		}

		if ($prefs['feature_forums'] == 'y' && $type == 'forum post') {
			$post = TikiLib::lib('comments')->get_comment($object);
			$options[] = $this->gatherOptions($userId, $events, 'forum', $post['object']);
		}

		if ($prefs['feature_trackers'] == 'y' && $type == 'trackeritem') {
			$item = TikiLib::lib('trk')->get_item_info($object);
			$options[] = $this->gatherOptions($userId, $events, 'tracker', $item['trackerId']);
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
		$events = array_filter($events, function ($e) {
			return ! $e['local'];
		});
		$options[] = $this->gatherOptions($userId, $events, 'global', null);

		return call_user_func_array('array_merge', $options);
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

		if ($prefs['feature_wiki_structure'] == 'y' && $type == 'wiki page') {
			$structlib = TikiLib::lib('struct');
			$structures = $structlib->get_page_structures($object);
			foreach ($structures as $row) {
				$path = $structlib->get_structure_path($row['req_page_ref_id']);
				foreach ($path as $entry) {
					$targets[] = "structure:{$entry['page_ref_id']}";
				}
			}
		}

		if ($prefs['feature_forums'] == 'y' && $type == 'forum post') {
			if (! empty($args['forum_id'])) {
				$targets[] = "forum:{$args['forum_id']}";
			}
			if (! empty($args['parent_id'])) {
				$targets[] = "forum post:{$args['parent_id']}";
			}
		}

		if ($prefs['feature_trackers'] == 'y' && $type == 'trackeritem') {
			if (! empty($args['trackerId'])) {
				$targets[] = "tracker:{$args['trackerId']}";
			}
		}

		return $targets;
	}

	private function table()
	{
		return TikiDb::get()->table('tiki_user_monitors');
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
		$list = $db->fetchAll('SELECT DISTINCT event FROM tiki_user_monitors', null, -1, -1, TikiDb::ERR_NONE);

		// Ignore errors to avoid locking out users
		if ($list) {
			foreach ($list as $row) {
				$event = $row['event'];
				$events->bind($event, function ($args, $originalEvent) use ($event) {
					$this->handleEvent($args, $originalEvent, $event);
				});
			}
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
				'force' => null,
			];
		}

		$this->queue[$eventId]['events'][] = $registeredEvent;
	}

	function directNotification($priority, $userId, $event, $args)
	{
		$this->queue[$args['EVENT_ID']] = [
			'event' => $event,
			'arguments' => $args,
			'events' => [],
			'force' => [
				'priority' => $priority,
				'userId' => $userId,
			],
		];
	}

	private function finalEvent()
	{
		$queue = $this->queue;
		$this->queue = [];

		$activitylib = TikiLib::lib('activity');

		$tx = TikiDb::get()->begin();

		// TODO : Shrink large events / truncate content ? 

		$mailQueue = [];

		$monitormail = TikiLib::lib('monitormail');
		foreach ($queue as $item) {
			list($args, $sendTo) = $this->finalHandleEvent($item['arguments'], $item['events'], $item['force']);

			if ($args) {
				$activitylib->recordEvent($item['event'], $args);
			}

			if (! empty($sendTo)) {
				$monitormail->queue($item['event'], $args, $sendTo);
			}

		}

		$tx->commit();

		// Send email (rather slow, dealing with external services) after Tiki's management is done
		$monitormail->sendQueue();
	}

	private function finalHandleEvent($args, $events, $force)
	{
		$currentUser = TikiLib::lib('login')->getUserId();

		if ($force) {
			if ($currentUser != $force['userId']) {
				// Direct notification, we know user and priority
				$results = [$force];
			}
		} else {
			$targets = $this->collectTargets($args);

			$table = $this->table();
			$results = $table->fetchAll(['priority', 'userId'], [
				'event' => $table->in($events),
				'target' => $table->in($targets),
				'userId' => $table->not($currentUser),
			]);
		}

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
	private function gatherOptions($userId, $events, $type, $object, $title = null)
	{
		if ($object) {
			$objectInfo = $this->getObjectInfo($type, $object, $title);
		} else {
			$objectInfo = array(
				'type' => 'global',
				'target' => 'global',
				'title' => tr('Anywhere'),
				'isContainer' => true,
				'fetchTargets' => ['global'],
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

	private function getObjectInfo($type, $object, $title)
	{
		$objectlib = TikiLib::lib('object');

		list($realType, $objectId) = $this->cleanObjectId($type, $object);

		$title = $title ?: $objectlib->get_title($realType, $object);

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
			'isContainer' => $isTranslation || in_array($realType, ['category', 'structure', 'forum', 'tracker']),
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

		if ($type == 'user') {
			$tikilib = TikiLib::lib('tiki');
			$object = $tikilib->get_user_id($object);
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
		/**
		 * Global indicates that the event cannot apply to a direct object
		 * Local indicates the event cannot apply on a global scale (to reduce noise)
		 */
		switch ($type) {
		case 'wiki page':
			return [
				'tiki.save' => ['global' => false, 'local' => true, 'label' => tr('Any activity')],
				'tiki.wiki.save' => ['global' => false, 'local' => false, 'label' => tr('Page modified')],
				'tiki.wiki.create' => ['global' => true, 'local' => false, 'label' => tr('Page created')],
			];
		case 'forum post':
			return [
				'tiki.save' => ['global' => false, 'local' => true, 'label' => tr('Any activity')],
				'tiki.forumpost.save' => ['global' => false, 'local' => false, 'label' => tr('Any forum activity')],
				'tiki.forumpost.create' => ['global' => true, 'local' => false, 'label' => tr('New topics')],
			];
		case 'trackeritem':
			return [
				'tiki.save' => ['global' => false, 'local' => true, 'label' => tr('Any activity')],
				'tiki.trackeritem.save' => ['global' => false, 'local' => false, 'label' => tr('Any item activity')],
				'tiki.trackeritem.create' => ['global' => true, 'local' => false, 'label' => tr('New items')],
			];
		case 'user':
			return [
				'tiki.mustread.required' => ['global' => false, 'local' => true, 'label' => tr('Action Required')],
				'tiki.recommendation.incoming' => ['global' => false, 'local' => true, 'label' => tr('Recommendation Received')],
			];
		default:
			return [];
		}
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

	private function getStructureLabel($level, $entry)
	{
		$page = $entry['pageName'];

		if ($entry['parent_id'] == 0) {
			return tr('%0 (%1 level up, entire structure)', $page, $level);
		} elseif ($level) {
			return tr('%0 (%1 level up)', $page, $level);
		} else {
			return tr('%0 (current subtree)', $page);
		}
	}
}

