<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once ('tiki-setup.php');
$bloglib = TikiLib::lib('blog');
$rsslib = TikiLib::lib('rss');

if ($prefs['feed_blogs'] != 'y' && $prefs['feed_blog'] != 'y') {
	$errmsg = tra('rss feed disabled');
	require_once ('tiki-rss_error.php');
}

if (!isset($_REQUEST['blogId'])) {
	$errmsg = tra('No blogId specified');
	require_once ('tiki-rss_error.php');
}

$smarty->assign('individual', 'n');
$tikilib->get_perm_object($_REQUEST['blogId'], 'blog');

if ($tiki_p_read_blog != 'y') {
	$smarty->assign('errortype', 401);
	$errmsg = tra('You do not have permission to view this section');
	require_once ('tiki-rss_error.php');
}

$feed = 'blog';
$id = 'blogId';
$uniqueid = "$feed.$id=" . $_REQUEST["$id"];
$output = $rsslib->get_from_cache($uniqueid);

if ($output['data'] == 'EMPTY') {
	$tmp = $bloglib->get_blog($_REQUEST["$id"]);
	$title = $prefs['feed_' . $feed . '_title'];
	$title.= $tmp['title'];
	$desc = $prefs['feed_' . $feed . '_desc'];
	$desc.= $tmp['description'];
	$dateId = 'created';
	$authorId = 'user';
	$titleId = 'title';
	$readrepl = 'tiki-view_blog_post.php?postId=%s';
	$changes = $bloglib->list_blog_posts($_REQUEST["$id"], false, 0, $prefs['feed_blog_max'], $dateId . '_desc', '', '', $tikilib->now);
	$tmp = array();
	include_once ('tiki-sefurl.php');
	foreach ($changes['data'] as $data) {
		$data['data'] = $tikilib->parse_data(
			$data['data'],
			array(
				'print' => true,
				'is_html' => $data['wysiwyg'] === 'y' && $prefs['wysiwyg_htmltowiki'] !== 'y',
			)
		);
		$data['sefurl'] = filter_out_sefurl(sprintf($readrepl, $data['postId']), 'blogpost', urlencode($data['title']));
		$tmp[] = $data;
	}
	$changes['data'] = $tmp;
	$tmp = null;
	$output = $rsslib->generate_feed(
		$feed,
		$uniqueid,
		'',
		$changes,
		$readrepl,
		'blogId',
		'',
		$title,
		$titleId,
		$desc,
		'data',
		$dateId,
		$authorId,
		false
	);
}
header('Content-type: ' . $output['content-type']);
print $output['data'];
