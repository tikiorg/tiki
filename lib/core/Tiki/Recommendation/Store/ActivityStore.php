<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

namespace Tiki\Recommendation\Store;

use Tiki\Recommendation\Input;
use Tiki\Recommendation\Recommendation;
use Tiki\Recommendation\RecommendationSet;

class ActivityStore implements StoreInterface
{
	private $unified;
	private $relation;
	private $events;

	private $tx;

	function __construct($unifiedsearch, $relation, \Tiki_Event_Manager $events)
	{
		$this->unified = $unifiedsearch;
		$this->relation = $relation;
		$this->events = $events;
	}

	function __destruct()
	{
		$this->terminate();
	}

	function getInputs()
	{
		$db = \TikiDb::get();

		$this->tx = $db->begin();

		$result = $db->fetchAll('SELECT login FROM users_users u INNER JOIN tiki_user_monitors m ON u.userId = m.userId WHERE m.event = ?', ['tiki.recommendation.incoming']);
		foreach ($result as $row) {
			yield new Input\UserInput($row['login']);
		}
	}

	function terminate()
	{
		if ($this->tx) {
			$this->tx->commit();
			$this->tx = null;
		}
	}

	function isReceived($input, Recommendation $rec)
	{
		if ($input instanceof Input\UserInput) {
			$query = $this->unified->buildQuery([
				'type' => 'activity',
				'event_type' => 'tiki.recommendation.incoming',
				'object' => $input->getUser(),
				'item_type' => '"' . $rec->getType() . '"',
				'item_id' => '"' . $rec->getId() . '"',
			]);
			$query->setRange(0, 1);

			$rs = $query->search($this->unified->getIndex());

			return count($rs) > 0;
		}

		return false;
	}

	function store($input, RecommendationSet $recommendations)
	{
		if ($input instanceof Input\UserInput) {
			foreach ($recommendations as $rec) {
				$this->relation->add_relation('tiki.recommendation.obtained', 'user', $input->getUser(), $rec->getType(), $rec->getId());
				$this->events->trigger('tiki.save', [
					'type' => $rec->getType(),
					'object' => $rec->getId(),
				]);

				$this->events->trigger('tiki.recommendation.incoming', [
					'type' => 'user',
					'object' => $input->getUser(),
					'item_type' => $rec->getType(),
					'item_id' => $rec->getId(),
					'engine_id' => $recommendations->getEngine(),
				]);

				// We only want to recommend one item per run
				break;
			}
		}
	}
}
