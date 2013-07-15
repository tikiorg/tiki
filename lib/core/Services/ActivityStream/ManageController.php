<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Services_ActivityStream_ManageController
{
	private $lib;

	function setUp()
	{
		if (! Perms::get()->admin) {
			throw new Services_Exception(tr('Permission Denied'), 403);
		}

		$this->lib = TikiLib::lib('activity');
	}

	function action_list(JitFilter $request)
	{
		$rules = $this->lib->getRules();

		return array(
			'rules' => $rules,
			'ruleTypes' => $this->getRuleTypes(),
		);
	}

	function action_record(JitFilter $request)
	{
		$id = $request->id->int();

		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$id = $this->lib->replaceRule($id, array(
				'rule' => '(event-record event args)',
				'ruleType' => 'record',
				'notes' => $request->notes->text(),
				'eventType' => $request->event->attribute_type(),
			));
		}

		return array(
			'rule' => $this->getRule($id),
			'eventTypes' => $this->getEventTypes(),
		);
	}
	
	private function getRuleTypes()
	{
		return array(
			'record' => tr('Record Event'),
		);
	}

	private function getEventTypes()
	{
		$graph = TikiLib::events()->getEventGraph();
		return $graph['nodes'];
	}

	private function getRule($id)
	{
		if ($rule = $this->lib->getRule($id)) {
			return $rule;
		}

		return array(
			'ruleId' => null,
			'eventType' => '',
			'notes' => '',
			'rule' => '',
		);
	}
}

