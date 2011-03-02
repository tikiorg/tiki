<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once ('tiki-setup.php');

$access->check_feature('change_language');

if (isset($_GET['from'])) $orig_url = $_GET['from'];
elseif (isset($_SERVER['HTTP_REFERER'])) $orig_url = $_SERVER['HTTP_REFERER'];
else $orig_url = $prefs['tikiIndex'];

if ($prefs['feature_sefurl'] == 'y' && !strstr($orig_url, '.php')) { 
        if (!preg_match('/article[0-9]+-?/', $orig_url)) {
                $orig_url = preg_replace('#\/([^\/]+)$#', '/tiki-index.php?page=$1', $orig_url);
        } else {
                $orig_url = preg_replace('#\/article([0-9]+)(.*)#', '/tiki-read_article.php?articleId=$1', $orig_url);
        }
}
if (!strstr($orig_url, '.php')) {
        $params = parse_url($orig_url);
        if (empty($params['query']))
                $orig_url = $prefs['tikiIndex'];
}

if (strstr($orig_url, 'tiki-index.php') || strstr($orig_url, 'tiki-read_article.php')) {
	global $multilinguallib;
	include_once ("lib/multilingual/multilinguallib.php");
	$orig_url = urldecode($orig_url);
	if (($txt = strstr($orig_url, '?')) == false) {
		$txt = '';
	} else {
		$txt = substr($txt, 1);
	}
	TikiLib::parse_str($txt, $param);
	if (!empty($param['page_id'])) {
		$pageId = $param['page_id'];
		$type = 'wiki page';
	} else if (!empty($param['articleId'])) {
                $pageId = $param['articleId'];
                $type = 'article';
	} else if (!empty($param['page'])) {
		$page = $param['page'];
		$info = $tikilib->get_page_info($page);
		$pageId = $info['page_id'];
		$type = 'wiki page';
	} else {
		global $wikilib;
		include_once ('lib/wiki/wikilib.php');
		$page = $wikilib->get_default_wiki_page();
		$info = $tikilib->get_page_info($page);
		$pageId = $info['page_id'];
		$type = 'wiki page';
	}
	$bestLangPageId = $multilinguallib->selectLangObj($type, $pageId, $_REQUEST['language']);
	if ($pageId != $bestLangPageId) {
		if (!empty($param['page_id'])) {
			$orig_url = preg_replace('/(.*[&?]page_id=)' . $pageId . '(.*)/', '${1}' . $bestLangPageId . '$2', $orig_url);
		} elseif (!empty($param['articleId'])) {
			$orig_url = preg_replace('/(.*[&?]articleId=)' . $pageId . '(.*)/', '${1}' . $bestLangPageId . '$2', $orig_url);
		} else {
			$newPage = urlencode($tikilib->get_page_name_from_id($bestLangPageId));
			$orig_url = preg_replace('/(.*[&?]page=)'.$page.'(.*)/', '${1}'."${newPage}".'$2', $orig_url);
			$orig_url = preg_replace('/(.*)(tiki-index.php)$/', "$1$2?page=$newPage", $orig_url);
		}
	}
	$orig_url = preg_replace('/(.*)no_bl=y&(.*)/', '$1$2', $orig_url);
	$orig_url = preg_replace('/(.*)&no_bl=y(.*)/', '$1$2', $orig_url);
	if ($prefs['feature_sefurl'] == 'y') {
		include_once('tiki-sefurl.php');
		$orig_url = filter_out_sefurl($orig_url, $smarty);
	}
}
$orig_url = preg_replace('/(.*\?.*)switchLang=[a-zA-Z-_]*&?(.*)/', '$1$2', $orig_url);
$orig_url = preg_replace('/(.*[?&]lang=)[a-zA-Z-_]*(&?.*)/', '$1' . $_REQUEST['language'] . '$2', $orig_url); // for tiki-view_lang.php?lang=en
if (isset($_GET['language'])) {
	$language = $_GET['language'];
	if ($user && $prefs['change_language'] == 'y') {
		$tikilib->set_user_preference($user, 'language', $language);
	} else {
		$_SESSION['s_prefs']['language'] = $language;
		$prefs['language'] = $language;
	}
}
header("location: $orig_url");
exit;
