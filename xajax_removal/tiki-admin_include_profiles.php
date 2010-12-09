<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}
require_once 'lib/profilelib/profilelib.php';
require_once 'lib/profilelib/installlib.php';
require_once 'lib/profilelib/listlib.php';
$list = new Tiki_Profile_List;
$sources = $list->getSources();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	check_ticket('admin-inc-profiles');
	if (isset($_POST['config'])) { // {{{
		$tikilib->set_preference('profile_sources', $_POST['profile_sources']);
		$tikilib->set_preference('profile_channels', $_POST['profile_channels']);
		header('Location: tiki-admin.php?page=profiles');
		exit;
	} // }}}
	if (isset($_POST['forget'], $_POST['pp'], $_POST['pd'])) { // {{{
		$profile = Tiki_Profile::fromNames($_POST['pd'], $_POST['pp']);
		$profile->removeSymbols();
		$data = array();
		foreach($_POST as $key => $value) if ($key != 'url' && $key != 'forget') $data[str_replace('_', ' ', $key) ] = $value;
		$installer = new Tiki_Profile_Installer;
		$installer->setUserData($data);
		$installer->install($profile);
		if ($target = $profile->getInstructionPage()) {
			global $wikilib;
			require_once 'lib/wiki/wikilib.php';
			$target = $wikilib->sefurl($target);
			header('Location: ' . $target);
			exit;
		} else {
			if (count($installer->getFeedback()) > 0) {
				$smarty->assign_by_ref('profilefeedback', $installer->getFeedback());
			}
			// need to reload sources as cache is cleared after install
			$sources = $list->getSources();
		}
	} // }}}
	if (isset($_POST['install'], $_POST['pd'], $_POST['pp'])) { // {{{
		$data = array();
		foreach($_POST as $key => $value) if ($key != 'url' && $key != 'install') $data[str_replace('_', ' ', $key) ] = $value;
		$installer = new Tiki_Profile_Installer;
		$installer->setUserData($data);
		$profile = Tiki_Profile::fromNames($_POST['pd'], $_POST['pp']);
		$installer->install($profile);
		if ($target = $profile->getInstructionPage()) {
			global $wikilib;
			require_once 'lib/wiki/wikilib.php';
			$target = $wikilib->sefurl($target);
			header('Location: ' . $target);
			exit;
		} else {
			if (count($installer->getFeedback()) > 0) {
				$smarty->assign_by_ref('profilefeedback', $installer->getFeedback());
			}
			// need to reload sources as cache is cleared after install
			$sources = $list->getSources();
		}
	} // }}}
	if (isset($_POST['test'], $_POST['profile_tester'], $_POST['profile_tester_name'])) { // {{{
		$test_source = $_POST['profile_tester'];
		if (strpos($test_source, '{CODE}') === false) {	// wrap in CODE tags if none there
			$test_source = "{CODE(caption=>YAML)}\n$test_source\n{CODE}";
		}
		$smarty->assign('test_source', $test_source);
		$smarty->assign('profile_tester_name', $_POST['profile_tester_name']);
		$profile = Tiki_Profile::fromString($test_source, $_POST['profile_tester_name']);
		$profile->removeSymbols();
		$installer = new Tiki_Profile_Installer;
		$installer->install($profile);
		if ($target = $profile->getInstructionPage()) {
			global $wikilib;
			require_once 'lib/wiki/wikilib.php';
			$target = $wikilib->sefurl($target);
			header('Location: ' . $target);
			exit;
		} else {
			if (count($installer->getFeedback()) > 0) {
				$smarty->assign_by_ref('profilefeedback', $installer->getFeedback());
			}
		}
	} // }}}
	if (isset($_GET['refresh'])) { // {{{
		$toRefresh = (int)$_GET['refresh'];
		if (isset($sources[$toRefresh])) {
			echo json_encode(array(
				'status' => $list->refreshCache($sources[$toRefresh]['url']) ? 'open' : 'closed',
				'lastupdate' => date('Y-m-d H:i:s') ,
			));
		} else echo '{}';
		exit;
	} // }}}
	if (isset($_GET['getinfo'], $_GET['pd'], $_GET['pp'])) { // {{{
		$installer = new Tiki_Profile_Installer;
		$profile = Tiki_Profile::fromNames($_GET['pd'], $_GET['pp']);
		$error = '';
		try {
			if (!$deps = $installer->getInstallOrder($profile)) {
				$deps = $profile->getRequiredProfiles(true);
				$deps[] = $profile;
				$sequencable = false;
			} else $sequencable = true;
		}
		catch(Exception $e) {
			$error = $e->getMessage();
			$sequencable = false;
		}
		$dependencies = array();
		$userInput = array();
		foreach($deps as $d) {
			$dependencies[] = $d->pageUrl;
			$userInput = array_merge($userInput, $d->getRequiredInput());
		}
		$parsed = $tikilib->parse_data($profile->pageContent);
		$installed = $installer->isInstalled($profile);
		
		echo json_encode(array(
			'dependencies' => $dependencies,
			'userInput' => $userInput,
			'installable' => $sequencable,
			'error' => $error,
			'content' => $parsed,
			'already' => $installed,
			'url' => $profile->url,
			'feedback' => $profile->getFeedback(),
		));
		exit;
	} // }}}
	
}
if (isset($_GET['list'])) { // {{{
	$params = array_merge(array(
		'repository' => '',
		'categories' => '',
		'profile' => ''
	) , $_GET);
	$smarty->assign('categories', $params['categories']);
	$smarty->assign('profile', $params['profile']);
	$smarty->assign('repository', $params['repository']);
	if (isset($_GET['preloadlist']) && $params['repository']) $list->refreshCache($params['repository']);
	$profiles = $list->getList($params['repository'], $params['categories'], $params['profile']);
	foreach ($profiles as &$profile) {
		$profile['categoriesString'] = "";
		foreach ($profile['categories'] as $category) {
			$profile['categoriesString'] .= (empty($profile['categoriesString']) ? '' : ', ') . $category;
		}
	}
	$smarty->assign('result', $profiles);
	$category_list = $list->getCategoryList($params['repository']);
	$smarty->assign('category_list', $category_list);
} // }}}
$threshhold = time() - 1800;
$oldSources = array();
foreach($sources as $key => $source) if ($source['lastupdate'] < $threshhold) $oldSources[] = $key;
$smarty->assign('sources', $sources);
$smarty->assign('oldSources', $oldSources);

$openSources = 0;
foreach($sources as $key => $source)
{
	if ($source['status'] == 'open')
		$openSources++;
}

if($openSources == count($sources))
	$smarty->assign('openSources', 'all');
elseif (($openSources > 0) &&($openSources < count($sources)))
	$smarty->assign('openSources', 'some');
else
	$smarty->assign('openSources', 'none');
	
$smarty->assign('tikiMajorVersion' ,substr($TWV->version,0,2));

	

ask_ticket('admin-inc-profiles');
