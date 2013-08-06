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

	function action_delete(JitRequest $request)
	{
		$id = $request->ruleId->int();
		$rule = $this->getRule($id);

		$removed = false;
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$this->lib->deleteRule($id);
			$removed = true;
		}

		return array(
			'removed' => $removed,
			'rule' => $rule,
			'eventTypes' => $this->getEventTypes(),
		);
	}

	function action_sample(JitFilter $request)
	{
		$id = $request->ruleId->int();

		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$event = $request->event->attribute_type();
			$id = $this->lib->replaceRule($id, array(
				'rule' => "(event-sample (str $event) event args)",
				'ruleType' => 'sample',
				'notes' => $request->notes->text(),
				'eventType' => $event,
			));
		}

		$rule = $this->getRule($id);
		return array(
			'data' => $this->lib->getSample($rule['eventType']),
			'rule' => $rule,
			'eventTypes' => $this->getEventTypes(),
		);
	}
	
	function action_record(JitFilter $request)
	{
		$id = $request->ruleId->int();

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
	
	function action_tracker_filter(JitFilter $request)
	{
		$id = $request->ruleId->int();

		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$tracker = $request->tracker->int();
			$targetEvent = $request->targetEvent->attribute_type();
			$customArguments = $request->parameters->text();

			$id = $this->lib->replaceRule($id, array(
				'rule' => "
(if (equals args.trackerId $tracker) (event-trigger $targetEvent (map
$customArguments
)))
",
				'ruleType' => 'tracker_filter',
				'notes' => $request->notes->text(),
				'eventType' => $request->sourceEvent->attribute_type(),
			));
		}

		$rule = $this->getRule($id);
		$root = $rule['element'];
		$parameters = '';
		$targetTracker = null;
		$targetEvent = null;

		if ($root) {
			$targetTracker = (int) $root->equals[1];
			$targetEvent = $root->{'event-trigger'}[0];
			foreach ($root->{'event-trigger'}->map as $element) {
				$parameters .= '(' . $element->getType() . ' ' . $element[0] . ')' . PHP_EOL;
			}
		} else {
			$parameters = "(user args.user)\n(type args.type)\n(object args.object)\n";
		}

		return array(
			'rule' => $rule,
			'eventTypes' => $this->getEventTypes(),
			'targetEvent' => $targetEvent,
			'targetTracker' => $targetTracker,
			'trackers' => TikiLib::lib('trk')->list_trackers(),
			'parameters' => $parameters,
		);
	}

	function action_advanced(JitFilter $request)
	{
		$id = $request->ruleId->int();

		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$id = $this->lib->replaceRule($id, array(
				'rule' => $request->rule->text(),
				'ruleType' => 'advanced',
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
			'sample' => tr('Sample Event'),
			'record' => tr('Record Event'),
			'tracker_filter' => tr('Tracker Filter'),
			'advanced' => tr('Advanced'),
		);
	}

	private function getEventTypes()
	{
		$graph = TikiLib::events()->getEventGraph();
		sort($graph['nodes']);
		return $graph['nodes'];
	}

	private function getRule($id)
	{
		if (! $rule = $this->lib->getRule($id)) {
			$rule = array(
				'ruleId' => null,
				'eventType' => '',
				'notes' => '',
				'rule' => '',
			);
		}

		if ($rule['rule']) {
			$parser = new Math_Formula_Parser;
			$rule['element'] = $parser->parse($rule['rule']);
		} else {
			$rule['element'] = null;
		}

		return $rule;
	}
}

