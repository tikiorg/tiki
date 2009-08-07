<?php

function module_category_transition_info() {
	return array(
		'name' => tra('Category Transition'),
		'description' => tra('Displays controls to trigger category transitions and change the page\'s state according to predefined rules.'),
		'prefs' => array( 'feature_category_transition' ),
		'params' => array(
		),
	);
}

function module_category_transition( $mod_reference, $module_params ) {
	global $smarty, $cat_type, $cat_objid;

	if( $cat_type && $cat_objid ) {
		$smarty->assign( 'objType', $cat_type );
		$smarty->assign( 'objId', $cat_objid );

		require_once 'lib/transitionlib.php';
		$transitionlib = new TransitionLib( 'category' );

		if( isset( $_POST['transition'] ) ) {
			$transitionlib->triggerTransition( $_POST['transition'], $cat_objid, $cat_type );
			header( 'Location: ' . $_SERVER['REQUEST_URI'] );
			exit;
		}

		$transitions = $transitionlib->getAvailableTransitions( $cat_objid, $cat_type );
		$smarty->assign( 'mod_transitions', $transitions );
	}
}

