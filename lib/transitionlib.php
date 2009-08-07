<?php

class TransitionLib
{
	private $transitionType;

	function __construct( $transitionType ) {
		$this->transitionType = $transitionType;
	}

	function getAvailableTransitions( $object, $type = null ) {
		$states = $this->getCurrentStates( $object, $type );

		$transitions = $this->getTransitionsFromStates( $states );
		$transitions = Perms::filter( array( 'type' => 'transition' ), 'object', $transitions, array( 'object' => 'transitionId' ), 'trigger_transition' );

		return $transitions;
	}

	function getAvailableTransitionsFromState( $state, $object, $type = null ) {
		$transitions = $this->getAvailableTransitions( $object, $type );

		$out = array();
		foreach( $transitions as $tr ) {
			if( $tr['from'] == $state ) {
				$out[ $tr['transitionId'] ] = $tr['name'];
			}
		}

		return $out;
	}

	function triggerTransition( $transitionId, $object, $type = null ) {
		// Make sure the transition exists
		if( ! $transition = $this->getTransition( $transitionId ) ) {
			return false;
		}

		// Make sure the user can use it
		$perms = Perms::get( array( 'type' => 'transition', 'object' => $transitionId ) );
		if( ! $perms->trigger_transition ) {
			return false;
		}

		// Verify that the states are consistent
		$states = $this->getCurrentStates( $object, $type );
		if( ! in_array( $transition['from'], $states ) ) {
			return false;
		}
		if( in_array( $transition['to'], $states ) ) {
			return false;
		}

		$this->addState( $transition['to'], $object, $type );
		if( ! $transition['preserve'] ) {
			$this->removeState( $transition['from'], $object, $type );
		}

		return true;
	}

	// Database interaction

	function addTransition( $from, $to, $name, $preserve = false ) {
		$db = TikiDb::get();

		$db->query( "INSERT INTO `tiki_transitions` ( `type`, `from`, `to`, `name`, `preserve` ) VALUES( ?, ?, ?, ?, ? )", array( $this->transitionType, $from, $to, $name, (int) $preserve ) );

		return $db->getOne( 'SELECT MAX(`transitionId`) FROM `tiki_transitions`' );
	}

	function removeTransition( $transitionId ) {
		$db->query( 'DELETE FROM `tiki_transitions` WHERE `transitionId` = ?', array( $transitionId ) );
	}

	private function getTransitionsFromStates( $states ) {
		$db = TikiDb::get();

		$bindvars = array( $this->transitionType );
		$query = "SELECT `transitionId`, `preserve`, `name`, `from`, `to` FROM `tiki_transitions` WHERE `type` = ? AND " . $db->in( 'from', $states, $bindvars ) . ' AND NOT (' . $db->in( 'to', $states, $bindvars ) . ')';

		return $db->fetchAll( $query, $bindvars );
	}

	private function getTransition( $transitionId ) {
		$db = TikiDb::get();

		$bindvars = array( $this->transitionType, $transitionId );
		$query = "SELECT `transitionId`, `preserve`, `name`, `from`, `to` FROM `tiki_transitions` WHERE `type` = ? AND `transitionId` = ?";

		return reset( $db->fetchAll( $query, $bindvars ) );
	}

	// The following functions vary depending on the transition type

	private function getCurrentStates( $object ) {
		switch( $this->transitionType ) {
		case 'group':
			global $userlib;
			return $userlib->get_user_groups( $object );
		case 'category':
			global $categlib; require_once 'lib/categories/categlib.php';
			return $categlib->get_object_categories( $type, $itemId );
		}
	}

	private function addState( $state, $object, $type ) {
		switch( $this->transitionType ) {
		case 'group':
			global $userlib;
			$userlib->assign_user_to_group( $object, $state );
			return;
		case 'category':
			global $categlib; require_once 'lib/categories/categlib.php';
			$categlib->categorize_any( $type, $object, $state );
			return;
		}
	}

	private function removeState( $state, $object, $type ) {
		switch( $this->transitionType ) {
		case 'group':
			global $userlib;
			$userlib->remove_user_from_group( $object, $state );
			return;
		case 'category':
			global $categlib; require_once 'lib/categories/categlib.php';
			if( $catobj = $categlib->is_categorized( $type, $object ) ) {
				$categlib->uncategorize( $catobj, $state );
			}
			return;
		}
	}
}

?>
