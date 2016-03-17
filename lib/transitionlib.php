<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * TransitionLib
 *
 */
class TransitionLib
{
	private $transitionType;

    /**
     * @param $transitionType
     */
    function __construct( $transitionType )
	{
		$this->transitionType = $transitionType;
	}

    /**
     * @param $object
     * @param null $type
     * @return array
     */
    function getAvailableTransitions( $object, $type = null )
	{
		$states = $this->getCurrentStates($object, $type);

		$transitions = $this->getTransitionsFromStates($states);
		$transitions = Perms::filter(
			array('type' => 'transition'),
			'object',
			$transitions,
			array('object' => 'transitionId'),
			'trigger_transition'
		);

		foreach ( $transitions as & $tr ) {
			$object = new Tiki_Transition($tr['from'], $tr['to']);
			$object->setStates($states);
			foreach ( $tr['guards'] as $guard ) {
				call_user_func_array(array($object, 'addGuard' ), $guard);
			}

			$tr['enabled'] = $object->isReady();
			$tr['explain'] = $object->explain();
		}

		return $transitions;
	}

    /**
     * @param $state
     * @param $object
     * @param null $type
     * @return array
     */
    function getAvailableTransitionsFromState( $state, $object, $type = null )
	{
		$transitions = $this->getAvailableTransitions($object, $type);

		$out = array();
		foreach ( $transitions as $tr ) {
			if ( $tr['from'] == $state ) {
				$out[$tr['transitionId']] = $tr['name'];
			}
		}

		return $out;
	}

    /**
     * @param $transitionId
     * @param $object
     * @param null $type
     * @return bool
     */
    function triggerTransition( $transitionId, $object, $type = null )
	{
		// Make sure the transition exists
		if ( ! $transition = $this->getTransition($transitionId) ) {
			return false;
		}

		// Make sure the user can use it
		$perms = Perms::get(array('type' => 'transition', 'object' => $transitionId));
		if ( ! $perms->trigger_transition ) {
			return false;
		}

		// Verify that the states are consistent
		$states = $this->getCurrentStates($object, $type);

		$tr = new Tiki_Transition($transition['from'], $transition['to']);
		$tr->setStates($states);

		foreach ( $transition['guards'] as $guard ) {
			call_user_func_array(array($tr, 'addGuard'), $guard);
		}

		if ( ! $tr->isReady() ) {
			return false;
		}

		$this->addState($transition['to'], $object, $type);
		if ( ! $transition['preserve'] ) {
			$this->removeState($transition['from'], $object, $type);
		}

		return true;
	}

    /**
     * @param $states
     * @return array
     */
    function listTransitions( $states )
	{
		$db = TikiDb::get();

		if ( empty($states) ) {
			return array();
		}

		$bindvars = array($this->transitionType);
		$query = "SELECT `transitionId`, `preserve`, `name`, `from`, `to`, `guards` FROM `tiki_transitions` WHERE `type` = ? AND ( " .
						$db->in('from', $states, $bindvars) .
						' OR ' . $db->in('to', $states, $bindvars) . ')';

		$result = $db->fetchAll($query, $bindvars);

		return array_map(array($this, 'expandGuards'), $result);
	}

	// Database interaction

    /**
     * @param $from
     * @param $to
     * @param $name
     * @param bool $preserve
     * @param array $guards
     * @return mixed
     */
    function addTransition( $from, $to, $name, $preserve = false, array $guards = array() )
	{
		$db = TikiDb::get();

		$db->query(
			"INSERT INTO `tiki_transitions` ( `type`, `from`, `to`, `name`, `preserve`, `guards`) VALUES( ?, ?, ?, ?, ?, ? )",
			array($this->transitionType, $from, $to, $name, (int) $preserve, json_encode($guards))
		);

		return $db->getOne('SELECT MAX(`transitionId`) FROM `tiki_transitions`');
	}

