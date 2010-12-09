<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once ('tiki-setup.php');
include_once ('lib/commcenter/commlib.php');
include_once ('lib/articles/artlib.php');
$access->check_feature('feature_comm');
$access->check_permission('tiki_p_admin_received_articles');

if (!isset($_REQUEST["receivedArticleId"])) {
	$_REQUEST["receivedArticleId"] = 0;
}
$smarty->assign('receivedArticleId', $_REQUEST["receivedArticleId"]);
if ($_REQUEST["receivedArticleId"]) {
	$info = $commlib->get_received_article($_REQUEST["receivedArticleId"]);
	$info["topic"] = 1;
} else {
	$info = array();
	$info["title"] = '';
	$info["authorName"] = '';
	$info["size"] = 0;
	$info["useImage"] = 'n';
	$info["image_name"] = '';
	$info["image_type"] = '';
	$info["image_size"] = 0;
	$info["image_x"] = 0;
	$info["image_y"] = 0;
	$info["image_data"] = '';
	$info["publishDate"] = $tikilib->now;
	$cur_time = explode(',', $tikilib->date_format('%Y,%m,%d,%H,%M,%S', $publishDate));
	$info["expireDate"] = $tikilib->make_time($cur_time[3], $cur_time[4], $cur_time[5], $cur_time[1], $cur_time[2], $cur_time[0] + 1);
	$info["created"] = $tikilib->now;
	$info["heading"] = '';
	$info["body"] = '';
	$info["hash"] = '';
	$info["author"] = '';
	$info["topic"] = 1;
	$info["type"] = 'Article';
	$info["rating"] = 5;
}
$smarty->assign('view', 'n');
if (isset($_REQUEST["view"])) {
	$info = $tikilib->get_received_article($_REQUEST["view"]);
	$smarty->assign('view', 'y');
	$info["topic"] = 1;
}
if (isset($_REQUEST["accept"])) {
	check_ticket('received-articles');
	// CODE TO ACCEPT A PAGE HERE
	$publishDate = $tikilib->make_time($_REQUEST["Time_Hour"], $_REQUEST["Time_Minute"], 0, $_REQUEST["Date_Month"], $_REQUEST["Date_Day"], $_REQUEST["Date_Year"]);
	$expireDate = $tikilib->make_time($_REQUEST["expire_Hour"], $_REQUEST["expire_Minute"], 0, $_REQUEST["expire_Month"], $_REQUEST["expire_Day"], $_REQUEST["expire_Year"]);
	$commlib->update_received_article($_REQUEST["receivedArticleId"], $_REQUEST["title"], $_REQUEST["authorName"], $_REQUEST["useImage"], $_REQUEST["image_x"], $_REQUEST["image_y"], $publishDate, $expireDate, $_REQUEST["heading"], $_REQUEST["body"], $_REQUEST["type"], $_REQUEST["rating"]);
	$commlib->accept_article($_REQUEST["receivedArticleId"], $_REQUEST["topic"]);
	$smarty->assign('preview', 'n');
	$smarty->assign('receivedArticleId', 0);
}
$smarty->assign('preview', 'n');
$smarty->assign('topic', $info["topic"]);
if (isset($_REQUEST["preview"])) {
	$smarty->assign('preview', 'y');
	$info["publishDate"] = $tikilib->make_time($_REQUEST["Time_Hour"], $_REQUEST["Time_Minute"], 0, $_REQUEST["Date_Month"], $_REQUEST["Date_Day"], $_REQUEST["Date_Year"]);
	$info["expireDate"] = $tikilib->make_time($_REQUEST["expire_Hour"], $_REQUEST["expire_Minute"], 0, $_REQUEST["expire_Month"], $_REQUEST["expire_Day"], $_REQUEST["expire_Year"]);
	$info["title"] = $_REQUEST["title"];
	$info["authorName"] = $_REQUEST["authorName"];
	$info["receivedArticleId"] = $_REQUEST["receivedArticleId"];
	$info["useImage"] = $_REQUEST["useImage"];
	$info["image_name"] = $_REQUEST["image_name"];
	$info["image_size"] = $_REQUEST["image_size"];
	$info["image_x"] = $_REQUEST["image_x"];
	$info["image_y"] = $_REQUEST["image_y"];
	$info["created"] = $_REQUEST["created"];
	$info["heading"] = $_REQUEST["heading"];
	$info["body"] = $_REQUEST["body"];
	$info["topic"] = $_REQUEST["topic"];
	$info["type"] = $_REQUEST["type"];
	$info["rating"] = $_REQUEST["rating"];
}
$smarty->assign('topic', $info["topic"]);
$smarty->assign('title', $info["title"]);
$smarty->assign('authorName', $info["authorName"]);
$smarty->assign('useImage', $info["useImage"]);
$smarty->assign('image_name', $info["image_name"]);
$smarty->assign('image_size', $info["image_size"]);
$smarty->assign('image_x', $info["image_x"]);
$smarty->assign('image_y', $info["image_y"]);
$smarty->assign('publishDate', $info["publishDate"]);
$smarty->assign('expireDate', $info["expireDate"]);
$smarty->assign('created', $info["created"]);
$smarty->assign('heading', $info["heading"]);
$smarty->assign('body', $info["body"]);
$smarty->assign('type', $info["type"]);
$smarty->assign('rating', $info["rating"]);
// Assign parsed
$smarty->assign('parsed_heading', $tikilib->parse_data($info["heading"]));
$smarty->assign('parsed_body', $tikilib->parse_data($info["body"]));
if (isset($_REQUEST["remove"])) {
	$access->check_authenticity();
	$commlib->remove_received_article($_REQUEST["remove"]);
}
if (isset($_REQUEST["save"])) {
	check_ticket('received-articles');
	$publishDate = $tikilib->make_time($_REQUEST["Time_Hour"], $_REQUEST["Time_Minute"], 0, $_REQUEST["Date_Month"], $_REQUEST["Date_Day"], $_REQUEST["Date_Year"]);
	$expireDate = $tikilib->make_time($_REQUEST["expire_Hour"], $_REQUEST["expire_Minute"], 0, $_REQUEST["Date_Month"], $_REQUEST["Date_Day"], $_REQUEST["Date_Year"]);
	$commlib->update_received_article($_REQUEST["receivedArticleId"], $_REQUEST["title"], $_REQUEST["authorName"], $_REQUEST["useImage"], $_REQUEST["image_x"], $_REQUEST["image_y"], $publishDate, $expireDate, $_REQUEST["heading"], $_REQUEST["body"]);
	$smarty->assign('receivedArticleId', $_REQUEST["receivedArticleId"]);
	$smarty->assign('title', $_REQUEST["title"]);
	$smarty->assign('authorName', $_REQUEST["authorName"]);
	$smarty->assign('size', strlen($_REQUEST["body"]));
	$smarty->assign('useImage', $_REQUEST["useImage"]);
	$smarty->assign('image_x', $_REQUEST["image_x"]);
	$smarty->assign('image_y', $_REQUEST["image_y"]);
	$smarty->assign('publishDate', $publishDate);
	$smarty->assign('expireDate', $expireDate);
	$smarty->assign('heading', $_REQUEST["heading"]);
	$smarty->assign('body', $_REQUEST["body"]);
}
if (!isset($_REQUEST["sort_mode"])) {
	$sort_mode = 'receivedDate_desc';
} else {
	$sort_mode = $_REQUEST["sort_mode"];
}
if (!isset($_REQUEST["offset"])) {
	$offset = 0;
} else {
	$offset = $_REQUEST["offset"];
}
$smarty->assign_by_ref('offset', $offset);
if (isset($_REQUEST["find"])) {
	$find = $_REQUEST["find"];
} else {
	$find = '';
}
$smarty->assign('find', $find);
$smarty->assign_by_ref('sort_mode', $sort_mode);
$channels = $commlib->list_received_articles($offset, $maxRecords, $sort_mode, $find);
$smarty->assign_by_ref('channels', $channels["data"]);
$smarty->assign_by_ref('cant', $channels["cant"]);
$topics = $artlib->list_topics();
$smarty->assign_by_ref('topics', $topics);
$types = $artlib->list_types();
$smarty->assign_by_ref('types', $types);
ask_ticket('received-articles');
// Display the template
$smarty->assign('mid', 'tiki-received_articles.tpl');
$smarty->display("tiki.tpl");
