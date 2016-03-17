<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once ('tiki-setup.php');
// do we need it?
$adminlib = TikiLib::lib('admin');
$access->check_permission('tiki_p_admin');

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
		'setting' => tra('Enabled') ,
		'message' => tra('The feature "Edit Templates" is switched on. Do not allow anyone you cannot trust to use this feature. It can easily be used to inject php code.')
	);
}
if ($prefs['wikiplugin_snarf'] == 'y') {
	$tikisettings['wikiplugin_snarf'] = array(
		'risk' => tra('unsafe') ,
		'setting' => tra('Enabled') ,
		'message' => tra('The "Snarf Wikiplugin" is activated. It can be used by wiki editors to include pages from the local network and via regex replacement create any HTML.')
	);
}
if ($prefs['wikiplugin_regex'] == 'y') {
	$tikisettings['wikiplugin_regex'] = array(
		'risk' => tra('unsafe') ,
		'setting' => tra('Enabled') ,
		'message' => tra('The "Regex Wikiplugin" is activated. It can be used by wiki editors to create any HTML via regex replacement.')
	);
}
if ($prefs['wikiplugin_lsdir'] == 'y') {
	$tikisettings['wikiplugin_lsdir'] = array(
		'risk' => tra('unsafe') ,
		'setting' => tra('Enabled') ,
		'message' => tra('The "Lsdir Wikiplugin" is activated. It can be used by wiki editors to view the contents of any directory.')
	);
}
if ($prefs['wikiplugin_bloglist'] == 'y') {
	$tikisettings['wikiplugin_bloglist'] = array(
		'risk' => tra('unsafe') ,
		'setting' => tra('Enabled') ,
		'message' => tra('The "Bloglist Wikiplugin" is activated. It can be used by wiki editors to disclose private blog posts.')
	);
}
if ($prefs['wikiplugin_iframe'] == 'y') {
	$tikisettings['wikiplugin_iframe'] = array(
		'risk' => tra('unsafe') ,
		'setting' => tra('Enabled') ,
		'message' => tra('The "iframe Wikiplugin" is activated. It can be used by wiki editors for cross site scripting attacks.')
	);
}
if ($prefs['wikiplugin_js'] == 'y') {
	$tikisettings['wikiplugin_js'] = array(
		'risk' => tra('unsafe') ,
		'setting' => tra('Enabled') ,
		'message' => tra('The "js Wikiplugin" is activated. It can be used by wiki editors to use Javascript, which can be used to do all kind of nasty things like cross site scripting attacks, etc.')
	);
}
if ($prefs['wikiplugin_jq'] == 'y') {
	$tikisettings['wikiplugin_jq'] = array(
		'risk' => tra('unsafe') ,
		'setting' => tra('Enabled') ,
		'message' => tra('The "JQ Wikiplugin" is activated. It can be used by wiki editors to use Javascript, which can be used to do all kind of nasty things like cross site scripting attacks, etc.')
	);
}
if ($prefs['wikiplugin_redirect'] == 'y') {
	$tikisettings['wikiplugin_redirect'] = array(
		'risk' => tra('unsafe') ,
		'setting' => tra('Enabled') ,
		'message' => tra('The "Redirect Wikiplugin" is activated. It can be used by wiki editors for cross site scripting attacks.')
	);
}
if ($prefs['wikiplugin_module'] == 'y') {
	$tikisettings['wikiplugin_module'] = array(
		'risk' => tra('unsafe') ,
		'setting' => tra('Enabled') ,
		'message' => tra('The "Module Wikiplugin" is activated. It can be used by wiki editors to add modules which permit to access information (see module list).')
	);
}
if ($prefs['wikiplugin_userlist'] == 'y') {
	$tikisettings['wikiplugin_userlist'] = array(
		'risk' => tra('unsafe') ,
		'setting' => tra('Enabled') ,
		'message' => tra('The "Userlist Wikiplugin" is activated. It can be used by wiki editors to display the list of users.')
	);
}
if ($prefs['wikiplugin_usercount'] == 'y') {
	$tikisettings['wikiplugin_usercount'] = array(
		'risk' => tra('unsafe') ,
		'setting' => tra('Enabled') ,
		'message' => tra('The "Usercount Wikiplugin" is activated. It can be used by wiki editors to display a count of the number of users.')
	);
}
if ($prefs['wikiplugin_sql'] == 'y') {
	$tikisettings['wikiplugin_sql'] = array(
		'risk' => tra('unsafe') ,
		'setting' => tra('Enabled') ,
		'message' => tra('The "SQL Wikiplugin" is activated. It can be used by wiki editors to execute SQL commands.')
	);
}
if ($prefs['feature_clear_passwords'] == 'y') {
	$tikisettings['feature_clear_passwords'] = array(
		'risk' => tra('unsafe') ,
		'setting' => tra('Enabled') ,
		'message' => tra('Store passwords in plain text is activated. You should never set this unless you know what you are doing.')
	);
}
if ($prefs['https_login'] != 'required') {
	$tikisettings['https_login'] = array(
		'risk' => tra('risky') ,
		'setting' => ucfirst($prefs['https_login']),
		'message' => tra('To the extent secure logins are not required, data transmitted between the browser and server is not private.')
	);
}

