<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

/**
 * @return array
 */
function module_category_transition_info()
{
	return array(
		'name' => tra('Category Transitions'),
		'description' => tra('Displays controls to trigger category transitions and change the page\'s state according to predefined rules.'),
		'prefs' => array('feature_category_transition'),
		'params' => array(
		),
	);
}

/**
 * @param $mod_reference
 * @param $module_params
 */
function module_category_transition($mod_reference, $module_params)
{
	$smarty = TikiLib::lib('smarty');
	$modlib = TikiLib::lib('mod');

	if ($object = current_object()) {
		$cat_type = $object['type'];
		$cat_objid = $object['object'];

		$smarty->assign('objType', $cat_type);
		$smarty->assign('objId', $cat_objid);

		require_once 'lib/transitionlib.php';
		$transitionlib = new TransitionLib('category');

		if (isset($_POST['transition'])) {
			$transitionlib->triggerTransition($_POST['transition'], $cat_objid, $cat_type);
			header('Location: ' . $_SERVER['REQUEST_URI']);
			exit;
		}

		$transitions = $transitionlib->getAvailableTransitions($cat_objid, $cat_type);
		$smarty->assign('mod_transitions', $transitions);

	} else if ($modlib->is_admin_mode(true)) {	// add a dummy transition to display on the module admin page

		$smarty->assign(
			'mod_transitions',
			array(
				array(
					'enabled' => true,
					'transitionId' => 0,
					'name' => tra('Example Transition')
				)
			)
		);
	}
}

