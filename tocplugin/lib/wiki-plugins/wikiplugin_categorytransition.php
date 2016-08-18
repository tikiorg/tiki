<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_categorytransition_info()
{
	return array(
		'name' => tra('PluginCategoryTransition'),
		'description' => tra('Displays controls to trigger category transitions for any object.'),
		'prefs' => array( 'feature_category_transition', 'wikiplugin_categorytransition' ),
		'iconname' => 'move',
		'introduced' => 9,
		'params' => array(
			'objType' => array(
				'required' => true,
				'name' => tra('Object Type'),
				'description' => tra('Object Type'),
				'since' => '9.0',
				'filter' => 'text',
				'default' => '',
			),
			'objId' => array(
				'required' => true,
				'name' => tra('Object ID'),
				'description' => tra('Object ID'),
				'since' => '9.0',
				'filter' => 'text',
				'default' => '',
				'profile_reference' => 'type_in_param',
			),
			'redirect' => array(
				'required' => false,
				'name' => tra('Redirect URL'),
				'description' => tra('URL the user is sent to after transition is done'),
				'since' => '9.0',
				'filter' => 'text',
				'default' => "REQUEST_URI",
			) 
		),
	);
}

function wikiplugin_categorytransition( $data, $params )
{
	$smarty = TikiLib::lib('smarty');

	extract($params, EXTR_SKIP);	

	if (empty($redirect)) {
		$redirect = $_SERVER['REQUEST_URI'];
	}

	if ( $objType && $objId ) {
		$smarty->assign('objType', $objType);
		$smarty->assign('objId', $objId);

		require_once 'lib/transitionlib.php';
		$transitionlib = new TransitionLib('category');

		if ( isset( $_POST['wp_transition'] ) && $_POST['wp_transition_obj'] == $objType . ':' . $objId) {
			$transitionlib->triggerTransition($_POST['wp_transition'], $objId, $objType);

			header('Location: ' . $redirect);
			exit;
		}

		$transitions = $transitionlib->getAvailableTransitions($objId, $objType);
		if (!$transitions) {
			return '';
		}
		$smarty->assign('wp_transitions', $transitions);
		$smarty->assign('wp_transition_obj', $objType . ':' . $objId);

		$out = $smarty->fetch('wiki-plugins/wikiplugin_categorytransition.tpl');
		return '~np~' . $out . '~/np~';
	}

	return '';
}