    /**
     * @param $transitionId
     * @param $from
     * @param $to
     * @param $label
     * @param $preserve
     */
    function updateTransition( $transitionId, $from, $to, $label, $preserve )
	{
		$db = TikiDb::get();
		$db->query(
			'UPDATE `tiki_transitions` SET `name` = ?, `from` = ?, `to` = ?, `preserve` = ? WHERE `transitionId` = ?',
			array($label, $from, $to, (int) $preserve, (int) $transitionId)
		);
	}

    /**
     * @param $transitionId
     * @param array $guards
     */
    function updateGuards( $transitionId, array $guards )
	{
		$db = TikiDb::get();
		$db->query(
			'UPDATE `tiki_transitions` SET `guards` = ? WHERE `transitionId` = ?',
			array(json_encode($guards), (int) $transitionId)
		);
	}

    /**
     * @param $transitionId
     */
    function removeTransition( $transitionId )
	{
		$db = TikiDb::get();

		$db->query('DELETE FROM `tiki_transitions` WHERE `transitionId` = ?', array($transitionId));
	}

    /**
     * @param $states
     * @return array
     */
    private function getTransitionsFromStates( $states )
	{
		$db = TikiDb::get();

		if ( empty( $states ) ) {
			return array();
		}

		$bindvars = array($this->transitionType);
		$query = "SELECT `transitionId`, `preserve`, `name`, `from`, `to`, `guards` FROM `tiki_transitions` WHERE `type` = ? AND " .
						$db->in('from', $states, $bindvars) . ' AND NOT (' .
						$db->in('to', $states, $bindvars) . ')';

		$result = $db->fetchAll($query, $bindvars);

		return array_map(array($this, 'expandGuards'), $result);
	}

    /**
     * @param $transitionId
     * @return mixed
     */
    function getTransition( $transitionId )
	{
		$db = TikiDb::get();

		$bindvars = array($this->transitionType, $transitionId);
		$query = "SELECT `transitionId`, `preserve`, `name`, `from`, `to`, `guards` FROM" .
							" `tiki_transitions` WHERE `type` = ? AND `transitionId` = ?";
		$result = $db->fetchAll($query, $bindvars);

		return $this->expandGuards(reset($result));
	}

    /**
     * @param $transition
     * @return mixed
     */
    private function expandGuards( $transition )
	{
		$transition['guards'] = json_decode($transition['guards'], true);
		if ( ! $transition['guards'] ) {
			$transition['guards'] = array();
		}

		return $transition;
	}

	// The following functions vary depending on the transition type

    /**
     * @param $object
     * @param $type
     * @return array
     */
    private function getCurrentStates( $object, $type )
	{
		switch( $this->transitionType ) {
			case 'group':
				$userlib = TikiLib::lib('user');
				return $userlib->get_user_groups($object);
			case 'category':
				$categlib = TikiLib::lib('categ');
				return $categlib->get_object_categories($type, $object);
		}
	}

    /**
     * @param $state
     * @param $object
     * @param $type
     */
    private function addState( $state, $object, $type )
	{
		switch ( $this->transitionType ) {
		case 'group':
			$userlib = TikiLib::lib('user');
			$userlib->assign_user_to_group($object, $state);
			return;
		case 'category':
			$categlib = TikiLib::lib('categ');
			$categlib->categorize_any($type, $object, $state);
			return;
		}
	}

    /**
     * @param $state
     * @param $object
     * @param $type
     */
    private function removeState( $state, $object, $type )
	{
		switch ( $this->transitionType ) {
		case 'group':
			$userlib = TikiLib::lib('user');
			$userlib->remove_user_from_group($object, $state);
			return;
		case 'category':
			$categlib = TikiLib::lib('categ');
			if ( $catobj = $categlib->is_categorized($type, $object) ) {
				$categlib->uncategorize($catobj, $state);
			}
			return;
		}
	}
}
