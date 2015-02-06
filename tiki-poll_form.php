<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$section = 'poll';
require_once ('tiki-setup.php');
$polllib = TikiLib::lib('poll');
$access->check_feature('feature_polls');
$access->check_permission('tiki_p_vote_poll');
if (empty($_REQUEST["pollId"])) {
	$smarty->assign('msg', tra("No poll indicated"));
	$smarty->display("error.tpl");
	die;
}
$poll_info = $polllib->get_poll($_REQUEST["pollId"]);
if (empty($poll_info)) {
        $smarty->assign('msg', tra("No poll indicated"));
        $smarty->display("error.tpl");
        die;
}
$options = $polllib->list_poll_options($_REQUEST["pollId"]);
$smarty->assign_by_ref('menu_info', $poll_info);
$smarty->assign_by_ref('channels', $options);
$smarty->assign('ownurl', $tikilib->httpPrefix() . $_SERVER["REQUEST_URI"]);
ask_ticket('poll-form');
// Display the template
$smarty->assign('title', $poll_info['title']);
$smarty->assign('mid', 'tiki-poll_form.tpl');
$smarty->display("tiki.tpl");
