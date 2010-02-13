<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_subscribenewsletter_info() {
	return array(
		'name' => tra('Subscribe newsletter'),
		'documentation' => 'PluginSubscribeNewsletter',
		'description' => tra('A button to subscribe to a newsletter available for a user if not already in'),
		'prefs' => array('feature_newsletters', 'wikiplugin_subscribenewsletter'),
		'body' => tra('Invitation message'),
		'params' => array(
			'nlId' => array(
				'required' => true,
				'name' => '',
				'description' => '',
				'filter' => 'digits',
			),
			'thanks' => array(
				'required' => false,
				'name' => tra('Confirmation message after posting form'),
				'filter' => 'wikicontent',
			),
		),
	);
}
function wikiplugin_subscribenewsletter($data, $params) {
	global $prefs, $user, $userlib, $smarty;
	global $nllib; include_once('lib/newsletters/nllib.php');
	extract($params, EXTR_SKIP);
	if ($prefs['feature_newsletters'] != 'y') {
		return tra('Feature disabled');
	}
	if (empty($nlId)) {
			return tra('Incorrect param');
	}
	$info = $nllib->get_newsletter($nlId);
	if (empty($info) || $info['allowUserSub'] != 'y') {
		return tra('Incorrect param');
	}
	if (empty($user)) {
		return;
	}
	if (!$userlib->user_has_perm_on_object($user, $nlId, 'newsletter', 'tiki_p_subscribe_newsletters')) {
		return;
	}
	$alls = $nllib->get_subscribers($nlId, 'n');
	if (in_array($user, $alls)) {
		return;
	}
	$wpSubscribe = '';
	if (isset($_REQUEST['wpSubscribe']) && $_REQUEST['wpNlId'] == $nlId) {
		if ($nllib->newsletter_subscribe($nlId, $user, 'y', 'n')) {
			$wpSubscribe = 'y';
			$smarty->assign('subscribeThanks', isset($thanks)?$thanks:'');
		}
	}
	$smarty->assign_by_ref('wpSubscribe', $wpSubscribe);
	$smarty->assign_by_ref('subcribeMessage', $data);
	$smarty->assign_by_ref('subscribeInfo', $info);
	$res = $smarty->fetch('wiki-plugins/wikiplugin_subscribenewsletter.tpl');
	return '~np~'.$res.'~/np~';
}
