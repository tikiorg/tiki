<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$section = 'mytiki';
require_once ('tiki-setup.php');
//include_once ('lib/userprefs/userprefslib.php');

$auto_query_args = array('userId', 'view_user');

$access->check_user($user);

$headerlib->add_map();

//$userwatch = $tikilib->get_user_login($_REQUEST['userId']);

if (isset($_REQUEST['user_calendar_watch_editor']) && $_REQUEST['user_calendar_watch_editor'] == true) {
	$tikilib->set_user_preference($user, 'user_calendar_watch_editor', 'y');
} else {
	$tikilib->set_user_preference($user, 'user_calendar_watch_editor', 'n');
}

if (isset($_REQUEST['user_article_watch_editor']) && $_REQUEST['user_article_watch_editor'] == true) {
	$tikilib->set_user_preference($user, 'user_article_watch_editor', 'y');
} else {
	$tikilib->set_user_preference($user, 'user_article_watch_editor', 'n');
}

if (isset($_REQUEST['user_wiki_watch_editor']) && $_REQUEST['user_wiki_watch_editor'] == true) {
	$tikilib->set_user_preference($user, 'user_wiki_watch_editor', 'y');
} else {
	$tikilib->set_user_preference($user, 'user_wiki_watch_editor', 'n');
}

if (isset($_REQUEST['user_blog_watch_editor']) && $_REQUEST['user_blog_watch_editor'] == true) {
	$tikilib->set_user_preference($user, 'user_blog_watch_editor', 'y');
} else {
	$tikilib->set_user_preference($user, 'user_blog_watch_editor', 'n');
}

if (isset($_REQUEST['user_tracker_watch_editor']) && $_REQUEST['user_tracker_watch_editor'] == true) {
	$tikilib->set_user_preference($user, 'user_tracker_watch_editor', 'y');
} else {
	$tikilib->set_user_preference($user, 'user_tracker_watch_editor', 'n');
}

if (isset($_REQUEST['user_comment_watch_editor']) && $_REQUEST['user_comment_watch_editor'] == true) {
	$tikilib->set_user_preference($user, 'user_comment_watch_editor', 'y');
} else {
	$tikilib->set_user_preference($user, 'user_comment_watch_editor', 'n');
}

header('Location: tiki-user_watches.php');
die;
