<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-mods.php,v 1.4 2005-01-05 19:22:42 jburleyebuilt Exp $

// Copyright (c) 2002-2005, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');
include('lib/mods/modslib.php');

$tikifeedback = array();

if ($tiki_p_admin != 'y') {
	$smarty->assign('msg', tra("You do not have permission to use this feature"));
	$smarty->display("error.tpl");
	die;
}

$host = "doc/mods/";
$master = 'http://tikiwiki.org/mods/';

if (!is_dir($host) or !is_writable($host)) {
	$smarty->assign('msg', tra("You need to run setup.sh :<br />./setup.sh \$APACHEUSER all<br />Common Apache users are www-data, apache or nobody"));
	$smarty->display("error.tpl");
	die;
}

if (isset($_REQUEST['type'])) {
	$type = $_REQUEST['type'];
} else {
	$type = 'all';
}

if (!is_dir($host."Packages")) {
	mkdir($host."Packages",02777);
}
if (!is_dir($host."Installed")) {
	mkdir($host."Packages",02777);
}
if (!is_file($host."Packages/30_list.public.txt")) {
	touch($host."Packages/30_list.public.txt");
}

$public = $modslib->read_list($host."Packages/30_list.public.txt");
if (isset($_REQUEST['publish'])) {
	$modslib->publish($host."Packages",$public,$_REQUEST['publish']);
	$public = $modslib->read_list($host."Packages/30_list.public.txt");
}
if (isset($_REQUEST['unpublish'])) {
	$modslib->unpublish($host."Packages",$public,$_REQUEST['unpublish']);
	$public = $modslib->read_list($host."Packages/30_list.public.txt");
}
if (isset($_REQUEST['dl'])) {
	
}

if (!is_file($host."Packages/00_list.txt") or isset($_REQUEST['rebuild'])) {
	$modslib->rebuild_list($host."Packages");
	$modslib->rebuild_list($host."Installed");
}
if (!is_file($host."Packages/10_index.txt") or isset($_REQUEST['refresh'])) {
	if (isset($_REQUEST['master'])) {
		$master = $_REQUEST['master'];
	}
	$modslib->refresh_remote($host."Packages/",$master."Packages/","10_index.txt");
}
$masters = file($host."Packages/10_index.txt");
foreach ($masters as $mas) {
	$mas = trim($mas);
	if (!is_file($host."Packages/20_list.". urlencode($mas) .".txt") or (isset($_REQUEST['reload']) and $_REQUEST['reload'] == $mas)) {
		$modslib->refresh_remote($host."Packages/",$mas."Packages/","30_list.public.txt","20_list.". urlencode($mas). ".txt");
	}
	$remote_masters[] = $mas;
}
if (isset($_REQUEST['action'])) {
	if ($_REQUEST['action'] == 'configuration') {
		$conf = '';
		foreach ($_REQUEST['conf'] as $k=>$v) {
			$conf.= "$k:\n$v\n\n";
		}
		$modslib->write_conf($host,$type,$_REQUEST['package'],$_REQUEST['conf']);
		$_REQUEST['action'] = 'upgrade';
	}
	if ($_REQUEST['action'] == 'remove') {
		$modslib->remove($host,$type,$_REQUEST['package']);
	} elseif ($_REQUEST['action'] == 'upgrade') {
		$modslib->upgrade($host,$type,$_REQUEST['package']);
	} elseif ($_REQUEST['action'] == 'install') {
		$modslib->install($host,$type,$_REQUEST['package']);
	}
}

$list = $modslib->read_list($host."Packages/00_list.txt");
$installed = $modslib->read_list($host."Installed/00_list.txt");
$remote = $modslib->read_list($host."Packages/20_list.". urlencode($master).".txt");
if ($type == 'all') {
	$display = $list;
} elseif ($type) {
	$display["$type"] = $list["$type"];
}

if (isset($_REQUEST['focus'])) {
	$focus = $_REQUEST['focus'];
	$more = $modslib->readconf($host.'Packages/'.$type.'-'.$focus.'.info.txt');
} else {
	$focus = false;
	$more = array();
}
$smarty->assign('remote_masters', $remote_masters);
$smarty->assign('master', $master);
$smarty->assign('focus', $focus);
$smarty->assign('more', $more);
$smarty->assign('type', $type);

$smarty->assign('list', $list);
$smarty->assign('installed', $installed);
$smarty->assign('remote', $remote);
$smarty->assign('public', $public);
$smarty->assign('display', $display);

$smarty->assign('mid', 'tiki-mods.tpl');
$smarty->display("tiki.tpl");

?>
