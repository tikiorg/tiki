<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

// This script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'], basename(__FILE__)) !== false) {
	header('location: index.php');
	exit;
}

$plugins = array();
foreach($tikilib->plugin_get_list() as $name) {
	$info = $tikilib->plugin_info($name);
	if (isset($info['prefs']) && is_array($info['prefs']) && count($info['prefs']) > 0) $plugins[$name] = $info;
}
$smarty->assign('plugins', $plugins);
if (isset($_REQUEST['textareasetup']) && (!isset($_COOKIE['tab']) || $_COOKIE['tab'] != 3)) { // tab=3 is plugins alias tab (TODO improve)
	ask_ticket('admin-inc-textarea');
	foreach(glob('temp/cache/wikiplugin_*') as $file) unlink($file);
}

// from tiki-admin_include_textarea.php
global $tikilib;
$pluginsAlias = $tikilib->plugin_get_list(false, true);
$pluginsReal = $tikilib->plugin_get_list(true, false);
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	global $cachelib;
	require_once ('lib/cache/cachelib.php');
	$areanames = array(
		'editwiki',
		'editpost',
		'editpost2',
		'blogedit',
		'faqans',
		'body',
		'description',
		'trackerDescription'
	);
	foreach($tikilib->list_languages() as $tlang) {
		foreach($areanames as $an) {
			$cachetag = 'plugindesc' . $tlang['value'] . $an . '_js=' . $prefs['javascript_enabled'];
			$cachelib->invalidate($cachetag);
		}
	}
	if (isset($_POST['enable'])) {
		if (!is_array($_POST['enabled'])) $_POST['enabled'] = array();
		foreach($pluginsAlias as $name) {
			$tikilib->set_preference("wikiplugin_$name", in_array($name, $_POST['enabled']) ? 'y' : 'n');
		}
		foreach(glob('temp/cache/wikiplugin_*') as $file) unlink($file);
	}
	if (isset($_POST['delete'])) {
		if (!is_array($_POST['enabled'])) $_POST['enabled'] = array();
		foreach($pluginsAlias as $name) {
			$tikilib->plugin_alias_delete( $name );
		}
		$pluginsAlias = $tikilib->plugin_get_list(false, true);
	}
	if (isset($_POST['textareasetup']) && !in_array($_POST['plugin_alias'], $pluginsReal) && isset($_REQUEST["plugin_alias"]) && (!isset($_COOKIE['tab']) || $_COOKIE['tab'] == 3)) { // tab=3 is plugins alias tab (TODO improve)
		$info = array(
			'implementation' => $_POST['implementation'],
			'description' => array(
				'name' => $_POST['name'],
				'description' => $_POST['description'],
				'prefs' => array() ,
				'validate' => $_POST['validate'],
				'filter' => $_POST['filter'],
				'inline' => isset($_POST['inline']) ,
				'params' => array() ,
			) ,
			'body' => array(
				'input' => isset($_POST['ignorebody']) ? 'ignore' : 'use',
				'default' => $_POST['defaultbody'],
				'params' => array() ,
			) ,
			'params' => array() ,
		);
		if (!empty($_POST['body'])) {
			$info['description']['body'] = $_POST['body'];
		}
		if ($_POST['validate'] == 'none') {
			unset($info['description']['validate']);
		}
		if (empty($_POST['prefs'])) $temp = array(
			"wikiplugin_{$_POST['plugin_alias']}"
		);
		else $temp = explode(',', $_POST['prefs']);
		$info['description']['prefs'] = $temp;
		if (isset($_POST['input'])) {
			foreach($_POST['input'] as $param) {
				if (!empty($param['token']) && !empty($param['name'])) {
					$info['description']['params'][$param['token']] = array(
						'required' => isset($param['required']) ,
						'safe' => isset($param['safe']) ,
						'name' => $param['name'],
						'description' => $param['description'],
						'filter' => $param['filter'],
					);
				}
			}
		}
		if (isset($_POST['bodyparam'])) {
			foreach($_POST['bodyparam'] as $param) {
				if (!empty($param['token'])) {
					$info['body']['params'][$param['token']] = array(
						'input' => $param['input'],
						'encoding' => $param['encoding'],
						'default' => $param['default'],
					);
				}
			}
		}
		if (isset($_POST['sparams'])) {
			foreach($_POST['sparams'] as $detail) {
				if (!empty($detail['token'])) {
					$info['params'][$detail['token']] = $detail['default'];
				}
			}
		}
		if (isset($_POST['cparams'])) {
			foreach($_POST['cparams'] as $detail) {
				if (!empty($detail['token'])) {
					$info['params'][$detail['token']] = array(
						'pattern' => $detail['pattern'],
						'params' => array() ,
					);
					foreach($detail['params'] as $param) {
						if (!empty($param['token'])) {
							$info['params'][$detail['token']]['params'][$param['token']] = array(
								'input' => $param['input'],
								'encoding' => $param['encoding'],
								'default' => $param['default'],
							);
						}
					}
				}
			}
		}
		$tikilib->plugin_alias_store($_POST['plugin_alias'], $info);
		if (!in_array($_POST['plugin_alias'], $pluginsAlias)) $pluginAlias[] = $_POST['plugins'];
		foreach(glob('temp/cache/wikiplugin_*') as $file) unlink($file);
		$pluginsAlias = $tikilib->plugin_get_list(false, true);
	}
}
if (isset($_REQUEST['plugin_alias']) && $pluginInfo = $tikilib->plugin_alias_info($_REQUEST['plugin_alias'])) {
	// Add an extra empty parameter to create new ones
	$pluginInfo['description']['params']['__NEW__'] = array(
		'name' => '',
		'description' => '',
		'required' => '',
		'safe' => '',
	);
	$pluginInfo['body']['params']['__NEW__'] = array(
		'encoding' => '',
		'input' => '',
		'default' => '',
	);
	$pluginInfo['params']['__NEW__'] = array(
		'pattern' => '',
		'params' => array() ,
	);
	foreach($pluginInfo['params'] as & $p) if (is_array($p)) $p['params']['__NEW__'] = array(
		'encoding' => '',
		'input' => '',
		'default' => '',
	);
	$smarty->assign('plugin_admin', $pluginInfo);
	$cookietab = 3;
} else {
	$smarty->assign('plugin_admin', array());
}
$smarty->assign('plugins_alias', $pluginsAlias);
$smarty->assign('plugins_real', $pluginsReal);

if (isset($_REQUEST['disabled']) && $tiki_p_admin == 'y') {
	$offset = 0;
	$disabled = array();
	foreach($tikilib->plugin_get_list() as $name) {
		if ($prefs["wikiplugin_$name"] == 'n') {
			$allDisabled[] = $name;
		}
	}
	do {
		$pages = $tikilib->list_pages($offset, $prefs['maxRecords'], 'pageName_asc');
		if (empty($pages['data'])) {
			break;
		}
		$offset += $prefs['maxRecords'];
		foreach ($pages['data'] as $page) {
			$plugins = $tikilib->getPlugins($page['data'], $allDisabled);
			if (!empty($plugins)) {
				foreach ($plugins as $plugin) {
					if (!in_array($plugin[1], $disabled)) {
						$disabled[] = $plugin[1];
					}
				}
			}
		}
	} while (true);
	$smarty->assign_by_ref('disabled', $disabled);
}
setcookie('tab', $cookietab);
