<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once ('tiki-setup.php');
// do we need it?
require_once ('lib/admin/adminlib.php');
$access->check_permission('tiki_p_admin');

// get all dangerous php settings and check them
$phpsettings = array();
// register globals
$s = ini_get('register_globals');
if ($s) {
	$phpsettings['register_globals'] = array(
		'risk' => tra('unsafe') ,
		'setting' => $s,
		'message' => tra('register_globals should be off by default. See the php manual for details.')
	);
} else {
	$phpsettings['register_globals'] = array(
		'risk' => tra('safe') ,
		'setting' => $s
	);
}
$fcts = array(
	'exec',
	'passthru',
	'shell_exec',
	'system',
	'proc_open',
	'curl_exec',
	'curl_multi_exec',
	'parse_ini_file',
	'show_source'
);
foreach($fcts as $fct) {
	if (function_exists($fct)) {
		$phpfunctions[$fct] = array(
			'setting' => tr('on') ,
			'risk' => tra('risky')
		);
	} else {
		$phpfunctions[$fct] = array(
			'setting' => tr('off') ,
			'risk' => tra('safe')
		);
	}
}
$smarty->assign_by_ref('phpfunctions', $phpfunctions);
// trans_sid
$s = ini_get('session.use_trans_sid');
if ($s) {
	$phpsettings['session.use_trans_sid'] = array(
		'risk' => tra('unsafe') ,
		'setting' => $s,
		'message' => tra('session.use_trans_sid should be off by default. See the php manual for details.')
	);
} else {
	$phpsettings['session.use_trans_sid'] = array(
		'risk' => tra('safe') ,
		'setting' => $s
	);
}
// check file upload dir and compare it to tiki root dir
$s = ini_get('upload_tmp_dir');
$sn = substr($_SERVER['SCRIPT_NAME'], 0, -23);
if (strpos($sn, $s) !== FALSE) {
	$phpsettings['upload_tmp_dir'] = array(
		'risk' => tra('unsafe') ,
		'setting' => $s,
		'message' => tra('upload_tmp_dir is probably within your Tikiwiki directory. There is a risk that someone can upload any file to this directory and access them via web browser')
	);
} else {
	$phpsettings['upload_tmp_dir'] = array(
		'risk' => tra('unknown') ,
		'setting' => $s,
		'message' => tra('cannot check if the upload_tmp_dir is accessible via web browser. To be sure you should check your webserver config.')
	);
}
$s = ini_get('xbithack');
if ($s == 1) {
	$phpsettings['xbithack'] = array(
		'risk' => tra('unsafe') ,
		'setting' => $s,
		'message' => tra('setting the xbithack option is unsafe. Depending on the file handling of your webserver and your tiki settings, it may be possible that a attacker can upload scripts to file gallery and execute them')
	);
} else {
	$phpsettings['xbithack'] = array(
		'risk' => tra('safe') ,
		'setting' => $s
	);
}
$s = ini_get('allow_url_fopen');
if ($s == 1) {
	$phpsettings['allow_url_fopen'] = array(
		'risk' => tra('risky') ,
		'setting' => $s,
		'message' => tra('allow_url_fopen may potentially be used to upload remote data or scripts. If you dont use the blog feature, you can switch it off.')
	);
} else {
	$phpsettings['allow_url_fopen'] = array(
		'risk' => tra('safe') ,
		'setting' => $s
	);
}
ksort($phpsettings);
$smarty->assign_by_ref('phpsettings', $phpsettings);
// tikiwiki preferences check
// do we need to get the preferences or are they already loaded?
$tikisettings = array();
if ($prefs['feature_file_galleries'] == 'y' && !empty($prefs['fgal_use_dir']) && substr($prefs['fgal_use_dir'], 0, 1) != '/') { // todo: check if absolute path is in tiki root
	$tikisettings['fgal_use_dir'] = array(
		'risk' => tra('unsafe') ,
		'setting' => $prefs['fgal_use_dir'],
		'message' => tra('The Path to store files in the filegallery should be outside the tiki root directory')
	);
}
if ($prefs['feature_galleries'] == 'y' && !empty($prefs['gal_use_dir']) && substr($prefs['gal_use_dir'], 0, 1) != '/') {
	$tikisettings['gal_use_dir'] = array(
		'risk' => tra('unsafe') ,
		'setting' => $prefs['gal_use_dir'],
		'message' => tra('The Path to store files in the imagegallery should be outside the tiki root directory')
	);
}
if ($prefs['feature_edit_templates'] == 'y') {
	$tikisettings['edit_templates'] = array(
		'risk' => tra('unsafe') ,
		'setting' => tra('on') ,
		'message' => tra('The feature "Edit Templates" is switched on. Do not allow anyone you cannot trust to use this feature. It can easily be used to inject php code.')
	);
}
if ($prefs['wikiplugin_snarf'] == 'y') {
	$tikisettings['wikiplugin_snarf'] = array(
		'risk' => tra('unsafe') ,
		'setting' => tra('on') ,
		'message' => tra('The "Snarf Wikiplugin" is activated. It can be used by wiki editors to include pages from the local network and via regex replacement create any html.')
	);
}
if ($prefs['wikiplugin_regex'] == 'y') {
	$tikisettings['wikiplugin_regex'] = array(
		'risk' => tra('unsafe') ,
		'setting' => tra('on') ,
		'message' => tra('The "Regex Wikiplugin" is activated. It can be used by wiki editors to create any html via regex replacement.')
	);
}
if ($prefs['wikiplugin_lsdir'] == 'y') {
	$tikisettings['wikiplugin_lsdir'] = array(
		'risk' => tra('unsafe') ,
		'setting' => tra('on') ,
		'message' => tra('The "Lsdir Wikiplugin" is activated. It can be used by wiki editors to view the contents of any directory.')
	);
}
if ($prefs['wikiplugin_bloglist'] == 'y') {
	$tikisettings['wikiplugin_bloglist'] = array(
		'risk' => tra('unsafe') ,
		'setting' => tra('on') ,
		'message' => tra('The "Bloglist Wikiplugin" is activated. It can be used by wiki editors to disclose private blog posts.')
	);
}
if ($prefs['wikiplugin_iframe'] == 'y') {
	$tikisettings['wikiplugin_iframe'] = array(
		'risk' => tra('unsafe') ,
		'setting' => tra('on') ,
		'message' => tra('The "iframe Wikiplugin" is activated. It can be used by wiki editors for cross site scripting attacks.')
	);
}
if ($prefs['wikiplugin_js'] == 'y') {
	$tikisettings['wikiplugin_js'] = array(
		'risk' => tra('unsafe') ,
		'setting' => tra('on') ,
		'message' => tra('The "js Wikiplugin" is activated. It can be used by wiki editors to use Javascript, which can be used to do all kind of nasty things like cross site scripting attacks, etc.')
	);
}
if ($prefs['wikiplugin_jq'] == 'y') {
	$tikisettings['wikiplugin_jq'] = array(
		'risk' => tra('unsafe') ,
		'setting' => tra('on') ,
		'message' => tra('The "JQ Wikiplugin" is activated. It can be used by wiki editors to use Javascript, which can be used to do all kind of nasty things like cross site scripting attacks, etc.')
	);
}
if ($prefs['wikiplugin_redirect'] == 'y') {
	$tikisettings['wikiplugin_redirect'] = array(
		'risk' => tra('unsafe') ,
		'setting' => tra('on') ,
		'message' => tra('The "Redirect Wikiplugin" is activated. It can be used by wiki editors for cross site scripting attacks.')
	);
}
if ($prefs['wikiplugin_module'] == 'y') {
	$tikisettings['wikiplugin_module'] = array(
		'risk' => tra('unsafe') ,
		'setting' => tra('on') ,
		'message' => tra('The "Module Wikiplugin" is activated. It can be used by wiki editors to add modules which permit to access information (see module list).')
	);
}
if ($prefs['wikiplugin_userlist'] == 'y') {
	$tikisettings['wikiplugin_userlist'] = array(
		'risk' => tra('unsafe') ,
		'setting' => tra('on') ,
		'message' => tra('The "Userlist Wikiplugin" is activated. It can be used by wiki editors to display the list of users.')
	);
}
if ($prefs['wikiplugin_usercount'] == 'y') {
	$tikisettings['wikiplugin_usercount'] = array(
		'risk' => tra('unsafe') ,
		'setting' => tra('on') ,
		'message' => tra('The "Usercount Wikiplugin" is activated. It can be used by wiki editors to display a count of the number of users.')
	);
}
if ($prefs['wikiplugin_sql'] == 'y') {
	$tikisettings['wikiplugin_sql'] = array(
		'risk' => tra('unsafe') ,
		'setting' => tra('on') ,
		'message' => tra('The "SQL Wikiplugin" is activated. It can be used by wiki editors to execute SQL commands.')
	);
}
if ($prefs['feature_clear_passwords'] == 'y') {
	$tikisettings['feature_clear_passwords'] = array(
		'risk' => tra('unsafe') ,
		'setting' => tra('on') ,
		'message' => tra('Store passwords in plain text is activated. You should never set this unless you know what you are doing.')
	);
}
ksort($tikisettings);
$smarty->assign_by_ref('tikisettings', $tikisettings);
// array for severity in tiki_secdb table. This can go into a extra table if
// the array grows to much.
$secdb_severity = array(
	//1000 Path disclosure
	1000 => tra('Path disclosure') ,
	1001 => tra('Path disclosure through error message') ,
	//2000 SQL injection
	2000 => tra('SQL injection') ,
	2001 => tra('SQL injection by authenticated user') ,
	2002 => tra('SQL injection by authenticated user with special privileges') ,
	2003 => tra('SQL injection without authentication') ,
	//3000 command injection
	3000 => tra('PHP command injection') ,
	3001 => tra('PHP command injection by authenticated user') ,
	3002 => tra('PHP command injection by authenticated user with special privileges') ,
	3003 => tra('PHP command injection without authentication') ,
	//4000 File upload
	4000 => tra('File upload')
);
// dir walk & check functions
function md5_check_dir($dir, &$result) { // save all suspicious files in $result
	global $tikilib;
	global $tiki_versions;
	$c_tiki_versions = count($tiki_versions);
	$query = "select * from `tiki_secdb` where `filename`=?";
	$d = dir($dir);
	while (false !== ($e = $d->read())) {
		$entry = $dir . '/' . $e;
		if (is_dir($entry)) {
			if ($e != '..' && $e != '.' && $entry != './templates_c') { // do not descend and no checking of templates_c since the file based md5 database would grow to big
				md5_check_dir($entry, $result);
			}
		} else if (substr($e, -4, 4) == ".php") {
			if (!is_readable($entry)) {
				$result[$entry] = tra('File is not readable. Unable to check.');
			} else {
				$md5val = md5_file($entry);
				$dbresult = $tikilib->query($query, array(
					$entry
				));
				$is_tikifile = false;
				$is_tikiver = array();
				$valid_tikiver = array();
				$severity = 0;
				// we could avoid the following with a second sql, but i think, this is faster.
				while ($res = $dbresult->FetchRow()) {
					$is_tikifile = true; // we know the filename ... probably modified
					if ($res['md5_value'] == $md5val) {
						$is_tikiver[] = $res['tiki_version']; // found
						$severity = $res['severity'];
					}
					$k = array_search($res['tiki_version'], $tiki_versions);
					if ($k > 0) {
						//record the valid versions in this array
						if ($res['md5_value'] == $md5val) {
							$valid_tikiver[$k] = true;
						} else {
							$valid_tikiver[$k] = false;
						}
					}
				}
				if ($is_tikifile == false) {
					$result[$entry] = tra('This is not a Tikiwiki file. Check if this file was uploaded and if it is dangerous.');
				} else if ($is_tikifile == true && count($is_tikiver) == 0) {
					$result[$entry] = tra('This is a modified File. Cannot check version. Check if it is dangerous.');
				} else {
					// check if we have a most recent valid version
					$most_recent = false;
					for ($i = $c_tiki_versions; $i > 0; $i--) { // search $valid_tikiver top to down to find the most recent version
						if (isset($valid_tikiver[$i])) {
							if ($valid_tikiver[$i] == false) {
								//$most_recent stays false. we break
								break;
							} else {
								$most_recent = true; // in this case we have found the most recent version. good
								break;
							}
						}
					}
					// use result of most_recent to decide
					if ($most_recent == false) {
						$result[$entry] = tra('This file is from another Tikiwiki version: ') . implode(' ' . tra('or') . ' ', $is_tikiver);
					}
				}
			}
		}
	}
	$d->close();
}
// if check installation is pressed, walk through all files and compute md5 sums
if (isset($_REQUEST['check_files'])) {
	global $tiki_versions;
	require_once ('lib/setup/twversion.class.php');
	$version = new TWVersion();
	$tiki_versions = $version->tikiVersions();
	$result = array();
	md5_check_dir(".", $result);
	$smarty->assign('filecheck', true);
	$smarty->assign_by_ref('tikifiles', $result);
}
define('S_ISUID', '2048');
define('S_ISGID', '1024');
define('S_ISVTX', '512');
define('S_IRUSR', '256');
define('S_IWUSR', '128');
define('S_IXUSR', '64');
define('S_IRGRP', '32');
define('S_IWGRP', '16');
define('S_IXGRP', '8');
define('S_IROTH', '4');
define('S_IWOTH', '2');
define('S_IXOTH', '1');
// Function to check Filesystem permissions
function check_dir_perms($dir, &$result) {
	static $depth = 0;
	$depth++;
	$d = dir($dir);
	while (false !== ($e = $d->read())) {
		$entry = $dir . '/' . $e;
		if ($e != '..' && ($e != '.' || $depth == 1)) {
			$result[$entry]['w'] = is_writable($entry);
			$result[$entry]['r'] = is_readable($entry);
			$result[$entry]['t'] = filetype($entry);
			$s = stat($entry);
			if (function_exists('posix_getpwuid')) {
				$t = posix_getpwuid($s['uid']);
				$result[$entry]['u'] = $t['name'];
				$t = posix_getgrgid($s['gid']);
				$result[$entry]['g'] = $t['name'];
			} else {
				$result[$entry]['u'] = $s['uid'];
				$result[$entry]['g'] = $s['gid'];
			}
			$m = (int)$s['mode'];
			if ($m >= 32768) $m-= 32768; // clear file type indicators
			if ($m >= 16384) $m-= 16384;
			if ($m >= 8192) $m-= 8192;
			if ($m >= 4096) $m-= 4096;
			$result[$entry]['p'] = $m;
			$result[$entry]['suid'] = ($m >= S_ISUID && ($m-= S_ISUID) >= 0);
			$result[$entry]['sgid'] = ($m >= S_ISGID && ($m-= S_ISGID) >= 0);
			$result[$entry]['sticky'] = ($m >= S_ISVTX && ($m-= S_ISVTX) >= 0);
			$result[$entry]['ur'] = ($m >= S_IRUSR && ($m-= S_IRUSR) >= 0);
			$result[$entry]['uw'] = ($m >= S_IWUSR && ($m-= S_IWUSR) >= 0);
			$result[$entry]['ux'] = ($m >= S_IXUSR && ($m-= S_IXUSR) >= 0);
			$result[$entry]['gr'] = ($m >= S_IRGRP && ($m-= S_IRGRP) >= 0);
			$result[$entry]['gw'] = ($m >= S_IWGRP && ($m-= S_IWGRP) >= 0);
			$result[$entry]['gx'] = ($m >= S_IXGRP && ($m-= S_IXGRP) >= 0);
			$result[$entry]['or'] = ($m >= S_IROTH && ($m-= S_IROTH) >= 0);
			$result[$entry]['ow'] = ($m >= S_IWOTH && ($m-= S_IWOTH) >= 0);
			$result[$entry]['ox'] = ($m >= S_IXOTH && ($m-= S_IXOTH) >= 0);
			if ($result[$entry]['t'] == 'dir' && $e != '.') {
				check_dir_perms($entry, $result);
			}
		}
	}
	$depth--;
}
if (isset($_REQUEST['check_file_permissions'])) {
	$fileperms = array();
	check_dir_perms('.', $fileperms);
	// walk throug array to find problematic entries
	$worldwritable = array();
	$suid = array();
	$executable = array();
	$strangeinode = array();
	$apachewritable = array();
	foreach($fileperms as $fname => $fperms) {
		if ($fperms['suid']) {
			$suid[$fname] = & $fileperms[$fname];
		}
		if ($fperms['ow']) {
			$worldwritable[$fname] = & $fileperms[$fname];
		}
		if ($fperms['t'] != 'dir' && ($fperms['ux'] || $fperms['gx'] || $fperms['ox'])) {
			$executable[$fname] = & $fileperms[$fname];
		}
		if ($fperms['t'] != 'dir' && $fperms['t'] != 'file' && $fperms['t'] != 'link') {
			$strangeinode[$fname] = & $fileperms[$fname];
		}
		if ($fperms['w']) {
			$apachewritable[$fname] = & $fileperms[$fname];
		}
	}
	$smarty->assign_by_ref('worldwritable', $worldwritable);
	$smarty->assign_by_ref('suid', $suid);
	$smarty->assign_by_ref('executable', $executable);
	$smarty->assign_by_ref('strangeinode', $strangeinode);
	$smarty->assign_by_ref('apachewritable', $apachewritable);
	$smarty->assign('permcheck', TRUE);
}
// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');
$smarty->assign('mid', 'tiki-admin_security.tpl');
$smarty->display("tiki.tpl");
