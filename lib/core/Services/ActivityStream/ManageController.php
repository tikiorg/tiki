<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Services_ActivityStream_ManageController
{
	private $lib;
	
	/**
	 * Set up the controller
	 */
	function setUp()
	{
		if (! Perms::get()->admin) {
			throw new Services_Exception(tr('Permission Denied'), 403);
		}

		$this->lib = TikiLib::lib('activity');
	}
	
	/**
	 * List activity rules from tiki_activity_stream_rules table
	 */
	function action_list(JitFilter $request)
	{
		$rules = $this->lib->getRules();
		
		foreach($rules as &$rule){
			$status = $this->getRuleStatus($rule['ruleId']);
			$rule['status'] = $status;
		}

		return array(
			'rules' => $rules,
			'ruleTypes' => $this->getRuleTypes(),
			'event_graph' => TikiLib::events()->getEventGraph(),
		);
	}
	
	/**
	 * Delete an activity rule from tiki_activity_stream_rules table
	 */
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
			'title' => tr('Delete Rule'),
			'removed' => $removed,
			'rule' => $rule,
			'eventTypes' => $this->getEventTypes(),
		);
	}

	/**
	 * Delete a recorded activity from tiki_activity_stream table
	 */
	function action_deleteactivity(JitRequest $request)
	{
		$id = $request->activityId->int();

		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$this->lib->deleteActivity($id);
			return array(
				'modal' => '1',
				'FORWARD' => array(
					'controller' => 'utilities',
					'action' => 'modal_alert',
					'ajaxtype' => 'feedback',
					'ajaxheading' => tra('Delete Activity'),
					'ajaxmsg' => 'Your activity (id:'.$id.') was successfully deleted',
					'ajaxdismissible' => 'n',
				)
			);
		}

		return array(
			'title' => tra('Delete Activity'),
			'activityId' => $id,
		);
	}
	
	/**
	 * Create/update a sample activity rule. Sample rules are never recorded.
	 */
	function action_sample(JitFilter $request)
	{
		$id = $request->ruleId->int();

		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$event = $request->event->attribute_type();
			$id = $this->replaceRule(
				$id,
				array(
					'rule' => "(event-sample (str $event) event args)",
					'ruleType' => 'sample',
					'notes' => $request->notes->text(),
					'eventType' => $event,
				),
				'event'
			);
		}

		$rule = $this->getRule($id);
		
		$getEventTypes = $this->getEventTypes();
		foreach($getEventTypes as $key => $eventType){
			$eventTypes[$key]['eventType'] = $eventType;
			$sample = $this->lib->getSample($eventType);
			if(!empty($sample)){
				$eventTypes[$key]['sample'] = $sample;
			}
		}
		
		return array(
			'title' => $id ? tr('Edit Rule %0', $id) : tr('Create Sample Rule'),
			'data' => $this->lib->getSample($rule['eventType']),
			'rule' => $rule,
			'eventTypes' => $eventTypes,
		);
	}
	
	/**
	 * Create/update a basic activity rule. Basic rules are recorded by default.
	 */
	function action_record(JitFilter $request)
	{
		$id = $request->ruleId->int();
		$priority = $request['priority'];
		$user = $request['user'];

		if ($request['is_notification'] != "on"){
			$rule = '(event-record event args)';
		}else{
			$rule = "(event-notify event args (str $priority) (str $user))";
		}

		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$id = $this->replaceRule(
				$id,
				array(
					'rule' => $rule,
					'ruleType' => 'record',
					'notes' => $request->notes->text(),
					'eventType' => $request->event->attribute_type(),
				),
				'notes'
			);
		}

		return array(
			'title' => $id ? tr('Edit Rule %0', $id) : tr('Create Record Rule'),
			'rule' => $this->getRule($id),
			'eventTypes' => $this->getEventTypes(),
		);
	}

	/**
	 * Create/update a tracker_filter activity rule. Tracker rules are recorded and linked to a tracker.
	 */
	function action_tracker_filter(JitFilter $request)
	{
		$id = $request->ruleId->int();

		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$tracker = $request->tracker->int();
			$targetEvent = $request->targetEvent->attribute_type();
			$customArguments = $request->parameters->text();

			if (! $targetEvent) {
				throw new Services_Exception_MissingValue('targetEvent');
			}

			$id = $this->replaceRule(
				$id,
				array(
					'rule' => "
(if (equals args.trackerId $tracker) (event-trigger $targetEvent (map
$customArguments
)))
",
					'ruleType' => 'tracker_filter',
					'notes' => $request->notes->text(),
					'eventType' => $request->sourceEvent->attribute_type(),
				),
				'parameters'
			);
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
			$parameters = "(user args.user)\n(type args.type)\n(object args.object)\n(aggregate args.aggregate)\n";
		}

		return array(
			'title' => $id ? tr('Edit Rule %0', $id) : tr('Create Tracker Rule'),
			'rule' => $rule,
			'eventTypes' => $this->getEventTypes(),
			'targetEvent' => $targetEvent,
			'targetTracker' => $targetTracker,
			'trackers' => TikiLib::lib('trk')->list_trackers(),
			'parameters' => $parameters,
		);
	}

	/**
	 * Create/update an advanced activity rule. Advanced rules are recorded by default.
	 */
	function action_advanced(JitFilter $request)
	{
		$id = $request->ruleId->int();

		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$id = $this->replaceRule(
				$id,
				array(
					'rule' => $request->rule->text(),
					'ruleType' => 'advanced',
					'notes' => $request->notes->text(),
					'eventType' => $request->event->attribute_type(),
				),
				'rule'
			);
		}

		return array(
			'title' => $id ? tr('Edit Rule %0', $id) : tr('Create Advanced Rule'),
			'rule' => $this->getRule($id),
			'eventTypes' => $this->getEventTypes(),
		);
	}

	/**
	 * Private function to perform updating of rules
	 */
	private function replaceRule($id, array $data, $ruleField)
	{
		try {
			$id = $this->lib->replaceRule($id, $data);

			return $id;
		} catch (Math_Formula_Exception $e) {
			throw new Services_Exception_FieldError($ruleField, $e->getMessage());
		}
	}

	/**
	 * Private function listing activity rule types
	 */
	private function getRuleTypes()
	{
		return array(
			'sample' => tr('Sample'),
			'record' => tr('Basic'),
			'tracker_filter' => tr('Tracker'),
			'advanced' => tr('Advanced'),
		);
	}

	/**
	 * Private function to get available event types
	 */
	private function getEventTypes()
	{
		$graph = TikiLib::events()->getEventGraph();
		sort($graph['nodes']);
		return $graph['nodes'];
	}

	/**
	 * Private function to get details of an activity rule
	 */
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
	
	/**
	 * Change rule type for an activity rule. Sample rules can be changed to basic or advanced rule. Basic rule can be changed to advanced rule. Other type changes are not supported.
	 */
	function action_change_rule_type($input)
	{
		$id = $input->ruleId->int();
		$rule = $this->getRule($id);
		$status = $this->getRuleStatus($id);
		$ruleTypes = $this->getRuleTypes();
		$currentRuleType = array_intersect_key($ruleTypes, array_flip(array('ruleType' => $rule['ruleType'])));

		if ($rule['ruleType'] === 'sample'){
			$updateRuleTypes = array(
				'record' => tr('Basic'),
				'advanced' => tr('Advanced'),
			);
		}
		elseif ($rule['ruleType'] === 'record'){
			$updateRuleTypes = array(
				'advanced' => tr('Advanced'),
			);
		}
		else {
			throw new Services_Exception_Denied(tr('Invalid rule type'));
		}
		
		$confirm = $input->confirm->int();
		if($confirm){
			$currentRuleType = $rule['ruleType'];
			$newRuleType = $input->ruleType->text();
			//if sample is changed to basic or advanced, "event-sample" needs to be changed to "event-record" in the rule 
			if ($currentRuleType === 'sample'){
				$rule['rule'] = str_replace('event-sample', 'event-record', $rule['rule']);
			}
			
			$id = $this->replaceRule(
				$id,
				array(
					'rule' => $rule['rule'],
					'ruleType' => $newRuleType,
					'notes' => $rule['notes'],
					'eventType' => $rule['eventType'],
				),
				'notes'
			);
		}
		
		return array(
			'title' => tr('Change Rule Type'),
			'rule' => $rule,
			'currentRuleType' => $currentRuleType,
			'ruleTypes' => $updateRuleTypes,
		);
	}
	
	/**
	 * Enable/disable an activity rule. Can be used for basic and advanced types. Tracker type is always enabled, sample type is always disabled, so no need to manage them.
	 */
	function action_change_rule_status($input)
	{
		$id = $input->ruleId->int();
		$rule = $this->getRule($id);
		$status = $this->getRuleStatus($id);
		$confirm = $input->confirm->int();

		if($confirm){
			//to disable a rule "event-record" needs to be changed to "event-sample" in the rule 
			if (($rule['ruleType'] === 'record' || $rule['ruleType'] === 'advanced') && $status === 'enabled'){
				$rule['rule'] = str_replace('event-record', 'event-sample', $rule['rule']);
			}
			//to enable a rule "event-sample" needs to be changed to "event-record" in the rule
			elseif (($rule['ruleType'] === 'record' || $rule['ruleType'] === 'advanced') && $status === 'disabled'){
				$rule['rule'] = str_replace('event-sample', 'event-record', $rule['rule']);
			}
			
			$id = $this->replaceRule(
				$id,
				array(
					'rule' => $rule['rule'],
					'ruleType' => $rule['ruleType'],
					'notes' => $rule['notes'],
					'eventType' => $rule['eventType'],
				),
				'notes'
			);
		}
		
		return array(
			'title' => tr('Change Rule Status'),
			'rule' => $rule,
			'status' => $status,
		);
	}
	
	/**
	 * Private function to get the status of an activity rule
	 */
	private function getRuleStatus($id)
	{
		$rule = $this->getRule($id);
		$ruleCommandRaw = explode(' ', $rule['rule']);
		$ruleCommand = str_replace('(','', $ruleCommandRaw[0]);
		if($ruleCommand === 'event-sample'){
			return 'disabled';
		}
		if($ruleCommand === 'event-record' || $ruleCommand === 'event-notify' || $rule['ruleType'] === 'tracker_filter'){
			return 'enabled';
		}
		else {
			return 'unknown';
		}
	}
}