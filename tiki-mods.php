<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once ('tiki-setup.php');
include_once ('lib/mods/modslib.php');
$access->check_permission('tiki_p_admin');
if (!is_dir($prefs['mods_dir'])) {
	@mkdir($prefs['mods_dir'], 02777);
}
if (!is_writable($prefs['mods_dir'])) {
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
if (!is_dir($prefs['mods_dir'] . "/Packages")) {
	mkdir($prefs['mods_dir'] . "/Packages", 02777);
}
if (!is_dir($prefs['mods_dir'] . "/Installed")) {
	mkdir($prefs['mods_dir'] . "/Installed", 02777);
}
if (!is_dir($prefs['mods_dir'] . "/Cache")) {
	mkdir($prefs['mods_dir'] . "/Cache", 02777);
}
$feedback = array();
/**
 * @param $num
 * @param $err
 */
function tikimods_feedback_listener($num, $err)
{
	global $feedback;
	$feedback[] = array('num' => $num, 'mes' => $err);
}
$modslib->add_feedback_listener('tikimods_feedback_listener');
if (isset($_REQUEST['find']) and trim($_REQUEST['find'])) {
	$findarg = '&amp;find=' . urlencode($_REQUEST['find']);
	$find = $_REQUEST['find'];
} else {
	$findarg = $find = '';
}
$smarty->assign('findarg', $findarg);
$smarty->assign('find', $find);
if (isset($_REQUEST['type']) and trim($_REQUEST['type'])) {
	$typearg = '&amp;type=' . urlencode($_REQUEST['type']);
	$type = $_REQUEST['type'];
} else {
	$type = $typearg = '';
}
$smarty->assign('typearg', $typearg);
$smarty->assign('type', $type);
if (isset($_REQUEST['version']) and trim($_REQUEST['version'])) {
	$versionarg = '&amp;version=' . urlencode($_REQUEST['version']);
	$version = $_REQUEST['version'];
} else {
	$version = '7';
	$versionarg = '&amp;version=7';
}
$smarty->assign('versionarg', $versionarg);
$smarty->assign('version', $version);
if ($prefs['feature_mods_provider'] == 'y') {
	if (!is_dir($prefs['mods_dir'] . "/Dist")) {
		mkdir($prefs['mods_dir'] . "/Dist", 02777);
	}
	if (!is_file($prefs['mods_dir'] . "/Packages/00_list.public.txt")) {
		touch($prefs['mods_dir'] . "/Packages/00_list.public.txt");
	}
	$public = $modslib->read_list($prefs['mods_dir'] . "/Packages/00_list.public.txt", 'remote', $type, $find, false);
	if (isset($_REQUEST['republish'])) {
		$modslib->unpublish($prefs['mods_dir'], array($_REQUEST['republish']));
		$modslib->publish($prefs['mods_dir'], array($_REQUEST['republish']));
	} elseif (isset($_REQUEST['republishall'])) {
		$items = $modslib->read_list($prefs['mods_dir'] . "/Packages/00_list.txt", 'local', $type, $find, true);
		$modslib->unpublish($prefs['mods_dir'], $items);
		$modslib->publish($prefs['mods_dir'], $items);
	} elseif (isset($_REQUEST['publish'])) {
		$modslib->publish($prefs['mods_dir'], array($_REQUEST['publish']));
	} elseif (isset($_REQUEST['publishall'])) {
		$modslib->publish($prefs['mods_dir'], $modslib->read_list($prefs['mods_dir'] . "/Packages/00_list.txt", 'local', $type, $find, true));
	} elseif (isset($_REQUEST['unpublish'])) {
		$modslib->unpublish($prefs['mods_dir'], array($_REQUEST['unpublish']));
	} elseif (isset($_REQUEST['unpublishall'])) {
		$items = $modslib->read_list($prefs['mods_dir'] . "/Packages/00_list.public.txt", 'public', $type, $find, true);
		$modslib->unpublish($prefs['mods_dir'], $items);
	}
	$smarty->assign('public', $public);
}
if (isset($_REQUEST['dl'])) {
	if (!function_exists("gzinflate")) {
		$smarty->assign('msg', tra("Your PHP installation does not have zlib enabled."));
		$smarty->display('error.tpl');
		die;
	}
	$modslib->dl_remote($prefs['mods_server'], $_REQUEST['dl'], $prefs['mods_dir']);
	$modslib->rebuild_list($prefs['mods_dir'] . "/Packages");
}
if (!is_file($prefs['mods_dir'] . "/Packages/00_list.txt") or isset($_REQUEST['rebuild'])) {
	$modslib->rebuild_list($prefs['mods_dir'] . "/Packages");
	$modslib->rebuild_list($prefs['mods_dir'] . "/Installed");
}
if ($prefs['mods_server']) {
	if (!is_file($prefs['mods_dir'] . "/Packages/00_list." . urlencode($prefs['mods_server']) . ".txt")) {
		touch($prefs['mods_dir'] . "/Packages/00_list." . urlencode($prefs['mods_server']) . ".txt");
		$_REQUEST['reload'] = true;
	}
	if (isset($_REQUEST['reload'])) {
		$modslib->refresh_remote($prefs['mods_server'] . "/Packages/00_list.public.txt", $prefs['mods_dir'] . "/Packages/00_list." . urlencode($prefs['mods_server']) . ".txt");
	}
}
if (isset($_REQUEST['package'])) {
	$packtype = substr($_REQUEST['package'], 0, strpos($_REQUEST['package'], '-'));
	$package = substr($_REQUEST['package'], strpos($_REQUEST['package'], '-') + 1);
	$smarty->assign('packtype', $packtype);
	$smarty->assign('package', $package);
}
if (isset($_REQUEST['action']) and isset($package) and $iswritable) {
	if ($_REQUEST['action'] == 'configuration') {
		$mod = new TikiModInfo($packtype, $package);
		$mod->writeconf($prefs['mods_dir'], $_REQUEST['conf']);
		$_REQUEST['action'] = 'upgrade';
	}
	if ($_REQUEST['action'] == 'remove') {
		$deps = $modslib->find_deps_remove($prefs['mods_dir'], $prefs['mods_server'], array($packtype . '-' . $package));
		$smarty->assign('installask', $deps);
	} elseif (($_REQUEST['action'] == 'install') || ($_REQUEST['action'] == 'upgrade')) {
		$deps = $modslib->find_deps($prefs['mods_dir'], $prefs['mods_server'], array($packtype . '-' . $package));
		$smarty->assign('installask', $deps);
	}
} elseif (isset($_REQUEST['button-check'])) {
	$deps = $modslib->find_deps($prefs['mods_dir'], $prefs['mods_server'], $_REQUEST['install-wants']);
	$smarty->assign('installask', $deps);
} elseif (isset($_REQUEST['button-install'])) {
	$deps = $modslib->find_deps($prefs['mods_dir'], $prefs['mods_server'], $_REQUEST['install-wants']);
	$modslib->install_with_deps($prefs['mods_dir'], $prefs['mods_server'], $deps);
} elseif (isset($_REQUEST['button-remove'])) {
	$deps = $modslib->find_deps_remove($prefs['mods_dir'], $prefs['mods_server'], $_REQUEST['install-wants']);
	$modslib->remove_with_deps($prefs['mods_dir'], $prefs['mods_server'], $deps);
}
$local = $modslib->read_list($prefs['mods_dir'] . "/Packages/00_list.txt", 'local', $type, $find, false);
$smarty->assign('local', $local);
$remote = $modslib->read_list($prefs['mods_dir'] . "/Packages/00_list." . urlencode($prefs['mods_server']) . ".txt", 'remote', $type, $find, false);
$smarty->assign('remote', $remote);
$installed = $modslib->read_list($prefs['mods_dir'] . "/Installed/00_list.txt", 'installed', $type, $find, false);
$smarty->assign('installed', $installed);

if ($prefs['feature_mods_provider'] == 'y') {
	$public = $modslib->read_list($prefs['mods_dir'] . "/Packages/00_list.public.txt", 'public', $type, $find, false);
	$smarty->assign('public', $public);
	$dist = $modslib->scan_dist($prefs['mods_dir'] . "/Dist");
	$smarty->assign('dist', $dist);
}
$types = $modslib->types;
$versions = $modslib->versions;
$display = array();
if ($type) {
	if (!isset($local[$type])) $local[$type] = array();
	if (!isset($remote[$type])) $remote[$type] = array();
	$display[$type] = array_merge($local[$type], $remote[$type]);
} else {
	foreach ($types as $t => $tt) {
		if (isset($local[$t])) {
			if (isset($remote[$t])) {
				$display[$t] = array_merge($local[$t], $remote[$t]);
			} else {
				$display[$t] = $local[$t];
			}
		} elseif (isset($remote[$t])) {
			$display[$t] = $remote[$t];
		}
	}
}
if (!empty($version)) { // filter out other versions
	$filtered = array();
	if ($version == - 1) {
		foreach ($display as $t => $ms) {
			$filtmod = array();
			foreach ($ms as $k => $m) {
				if (empty($m->version[0])) {
					$filtmod[$k] = $m;
				}
			}
			if (count(array_keys($filtmod)) > 0) {
				$filtered[$t] = $filtmod;
			}
		}
	} else {
		$v = floatval($version);
		foreach ($display as $t => $ms) {
			$filtmod = array();
			foreach ($ms as $k => $m) {
				$mv = floatval($m->version[0]);
				// TODO - fix the data, but for the mean time...
				if (strpos($m->version[0], '1.10') !== false || strpos($m->version[0], ' 2 ') !== false) {
					$mv = 2.0; // 1.10 was renumbered 2.0 - or version= "Compatible with TikiWiki 2 releases."
					
				}
				if (strpos($m->version[0], ' 3 ') !== false || strpos($m->version[0], ' 3+') !== false) {
					$mv = 3.0; // e.g. version= "Compatible with TikiWiki 3 releases." or "3+"
					
				}
				if (strpos($m->version[0], ' 4 ') !== false || strpos($m->version[0], ' 4+') !== false) {
					$mv = 4.0; // e.g. version= "Compatible with TikiWiki 4 releases." or "4+"
					
				}
				if (strpos($m->version[0], ' 5 ') !== false || strpos($m->version[0], ' 5+') !== false) {
					$mv = 5.0; // e.g. version= "Compatible with TikiWiki 5 releases." or "5+"
					
				}
				if (strpos($m->version[0], ' 6 ') !== false || strpos($m->version[0], ' 6+') !== false) {
					$mv = 6.0; // e.g. version= "Compatible with Tiki 6 releases." or "6+"
					
				}
				
				if (strpos($m->version[0], ' 7 ') !== false || strpos($m->version[0], ' 7+') !== false) {
					$mv = 7.0; // e.g. version= "Compatible with Tiki 7 releases." or "7+"
					
				}

				if (strpos($m->version[0], ' 8 ') !== false || strpos($m->version[0], ' 8+') !== false) {
					$mv = 8.0; // e.g. version= "Compatible with Tiki 8 releases." or "8+"
					
				}				

				if (strpos($m->version[0], ' 9 ') !== false || strpos($m->version[0], ' 9+') !== false) {
					$mv = 9.0; // e.g. version= "Compatible with Tiki 9 releases." or "9+"

				}

				if (strpos($m->version[0], ' 10 ') !== false || strpos($m->version[0], ' 10+') !== false) {
					$mv = 10.0; // e.g. version= "Compatible with Tiki 10 releases." or "10+"

				}

				if (strpos($m->version[0], ' 11 ') !== false || strpos($m->version[0], ' 11+') !== false) {
					$mv = 11.0; // e.g. version= "Compatible with Tiki 11 releases." or "11+"

				}

				if (strpos($m->version[0], ' 12 ') !== false || strpos($m->version[0], ' 12+') !== false) {
					$mv = 12.0; // e.g. version= "Compatible with Tiki 12 releases." or "12+"

				}

				if ($mv >= $v) {
					$filtmod[$k] = $m;
				}
			}
			if (count(array_keys($filtmod)) > 0) {
				$filtered[$t] = $filtmod;
			}
		}
	}
	$display = $filtered;
}
$smarty->assign('display', $display);
if (isset($_REQUEST['focus'])) {
	$focus = $_REQUEST['focus'];
	$more = new TikiModInfo($focus);
	$err = $more->readinfo($prefs['mods_dir'] . '/Packages/' . $focus . '.info.txt');
	if ($err !== false) die($err);
} else {
	$focus = false;
	$more = array();
}
$smarty->assign('focus', $focus);
$smarty->assign('more', $more);
$smarty->assign('tikifeedback', $feedback);
$smarty->assign('types', $types);
$smarty->assign('versions', $versions);
$smarty->assign('mid', 'tiki-mods.tpl');
$smarty->display("tiki.tpl");
