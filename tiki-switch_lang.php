<?php
// language will change the strings language only and will add bl for previous compatibility
//switchLang will change the strings+ page language

require_once('tiki-setup.php');
include_once('lib/tikilib.php');

if (isset($_GET['from'])) 
	$orig_url = $_GET['from'];
elseif (isset($_SERVER['HTTP_REFERER']))
	$orig_url = $_SERVER['HTTP_REFERER'];
else
	$orig_url = $tikiIndex;

if (!preg_match('/(\?|&)bl=/', $orig_url)) {
	if (strstr($orig_url, 'tiki-index.php?'))
		$orig_url .= '&bl=y';
	else if (strstr($orig_url, 'tiki-index.php'))
		$orig_url .= '?bl=y';
}
if(isset($_REQUEST['language'])|| isset($_REQUEST['switchLang'])) {
	$language = isset($_REQUEST['language'])? $_REQUEST['language']: $_REQUEST['switchLang'];
	if($feature_userPreferences == 'y' && $user && $change_language == 'y')  {
		$tikilib->set_user_preference($user, 'language', $language);
	}
	else {
		$_SESSION['language'] = $language;
	}
}

if (strstr($orig_url, 'tiki-index.php') && isset($_REQUEST['switchLang'])) {
	global $multilinguallib; include_once('lib/multilingual/multilinguallib.php');
	global $user;
	if (preg_match('/tiki-index.php?.*page=([^&]*)/', $orig_url, $matches))
		$page = $matches[1];
	else
		$page = $userlib->get_user_default_homepage2($user);
	$info = $tikilib->get_page_info($page);
	$bestLangPageId = $multilinguallib->selectLangObj('wiki page', $info['page_id']);
//echo "eeee".$info['page_id'].$tikilib->get_page_name_from_id($bestLangPageId).$bestLangPageId;die;
	if ($info['page_id'] != $bestLangPageId) {
		$orig_url = 'tiki-index.php?page='.$tikilib->get_page_name_from_id($bestLangPageId);
//TODO: introduce a get_info_from_id to save a sql request
	} elseif ($info['lang'] != $language && $feature_homePage_if_bl_missing == 'y') {
		if (!isset($userPageName))
			$userPageName = $userlib->get_user_default_homepage2($user);
		$page = $userPageName;
		$info = $tikilib->get_page_info($page);
		$bestLangPageId = $multilinguallib->selectLangObj('wiki page', $info['page_id']);
		if ($info['page_id'] != $bestLangPageId) {
			$orig_url = 'tiki-index.php?page='.$tikilib->get_page_name_from_id($bestLangPageId);
		}
	}
} else if ($feature_best_language == 'y') {
	if (!preg_match('/(\?|&)bl=/', $orig_url)) {
		if (strstr($orig_url, 'tiki-index.php?page'))
			$orig_url .= '&bl=y';
		else if (strstr($orig_url, 'tiki-index.php'))
			$orig_url .= '?bl=y';
	}
}	

header("location: $orig_url");
exit;
?>
