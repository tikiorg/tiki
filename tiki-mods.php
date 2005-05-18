<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-mods.php,v 1.6 2005-05-18 10:58:58 mose Exp $

// Copyright (c) 2002-2005, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');
include('lib/mods/modslib.php');

if ($tiki_p_admin != 'y') {
	$smarty->assign('msg', tra("You do not have permission to use this feature"));
	$smarty->display("error.tpl");
	die;
}

if (!is_dir($mods_dir)) {
	@mkdir($mods_dir,02777);
}

if (!is_writable($mods_dir)) {
	$smarty->assign('msg', tra("You need to run setup.sh :<br />./setup.sh \$APACHEUSER all<br />Common Apache users are www-data, apache or nobody"));
	$smarty->display("error.tpl");
	die;
}

if (!is_writable('tiki-index.php')) {
	$iswritable = false;
} else {
	$iswritable = true;
}
$smarty->assign('iswritable', $iswritable);

if (!is_dir($mods_dir."/Packages")) {
	mkdir($mods_dir."/Packages",02777);
}
if (!is_dir($mods_dir."/Installed")) {
	mkdir($mods_dir."/Installed",02777);
}
if (!is_dir($mods_dir."/Cache")) {
	mkdir($mods_dir."/Cache",02777);
}

if (isset($_REQUEST['find']) and trim($_REQUEST['find'])) {
	$findarg = '&amp;find='. urlencode($_REQUEST['find']);
	$find = $_REQUEST['find'];
} else {
	$findarg = $find = '';
}
$smarty->assign('findarg', $findarg);
$smarty->assign('find', $find);

if (isset($_REQUEST['type']) and trim($_REQUEST['type'])) {
	$typearg = '&amp;type='. urlencode($_REQUEST['type']);
	$type = $_REQUEST['type'];
} else {
	$type = $typearg = '';
}
$smarty->assign('typearg', $typearg);
$smarty->assign('type', $type);


if ($feature_mods_provider == 'y') {
	if (!is_dir($mods_dir."/Dist")) {
	  mkdir($mods_dir."/Dist",02777);
	}
	if (!is_file($mods_dir."/Packages/00_list.public.txt")) {
		touch($mods_dir."/Packages/00_list.public.txt");
	}
	$public = $modslib->read_list($mods_dir."/Packages/00_list.public.txt",$type,$find);
	if (isset($_REQUEST['republish'])) {
		$modslib->unpublish($mods_dir."/Packages",$public,array($_REQUEST['republish']));
		$modslib->publish($mods_dir."/Packages",$public,array($_REQUEST['republish']));
	} elseif (isset($_REQUEST['republishall'])) {
		$modslib->unpublish($mods_dir."/Packages",$public,$modslib->read_list($mods_dir."/Packages/00_list.txt",$type,$find,'modname'));
		$modslib->publish($mods_dir."/Packages",$public,$modslib->read_list($mods_dir."/Packages/00_list.txt",$type,$find,'modname'));
	} elseif (isset($_REQUEST['publish'])) {
		$modslib->publish($mods_dir."/Packages",$public,array($_REQUEST['publish']));
	} elseif (isset($_REQUEST['publishall'])) {
		$modslib->publish($mods_dir."/Packages",$public,$modslib->read_list($mods_dir."/Packages/00_list.txt",$type,$find,'modname'));
	} elseif (isset($_REQUEST['unpublish'])) {
		$modslib->unpublish($mods_dir."/Packages",$public,array($_REQUEST['unpublish']));
	} elseif (isset($_REQUEST['unpublishall'])) {
		$modslib->unpublish($mods_dir."/Packages",$public,$modslib->read_list($mods_dir."/Packages/00_list.txt",$type,$find,'modname'));
	}
	$smarty->assign('public', $public);
}

if (isset($_REQUEST['dl'])) {
        if(!function_exists("gzinflate")) {
	    $smarty->assign('msg',tra("Your PHP installation does not have zlib enabled."));
	    $smarty->display('error.tpl');
	    die;
	}
	$modslib->dl_remote($mods_server,$_REQUEST['dl'],$mods_dir);
	$modslib->rebuild_list($mods_dir."/Packages");
}

