<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: ActivityStreamRule.php 47432 2013-09-12 18:27:41Z lphuberdeau $

class Tiki_Profile_InstallHandler_Goal extends Tiki_Profile_InstallHandler
{
	function fetchData()
	{
		if ($this->data) {
			return $this->data;
		}

		$data = $this->obj->getData();

		$data = array_intersect_key($data, [
			'name' => null,
			'type' => null,
			'description' => null,
			'enabled' => null,
			'day_span' => null,
			'from' => null,
			'to' => null,
			'eligible' => null,
			'conditions' => null,
			'rewards' => null,
		]);

		$data['daySpan'] = $data['day_span'];
		unset($data['day_span']);

		$this->data = $data;
	}

	function canInstall()
	{
		$this->fetchData();

		if (empty($this->data['name'])) {
			return false;
		}

		return true;
	}

	function _install()
	{
		$this->fetchData();
		$this->replaceReferences($this->data);
		
		$lib = TikiLib::lib('goal');
		$id = $lib->replaceGoal(null, $this->data);

		return $id;
	}

	public static function export($writer, $goalId)
	{
		$lib = TikiLib::lib('goal');

		$data = $lib->fetchGoal($goalId);

		if ($data) {
			$goalId = $data['goalId'];
			$data['day_span'] = $data['daySpan'];
			unset($data['goalId']);
			unset($data['daySpan']);

			array_walk($data['conditions'], function (& $condition) use ($writer) {
				self::handleParameters($writer, $condition);
			});
			array_walk($data['rewards'], function (& $reward) use ($writer) {
				self::handleParameters($writer, $reward);
			});

			$writer->addObject(
				'goal',
				$goalId,
				$data
			);
			return true;
		} else {
			return false;
		}
	}

	private static function handleParameters($writer, & $data)
	{
		if (! empty($data['trackerItemBadge'])) {
			$data['trackerItemBadge'] = $writer->getReference('trackeritem', $data['trackerItemBadge']);
		}
	}
}
