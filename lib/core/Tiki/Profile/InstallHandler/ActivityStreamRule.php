<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Tiki_Profile_InstallHandler_ActivityStreamRule extends Tiki_Profile_InstallHandler
{
	private $eventType;
	private $ruleType;
	private $rule;
	private $notes;

	function fetchData()
	{
		$data = $this->obj->getData();

		if (isset($data['event_type'])) {
			$this->eventType = $data['event_type'];
		}

		if (isset($data['rule_type'])) {
			$this->ruleType = $data['rule_type'];
		} else {
			$this->ruleType = 'advanced';
		}

		if (isset($data['rule'])) {
			$this->rule = $data['rule'];
		}

		if (isset($data['notes'])) {
			$this->notes = $data['notes'];
		}
	}

	function canInstall()
	{
		$this->fetchData();

		if (empty($this->eventType) || empty($this->rule)) {
			return false;
		}

		return true;
	}

	function _install()
	{
		$this->fetchData();
		$this->replaceReferences($this->eventType);
		$this->replaceReferences($this->ruleType);
		$this->replaceReferences($this->rule);
		$this->replaceReferences($this->notes);
		
		$activitylib = TikiLib::lib('activity');
		$id = $activitylib->replaceRule(null, array(
			'eventType' => $this->eventType,
			'ruleType' => $this->ruleType,
			'rule' => $this->rule,
			'notes' => $this->notes,
		));

		return $id;
	}

	public static function export($writer, $ruleId)
	{
		$activitylib = TikiLib::lib('activity');

		if (is_array($ruleId)) {
			$data = $ruleId;
		} else {
			$data = $activitylib->getRule($ruleId);
		}

		if ($data) {
			$writer->addObject(
				'activity_stream_rule',
				$data['ruleId'],
				array(
					'event_type' => $data['eventType'],
					'rule_type' => $data['ruleType'],
					'rule' => self::exportRule($writer, $data['rule']),
					'notes' => $data['notes'],
				)
			);
			return true;
		} else {
			return false;
		}
	}

	private static function exportRule($writer, $string)
	{
		// Convert (equals args.trackerId X)
		// Convert (not-equals args.trackerId X)
		// Where trackerId  -> tracker
		//       tracker_id -> tracker
		//       tracker    -> tracker
		//       blog_id    -> blog
		//       ...
		$string = preg_replace_callback('/\((?<OP>(?:not-)?equals) args\.(?<KEY>[^\s]+)\s(?<ID>[\d]+)\)/', function ($matches) use ($writer) {
			$key = $matches['KEY'];
			if (substr($key, -2) == 'Id') {
				$key = substr($key, 0, -2);
			} elseif (substr($key, -3) == '_id') {
				$key = substr($key, 0, -3);
			}

			$id = $writer->getReference($key, $matches['ID']);
			return "({$matches['OP']} args.{$matches['KEY']} {$id})";
		}, $string);

		return $string;
	}
}
