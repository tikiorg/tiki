<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class ActivityLib
{
	private $mapping = false;

	function getRules()
	{
		$table = $this->rulesTable();
		$table->useExceptions();
		return $table->fetchAll(
			[
				'ruleId',
				'eventType',
				'ruleType',
				'rule',
				'notes',
			],
			[]
		);
	}

	function getRule($id)
	{
		return $this->rulesTable()->fetchRow(
			[
				'ruleId',
				'eventType',
				'ruleType',
				'rule',
				'notes',
			],
			[
				'ruleId' => $id,
			]
		);
	}

	/**
	 * @throws Math_Formula_Exception
	 */
	function replaceRule($id, array $data)
	{
		$testRunner = $this->getRunner(new Tiki_Event_Manager);
		$testRunner->setFormula($data['rule']);
		$testRunner->inspect();

		return $this->rulesTable()->insertOrUpdate(
			$data,
			[
				'ruleId' => $id,
			]
		);
	}

	function deleteRule($id)
	{
		return $this->rulesTable()->delete(
			[
				'ruleId' => $id,
			]
		);
	}

	function deleteActivity($id)
	{
		$info = $this->streamTable()->delete(
			[
				'activityId' => $id,
			]
		);
		require_once 'lib/search/refresh-functions.php';
		refresh_index('activity', $id);
	}

	function preserveRules(array $ids)
	{
		$table = $this->rulesTable();
		return $table->deleteMultiple(
			[
				'ruleId' => $table->notIn($ids),
			]
		);
	}

	function recordEvent($event, $arguments)
	{
		if (! $event) {
			return; // prevent false recording of test runs
		}

		$mapping = $this->getMapping();
		$unknown = array_diff_key($arguments, $mapping);

		if (count($unknown) > 0) {
			$this->guessMapping($unknown);
		}

		$id = $this->streamTable()->insert(
			[
				'eventType' => $event,
				'eventDate' => TikiLib::lib('tiki')->now,
				'arguments' => json_encode($arguments),
			]
		);

		TikiLib::lib('unifiedsearch')->invalidateObject('activity', $id);
	}

	/**
	 * Logs an event to the web server log via final tiki.eventlog.commit event.
	 * Needs to be activated via setting TIKI_HEADER_REPORT_EVENTS as a
	 * server environment variable
	 *
	 * @param $event
	 * @param $arguments
	 */
	function logEvent($event, $arguments, $includes = [], $excludes = [])
	{
		if (! $event) {
			return; // prevent false recording of test runs
		}

		if ($includes) {
			// if includes is provided, then everything is excluded by default
			$clean_args = [];
			foreach ($arguments as $k => $v) {
				if (in_array($k, $includes)) {
					$clean_args[$k] = $v;
				}
			}
		} elseif ($excludes) {
			$clean_args = [];
			foreach ($arguments as $k => $v) {
				if (! in_array($k, $excludes)) {
					$clean_args[$k] = $v;
				}
			}
		} else {
			$clean_args = $arguments;
		}

		$events = TikiLib::events();
		$events->logEvent($event, $clean_args);
		$events->trigger('tiki.eventlog.commit');
	}

	function bindBasicEvents(Tiki_Event_Manager $manager)
	{
		global $prefs;
		$map = [
			'activity_basic_tracker_create' => 'tiki.trackeritem.create',
			'activity_basic_tracker_update' => 'tiki.trackeritem.update',
			'activity_basic_user_follow_add' => 'tiki.user.follow.add',
			'activity_basic_user_follow_incoming' => 'tiki.user.follow.incoming',
			'activity_basic_user_friend_add' => 'tiki.user.friend.add',
		];

		foreach ($map as $preference => $event) {
			if ($prefs[$preference] == 'y') {
				$this->bindEventRecord($manager, $event);
			}
		}
	}

	private function bindEventRecord($manager, $event)
	{
		$self = $this;
		$manager->bind(
			$event,
			function ($args) use ($self, $event) {
				$self->recordEvent($event, $args);
			}
		);
	}

	function bindCustomEvents(Tiki_Event_Manager $manager)
	{
		$runner = $this->getRunner($manager);
		$customizer = new Tiki_Event_Customizer;

		try {
			foreach ($this->getRules() as $rule) {
				$customizer->addRule($rule['eventType'], $rule['rule']);
			}

			$customizer->bind($manager, $runner);
		} catch (TikiDb_Exception $e) {
			// Prevent failure while binding events to avoid locking out users
		}
	}

	private function getRunner($manager)
	{
		$self = $this;
		return new Math_Formula_Runner(
			[
				function ($verb) use ($manager, $self) {
					switch ($verb) {
						case 'event-trigger':
							return new Tiki_Event_Function_EventTrigger($manager);
						case 'event-record':
							return new Tiki_Event_Function_EventRecord($self);
						case 'event-notify':
							return new Tiki_Event_Function_EventNotify($self);
						case 'event-sample':
							return new Tiki_Event_Function_EventSample($self);
						case 'event-log':
							return new Tiki_Event_Function_EventLog($self);
					}
				},
				'Math_Formula_Function_' => '',
				'Tiki_Event_Function_' => '',
			]
		);
	}

	function getActivityList()
	{
		return $this->streamTable()->fetchColumn('activityId', []);
	}

	function getActivity($id)
	{
		$info = $this->streamTable()->fetchFullRow(
			[
				'activityId' => $id,
			]
		);

		if ($info) {
			$info['arguments'] = json_decode($info['arguments'], true);

			return $info;
		}
	}

	function getMapping()
	{
		if ($this->mapping === false) {
			$table = $this->mappingTable();
			$this->mapping = $table->fetchMap('field_name', 'field_type', []);
		}

		return $this->mapping;
	}

	function getSample($eventName)
	{
		$cachelib = TikiLib::lib('cache');
		return $cachelib->getCached($eventName, 'event_sample');
	}

	function setSample($eventName, array $data)
	{
		$data = $this->serializeData($data);
		$cachelib = TikiLib::lib('cache');
		$cachelib->cacheItem($eventName, $data, 'event_sample');
	}

	private function serializeData(array $data, $prefix = '')
	{
		$out = '';
		foreach ($data as $key => $value) {
			if (is_array($value)) {
				$out .= $this->serializeData($value, "$prefix$key.");
			} else {
				if (false !== strpos($value, "\n")) {
					$value = str_replace("\n", "\n  |", "\n" . $value);
				}
				$out .= "$prefix$key = $value\n";
			}
		}

		return $out;
	}

	private function guessMapping($arguments)
	{
		$this->getMapping(); // Ensure mapping is loaded.

		$mapper = new Search_Type_Analyzer;
		$mappingTable = $this->mappingTable();

		foreach ($arguments as $key => $value) {
			$type = $mapper->findType($key, $value);
			$mappingTable->insert(
				[
					'field_name' => $key,
					'field_type' => $type,
				]
			);
			$this->mapping[$key] = $type;
		}
	}

	private function rulesTable()
	{
		return TikiDb::get()->table('tiki_activity_stream_rules');
	}

	private function mappingTable()
	{
		return TikiDb::get()->table('tiki_activity_stream_mapping');
	}

	private function streamTable()
	{
		return TikiDb::get()->table('tiki_activity_stream');
	}
}
