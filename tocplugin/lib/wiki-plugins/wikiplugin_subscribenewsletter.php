<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_subscribenewsletter_info()
{
	return array(
		'name' => tra('Subscribe to newsletter'),
		'documentation' => 'PluginSubscribeNewsletter',
		'description' => tra('Allow users to subscribe to a newsletter'),
		'prefs' => array('feature_newsletters', 'wikiplugin_subscribenewsletter'),
		'body' => tra('Invitation message'),
		'iconname' => 'articles',
		'introduced' => 5,
		'tags' => array( 'basic' ),
		'params' => array(
			'nlId' => array(
				'required' => true,
				'name' => tra('Newsletter ID'),
				'description' => tra('Identification number of the Newsletter that you want to allow the users to subscribe to'),
				'since' => '5.0',
				'filter' => 'digits',
				'default' => '',
				'profile_reference' => 'newsletter',
			),
			'thanks' => array(
				'required' => false,
				'name' => tra('Confirmation Message'),
				'description' => tra('Confirmation message after posting form. The plugin body is then the button label.'),
				'since' => '5.0',
				'filter' => 'wikicontent',
			),
			'button' => array(
				'required' => false,
				'name' => tra('Button'),
				'description' => tra('Button label. The plugin body is then the confirmation message'),
				'since' => '5.0',
				'filter' => 'wikicontent',
			),
			'wikisyntax' => array(
				'required' => false,
				'safe' => true,
				'name' => tra('Wiki Syntax'),
				'description' => tra('Choose whether the output should be parsed as wiki syntax'),
				'since' => '6.0',
				'filter' => 'int',
				'default' => 0,
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 1), 
					array('text' => tra('No'), 'value' => 0)
				)
			),

		),
	);
}
function wikiplugin_subscribenewsletter($data, $params)
{
	global $prefs, $user;
	$userlib = TikiLib::lib('user');
	$tikilib = TikiLib::lib('tiki');
	$smarty = TikiLib::lib('smarty');
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

	if (!$userlib->user_has_perm_on_object($user, $nlId, 'newsletter', 'tiki_p_subscribe_newsletters')) {
		return;
	}

	if ($user) {
		$alls = $nllib->get_all_subscribers($nlId, false);
		foreach ($alls as $all) {
			if (strtolower($all['db_email']) == strtolower($user))
				return;
		}
	}

	$wpSubscribe = '';
	$wpError = '';
	$subscribeEmail = '';
	if (isset($_REQUEST['wpSubscribe']) && $_REQUEST['wpNlId'] == $nlId) {
		if (!$user && empty($_REQUEST['wpEmail'])) {
			$wpError = tra('Invalid Email');
		} elseif (!$user && !validate_email($_REQUEST['wpEmail'], $prefs['validateEmail'])) {
			$wpError = tra('Invalid Email');
			$subscribeEmail = $_REQUEST['wpEmail'];
		} elseif (($user && $nllib->newsletter_subscribe($nlId, $user, 'y', 'n'))
			|| (!$user && $nllib->newsletter_subscribe($nlId, $_REQUEST['wpEmail'], 'n', $info['validateAddr']))) {
			$wpSubscribe = 'y';
			$smarty->assign('subscribeThanks', empty($thanks)?$data: $thanks);
		} else {
			$wpError = tra('Already subscribed');
		}
	}
	$smarty->assign_by_ref('wpSubscribe', $wpSubscribe);
	$smarty->assign_by_ref('wpError', $wpError);
	$smarty->assign('subscribeEmail', $subscribeEmail);
	$smarty->assign('subcribeMessage', empty($button)?$data: $button);
	$smarty->assign_by_ref('subscribeInfo', $info);
	$res = $smarty->fetch('wiki-plugins/wikiplugin_subscribenewsletter.tpl');
	if (isset($params["wikisyntax"]) && $params["wikisyntax"]==1) {
		return $res;
	} else { 		// if wikisyntax != 1 : no parsing of any wiki syntax
		return '~np~'.$res.'~/np~';
	}
}
