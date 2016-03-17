<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$section = 'cms';
require_once('tiki-setup.php');
$artlib = TikiLib::lib('art');

$access->check_feature('feature_articles');
$access->check_permission('tiki_p_admin_cms');

if (!isset($_REQUEST["topicid"])) {
  $smarty->assign('msg', tra("No topic id specified"));
  $smarty->display("error.tpl");
  die;
}

$topic_info = $artlib->get_topic($_REQUEST["topicid"]);
if ($topic_info == DB_ERROR) {
  $smarty->assign('msg', tra("Invalid topic id specified"));
  $smarty->display("error.tpl");
  die;
}
$smarty->assign_by_ref('topic_info', $topic_info);

if (isset($_REQUEST["edittopic"])) {
  if (isset($_FILES['userfile1'])&&is_uploaded_file($_FILES['userfile1']['tmp_name'])) {
    $fp = fopen($_FILES['userfile1']['tmp_name'], "rb");
    $data = fread($fp, filesize($_FILES['userfile1']['tmp_name']));
    fclose($fp);
    $imgtype = $_FILES['userfile1']['type'];
    $imgsize = $_FILES['userfile1']['size'];
    $imgname = $_FILES['userfile1']['name'];

    $artlib->replace_topic_image($_REQUEST["topicid"], $imgname, $imgtype, $imgsize, $data);
  }

  if (isset($_REQUEST["name"])) {
    $artlib->replace_topic_name($_REQUEST["topicid"], $_REQUEST["name"]);
	$topic_info['name'] = $_REQUEST['name'];
  }
	if (isset($_REQUEST['email']) && !empty($_REQUEST['email'])) {
		if (!validate_email($_REQUEST['email'])) {
			$errors[] = tra('Invalid email');
			$smarty->assign('email', $_REQUEST['email']);
		} else {
			$tikilib->add_user_watch('admin', 'topic_article_created', $_REQUEST['topicid'], 'cms', $topic_info['name'], 'tiki-edit_topic.php?topicId='.$_REQUEST['topicid'], $_REQUEST['email']);
			$tikilib->add_user_watch('admin', 'topic_article_edited', $_REQUEST['topicid'], 'cms', $topic_info['name'], 'tiki-edit_topic.php?topicId='.$_REQUEST['topicid'], $_REQUEST['email']);
			$tikilib->add_user_watch('admin', 'topic_article_deleted', $_REQUEST['topicid'], 'cms', $topic_info['name'], 'tiki-edit_topic.php?topicId='.$_REQUEST['topicid'], $_REQUEST['email']);
		}
	}
	if (empty($errors)) {
		header("Location: tiki-admin_topics.php");
		die;
	} else {
		$smarty->assign_by_ref('errors', $errors);
	}
}
include_once ('tiki-section_options.php');

// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');

$smarty->assign('mid', 'tiki-edit_topic.tpl');
$smarty->display("tiki.tpl");
