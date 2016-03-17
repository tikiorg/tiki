<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: tiki-socialnetworks.php 53802 2015-02-06 00:11:35Z jyhem $

$section = 'mytiki';
require_once ('tiki-setup.php');
require_once ('lib/socialnetworkslib.php');
$access->check_feature('feature_socialnetworks');
$access->check_permission('tiki_p_socialnetworks', tra('Social networks'));

$auto_query_args = array();
if (isset($_REQUEST['connect'])) {
	$socialnetworkslib->getLinkedInRequestToken();
}
/* Is set to link the existing user to the LinkedIn Account */
if (isset($_REQUEST['link'])) {
	$access->check_user($user);
	$socialnetworkslib->getLinkedInRequestToken();
}
if (isset($_REQUEST['code'])) {
	if ($_SESSION['LINKEDIN_REQ_STATE'] != $_REQUEST['state']) {
		//csrf breach 401
		return false;
	}
	$_SESSION['LINKEDIN_AUTH_CODE'] = $_REQUEST['code'];
	$socialnetworkslib->getLinkedInAccessToken();
}
/* Is set to remove the link from your user to the LinkedIn Account */
if (isset($_REQUEST['remove'])) {
	global $tikilib;
	$access->check_user($user);
	$tikilib->set_user_preference($user, 'linkedin_token', '');
	$tikilib->set_user_preference($user, 'linkedin_id', '');
}

header("Location: tiki-socialnetworks.php");