if (!is_file($mods_dir."/Packages/00_list.txt") or isset($_REQUEST['rebuild'])) {
	$modslib->rebuild_list($mods_dir."/Packages");
	$modslib->rebuild_list($mods_dir."/Installed");
}

if ($mods_server) {
	if (!is_file($mods_dir."/Packages/00_list.". urlencode($mods_server) .".txt")) {
		touch($mods_dir."/Packages/00_list.". urlencode($mods_server) .".txt");
		$_REQUEST['reload'] = true;
	}
	if (isset($_REQUEST['reload'])) {
		$modslib->refresh_remote($mods_server."/Packages/00_list.public.txt",$mods_dir."/Packages/00_list.". urlencode($mods_server). ".txt");
	}
}

if (isset($_REQUEST['package'])) {
	$packtype = substr($_REQUEST['package'],0,strpos($_REQUEST['package'],'-'));
	$package = substr($_REQUEST['package'],strpos($_REQUEST['package'],'-')+1);	
	$smarty->assign('packtype', $packtype);
	$smarty->assign('package', $package);
}

if (isset($_REQUEST['action']) and isset($package) and $iswritable) {
	if ($_REQUEST['action'] == 'configuration') {
		$conf = '';
		foreach ($_REQUEST['conf'] as $k=>$v) {
			$conf.= "$k:\n$v\n\n";
		}
		$modslib->write_conf($mods_dir,$packtype,$package,$_REQUEST['conf']);
		$_REQUEST['action'] = 'upgrade';
	}
	if ($_REQUEST['action'] == 'remove') {
		$modslib->remove($mods_dir,$packtype,$package);
	} elseif ($_REQUEST['action'] == 'upgrade') {
		$modslib->upgrade($mods_dir,$packtype,$package);
	} elseif ($_REQUEST['action'] == 'install') {
		$modslib->install($mods_dir,$packtype,$package);
	}
}

$local = $modslib->read_list($mods_dir."/Packages/00_list.txt",$type,$find);
$smarty->assign('local', $local);

$remote = $modslib->read_list($mods_dir."/Packages/00_list.". urlencode($mods_server).".txt",$type,$find);
$smarty->assign('remote', $remote);

$installed = $modslib->read_list($mods_dir."/Installed/00_list.txt",$type,$find);
$smarty->assign('installed', $installed);

if ($feature_mods_provider == 'y') {
	$public = $modslib->read_list($mods_dir."/Packages/00_list.public.txt",$type,$find);
	$smarty->assign('public', $public);
	$dist = $modslib->scan_dist($mods_dir."/Dist");
	$smarty->assign('dist', $dist);
}

$types = $modslib->types;
$display = array();

if ($type) {
        if (!isset($local[$type])) $local[$type] = array();
	if (!isset($remote[$type])) $remote[$type] = array();

	$display[$type] = array_merge($local[$type],$remote[$type]);
} else {
	foreach ($types as $t=>$tt) {
		if (isset($local[$t])) {
			if (isset($remote[$t])) {
				$display[$t] = array_merge($local[$t],$remote[$t]);
			} else {
				$display[$t] = $local[$t];
			}
		} elseif (isset($remote[$t])) {
			$display[$t] = $remote[$t];
		}
	}
}
$smarty->assign('display', $display);

if (isset($_REQUEST['focus'])) {
	$focus = $_REQUEST['focus'];
	$more = $modslib->readconf($mods_dir.'/Packages/'.$focus.'.info.txt');
} else {
	$focus = false;
	$more = array();
}

$smarty->assign('focus', $focus);
$smarty->assign('more', $more);
$smarty->assign('tikifeedback', $modslib->feedback);
$smarty->assign('types', $types);

$smarty->assign('mid', 'tiki-mods.tpl');
$smarty->display("tiki.tpl");

?>
