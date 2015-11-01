<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Tiki_Transition
{
	private $from;
	private $to;
	private $states = array();
	private $guards = array();

	private $blockers;

	function __construct($from, $to)
	{
		$this->from = $from;
		$this->to = $to;
	}

	function setStates(array $states)
	{
		$this->states = $states;
		$this->blockers = null;
	}

	function addGuard($type, $boundary, $set)
	{
		if (method_exists($this, '_' . $type)) {
			$this->guards[] = array($type, $boundary, $set);
		} else {
			$this->guards[] = array('unknown', 1, array($type));
		}
	}

	function isReady()
	{
		return count($this->explain()) == 0;
	}

	function explain()
	{
		$this->blockers = array();
		$this->_exactly(1, array($this->from));
		$this->_exactly(0, array($this->to));

		$this->applyGuards();

		return $this->blockers;
	}

	private function applyGuards()
	{
		foreach ($this->guards as $guard) {
			$method = '_' . array_shift($guard);

			call_user_func_array(array($this, $method), $guard);
		}
	}

	private function addBlocker($type, $amount, $set)
	{
		$this->blockers[] = array('class' => $type, 'count' => $amount, 'set' => array_values($set));
	}

	private function _exactly($amount, $list)
	{
		if (count($list) < $amount) {
			$this->addBlocker('invalid', $amount, $list);
			return;
		}

		$intersect = array_intersect($this->states, $list);
		$count = count($intersect);

		if ($count > $amount) {
			$this->addBlocker('extra', $count - $amount, $intersect);
		} elseif ($count < $amount) {
			$set = array_diff($list, $intersect);
			$this->addBlocker('missing', $amount - $count, $set);
		}
	}

	private function _atMost($amount, $list)
	{
		$intersect = array_intersect($this->states, $list);
		$count = count($intersect);

		if ($count > $amount) {
			$this->addBlocker('extra', $count - $amount, $intersect);
		}
	}

	private function _atLeast($amount, $list)
	{
		if (count($list) < $amount) {
			$this->addBlocker('invalid', $amount, $list);
			return;
		}

		$intersect = array_intersect($this->states, $list);
		$count = count($intersect);

		if ($count < $amount) {
			$set = array_diff($list, $intersect);
			$this->addBlocker('missing', $amount - $count, $set);
		}
	}

	private function _unknown($amount, $list)
	{
		$this->addBlocker('unknown', 1, $list);
	}
}