// Check if any of the mail-in accounts uses "Allow anonymous access"
if ($prefs['feature_mailin'] == 'y') {
	$mailinlib = TikiLib::lib('mailin');
	$accs = $mailinlib->list_active_mailin_accounts(0, -1, 'account_desc', '');
	
	// Check anonymous access
	$errorCnt = 0;
	foreach ($accs['data'] as $acc) {
		if ($acc['anonymous'] === 'y') {
			$errorCnt++;
		}
	}
	if ($errorCnt > 0) {
		$tikisettings['feature_mailin-anonymous'] = array(
			'risk' => tra('unsafe') ,
			'setting' => tra('Enabled') ,
			'message' => tra('One or more mail-in accounts have enabled "Allow anonymous access", which disables all permission checking for incoming email. Check tiki-admin_mailin.php')
		);
	}
	
	// Check admin access
	$errorCnt = 0;
	foreach ($accs['data'] as $acc) {
		if ($acc['admin'] === 'y') {
			$errorCnt++;
		}
	}
	if ($errorCnt > 0) {
		$tikisettings['feature_mailin-admin'] = array(
			'risk' => tra('unsafe') ,
			'setting' => tra('Enabled') ,
			'message' => tra('One or more mail-in accounts have enabled "Allow admin access", which allows for incoming email from admins. Admins have all rights, and web pages can easily be overwitten / tampered with. Check tiki-admin_mailin.php')
		);
	}
}

//check to see if installer lock is being used
//check multitiki
if (is_file('db/virtuals.inc')) {
	$virtuals = array_map('trim', file('db/virtuals.inc'));
	foreach ($virtuals as $v) {
		if ($v) {
			if (is_file("db/$v/local.php") && is_readable("db/$v/local.php")) {
				$virt[$v] = 'y';
			} else {
				$virt[$v] = 'n';
			}
		}
	}
} else {
	$virt = false;
	$virtuals = false;
}
$multi = '';
if ($virtuals) {
	if (isset($_SERVER['TIKI_VIRTUAL']) && is_file('db/'.$_SERVER['TIKI_VIRTUAL'].'/local.php')) {
		$multi = $_SERVER['TIKI_VIRTUAL'];
	} elseif (isset($_SERVER['SERVER_NAME']) && is_file('db/'.$_SERVER['SERVER_NAME'].'/local.php')) {
		$multi = $_SERVER['SERVER_NAME'];
	} elseif (isset($_SERVER['HTTP_HOST']) && is_file('db/'.$_SERVER['HTTP_HOST'].'/local.php')) {
		$multi = $_SERVER['HTTP_HOST'];
	}
}
$tikidomain = $multi;
$tikidomainslash = (!empty($tikidomain) ? $tikidomain . '/' : '');
if (!file_exists('db/'.$tikidomainslash.'lock')) {
	$tikisettings['installer lock'] = array(
		'risk' => tra('unsafe') ,
		'setting' => tra('Unlocked') ,
		'message' => tra('The installer is not locked. The installer could be accessed, putting the database at risk of being altered or destroyed.')
	);
}

$fmap = [
	'good' => ['icon' => 'ok', 'class' => 'success'],
	'safe' => ['icon' => 'ok', 'class' => 'success'],
	'bad' => ['icon' => 'ban', 'class' => 'danger'],
	'unsafe' => ['icon' => 'ban', 'class' => 'danger'],
	'risky' => ['icon' => 'warning', 'class' => 'warning'],
	'ugly' => ['icon' => 'warning', 'class' => 'warning'],
	'info' => ['icon' => 'information', 'class' => 'info'],
	'unknown' => ['icon' => 'help', 'class' => 'muted'],
];
$smarty->assign('fmap', $fmap);


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
/**
 * @param $dir
 * @param $result
 */
function md5_check_dir($dir, &$result)
{ // save all suspicious files in $result
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
		} else if (preg_match('/\.(sql|css|tpl|js|php)$/', $e)) {
			if (!is_readable($entry)) {
				$result[$entry] = tra('File is not readable. Unable to check.');
			} else {
				$md5val = md5_file($entry);
				$dbresult = $tikilib->query($query, array($entry));
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
					$result[$entry] = tra('This is not a Tiki file. Check if this file was uploaded and if it is dangerous.');
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
						$result[$entry] = tra('This file is from another Tiki version: ') . implode(' ' . tra('or') . ' ', $is_tikiver);
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
/**
 * @param $dir
 * @param $result
 */
function check_dir_perms($dir, &$result)
{
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
	foreach ($fileperms as $fname => $fperms) {
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
