<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/*
 * AJAXified Shoutbox module (jonnybradley for mpvolt Aug/Sept 2008 - de-AJAXified for Tiki 7 Dec 2010 jb)
 *
 * Anonymous may need tiki_p_view_shoutbox and tiki_p_post_shoutbox setting (in Group admin)
 * Enable Admin/Wiki/Wiki Features/feature_antibot to prevent spam ("Anonymous editors must input anti-bot code")
 *
 */

/*
 * Added twitter+facebook support (joernott for poiesipedia.com May 2010)
 *
 * Requires a site registration with twitter/facebook to receive site consumer key/secret.
 * The user must authorize the site by requesting an oauth token via tiki-socialnetworks.php.
 *
 */

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'], basename(__FILE__)) !== false) {
	header('location: index.php');
	exit;
}

/**
 * @return array
 */
function module_shoutbox_info()
{
	return array(
		'name' => tra('Shoutbox'),
		'description' => tra('The shoutbox is a quick messaging tool. Messages reload each time the page changes. Anyone with the right permission can see all messages. Another permission allows messages to be sent..'),
		'prefs' => array('feature_shoutbox'),
		'documentation' => 'Module shoutbox',
		'params' => array(
			'tooltip' => array(
				'name' => tra('Tooltip'),
				'description' => tra('If set to "1", displays message post dates and times as tooltips instead of showing directly in the module content.') . " " . tr('Default:') . '"0".',
				'filter' => 'word'
			),
			'buttontext' => array(
				'name' => tra('Button label'),
				'description' => tra('Label on the button to post a message.') . ' ' . tra('Default:') . ' ' . tra('Post')
			),
			'waittext' => array(
				'name' => tra('Wait label'),
				'description' => tra('Label on the button to post a message when the message is being posted if AJAX is enabled.') . ' ' . tra('Default:') . ' ' . tra('Please wait...')
			),
			'maxrows' => array(
				'name' => tra('Maximum messages shown'),
				'description' => tra('Number of messages to display.') . ' ' . tra('Default:') . ' 5.',
				'filter' => 'int'
			),
			'tweet' => array(
				'name'=> tra('Tweet'),
				'description' => tra('If set to "1" and the user has authorized us to tweet messages with Twitter, the user can decide, if he wants to shout via Twitter.'),
				'filter' => 'word'
			),
			'facebook' => array(
				'name'=> tra('Facebook'),
				'description' => tra('If set to "1" and the user has authorized us with Facebook, the user can decide, if he wants to add the shout to his Facebook wall.'),
				'filter' => 'word'
			)

		)
	);
}

/**
 * @param $inFormValues
 */
function doProcessShout($inFormValues)
{
	global $shoutboxlib, $user, $prefs;
	$captchalib = TikiLib::lib('captcha');
	$smarty = TikiLib::lib('smarty');
//	$smarty->assign('tweet',$inFormValues['tweet']);
	if (array_key_exists('shout_msg', $inFormValues) && strlen($inFormValues['shout_msg']) > 2) {
		if (empty($user) && $prefs['feature_antibot'] == 'y' && (!$captchalib->validate())) {
			$smarty->assign('shout_error', $captchalib->getErrors());
			$smarty->assign_by_ref('shout_msg', $inFormValues['shout_msg']);
		} else {

			$shoutboxlib->replace_shoutbox(0, $user, $inFormValues['shout_msg'], ($inFormValues['shout_tweet']==1), ($inFormValues['shout_facebook']==1));
		}
	}
}

/**
 * @param $mod_reference
 * @param $module_params
 */
function module_shoutbox($mod_reference, $module_params)
{
	global $shoutboxlib, $prefs, $user, $tiki_p_view_shoutbox;
	global $tiki_p_admin_shoutbox, $tiki_p_post_shoutbox, $base_url;
	$access = TikiLib::lib('access');
	$smarty = TikiLib::lib('smarty');
	$tikilib = TikiLib::lib('tiki');

	include_once ('lib/shoutbox/shoutboxlib.php');

	if ($tiki_p_view_shoutbox == 'y') {

		if (isset($_REQUEST['shout_remove'])) {
			$info = $shoutboxlib->get_shoutbox($_REQUEST['shout_remove']);
			if ($tiki_p_admin_shoutbox == 'y' || $info['user'] == $user) {
				$access->check_authenticity();
				$shoutboxlib->remove_shoutbox($_REQUEST["shout_remove"]);
			}
		}

		if ($tiki_p_post_shoutbox == 'y') {
			if (isset($_REQUEST['shout_send'])) {
				doProcessShout($_REQUEST);
			}
		}

		$maxrows = isset($module_params['maxrows']) ? $module_params['maxrows'] : 5;
		$shout_msgs = $shoutboxlib->list_shoutbox(0, $maxrows, 'timestamp_desc', '');
		$smarty->assign('shout_msgs', $shout_msgs['data']);

		// Subst module parameters
		$smarty->assign('tooltip', isset($module_params['tooltip']) ? $module_params['tooltip'] : 0);
		$smarty->assign('buttontext', isset($module_params['buttontext']) ? $module_params['buttontext'] : tra('Post'));
		$smarty->assign('waittext', isset($module_params['waittext']) ? $module_params['waittext'] : tra('Please wait...'));

		$smarty->assign(
			'tweet',
			isset($module_params['tweet']) &&($tikilib->get_user_preference($user, 'twitter_token')!='') ? $module_params['tweet'] : '0'
		);

		$smarty->assign(
			'facebook',
			isset($module_params['facebook']) && ($tikilib->get_user_preference($user, 'facebook_token')!='') ? $module_params['facebook'] : '0'
		);
	}
}
