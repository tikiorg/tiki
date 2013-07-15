<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class ActivityLib
{
	private $mapping = false;

	function getRules()
	{
		return $this->rulesTable()->fetchAll(array(
			'ruleId',
			'eventType',
			'ruleType',
			'rule',
			'notes',
		), array());
	}

	function getRule($id)
	{
		return $this->rulesTable()->fetchRow(array(
			'ruleId',
			'eventType',
			'ruleType',
			'rule',
			'notes',
		), array(
			'ruleId' => $id,
		));
	}

	function replaceRule($id, array $data)
	{
		return $this->rulesTable()->insertOrUpdate($data, array(
			'ruleId' => $id,
		));
	}

	function recordEvent($event, $arguments)
	{
		$mapping = $this->getMapping();
		$unknown = array_diff_key($arguments, $mapping);

		if (count($unknown) > 0) {
			$this->guessMapping($unknown);
		}

		$id = $this->streamTable()->insert(array(
			'eventType' => $event,
			'eventDate' => TikiLib::lib('tiki')->now,
			'arguments' => json_encode($arguments),
		));

		TikiLib::lib('unifiedsearch')->invalidateObject('activity', $id);
	}

	function bindEvents(Tiki_Event_Manager $manager)
	{
		$self = $this;
		$runner = new Math_Formula_Runner(
			array(
				function ($verb) use ($manager, $self) {
					switch ($verb) {
					case 'event-trigger':
						return new Tiki_Event_Function_EventTrigger($manager);
					case 'event-record':
						return new Tiki_Event_Function_EventRecord($self);
					}
				},
				'Math_Formula_Function_' => '',
				'Tiki_Event_Function_' => '',
			)
		);

		$customizer = new Tiki_Event_Customizer;

		foreach ($this->getRules() as $rule) {
			$customizer->addRule($rule['eventType'], $rule['rule']);
		}
		
		$customizer->bind($manager, $runner);
	}

	function getActivityList()
	{
		return $this->streamTable()->fetchColumn('activityId', array());
	}

	function getActivity($id, $typeFactory)
	{
		$info = $this->streamTable()->fetchFullRow(array(
			'activityId' => $id,
		));

		if ($info) {
			$info['arguments'] = json_decode($info['arguments'], true);

			return $info;
		}
	}

	function getMapping()
	{
		if ($this->mapping === false) {
			$table = $this->mappingTable();
			$this->mapping = $table->fetchMap('field_name', 'field_type', array());
		}

		return $this->mapping;
	}

	private function guessMapping($arguments)
	{
		$mapper = new Search_Type_Analyzer;
		$mappingTable = $this->mappingTable();
		
		foreach ($arguments as $key => $value) {
			$type = $mapper->findType($key, $value);
			$mappingTable->insert(array(
				'field_name' => $key,
				'field_type' => $type,
			));
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

