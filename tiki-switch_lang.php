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

$access->check_feature('change_language');

if (isset($_SERVER['HTTP_REFERER'])) $orig_url = $_SERVER['HTTP_REFERER'];
else $orig_url = $prefs['tikiIndex'];

//for lang_nonswitchingpages
if ($prefs['feature_lang_nonswitchingpages'] == "y" && !empty($prefs['feature_lang_nonswitchingpages_names'])){
	$nopage1 = explode( ',', $prefs['feature_lang_nonswitchingpages_names'] );
	$orig_url_dec = urldecode($orig_url);
	
	foreach($nopage1 as $valpage){
		$valpage_dec = urldecode($valpage);
		if (strstr($orig_url_dec, $valpage_dec)) {
			$orig_url = $prefs['tikiIndex'];
		}
	}
}

// for item id when switching from one lang to other
$item_url = parse_url($_SERVER['HTTP_REFERER']);
$item_url = $item_url[query];

if ($prefs['feature_sefurl'] == 'y' && !strstr($orig_url, '.php')) { 
	if (preg_match('/cat[0-9]+-?/', $orig_url)) {
		include_once('tiki-sefurl.php');
		$orig_url = filter_out_sefurl(preg_replace('#(.*)\/cat([0-9]+)(.*)#', '$1/tiki-browse_categories.php?parentId=$2$3', $orig_url), 'category');
	} elseif (preg_match('/article[0-9]+-?/', $orig_url)) {
		$orig_url = preg_replace('#\/article([0-9]+)(.*)#', '/tiki-read_article.php?articleId=$1', $orig_url);
	} else {
		if (strpos($orig_url, '?') !== false) {
			$orig_url = preg_replace('#\/([^\/\?]+)\?(.*)?$#', '/tiki-index.php?page=$1&$2', $orig_url);
		} else {
			$orig_url = preg_replace('#\/([^\/\?]+)$#', '/tiki-index.php?page=$1', $orig_url);
		}
	}
} elseif (!strstr($orig_url, '.php')) {
	$orig_url = preg_replace('#\/([^\/\?]+)(\?.*)?$#', '/tiki-index.php?page=$1', $orig_url);
        $params = parse_url($orig_url);
        if (empty($params['query']))
                $orig_url = $prefs['tikiIndex'];
}

if (strstr($orig_url, 'tiki-index.php') || strstr($orig_url, 'tiki-read_article.php')) {
	$multilinguallib = TikiLib::lib('multilingual');
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
		$wikilib = TikiLib::lib('wiki');
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
			$orig_url = preg_replace('/(.*[&?]page=)'.preg_quote($page).'(.*)/', '${1}'."${newPage}".'$2', $orig_url);
			$orig_url = preg_replace('/(.*)(tiki-index.php)$/', "$1$2?page=$newPage", $orig_url);
		}
	}
	$orig_url = preg_replace('/(.*)no_bl=y&(.*)/', '$1$2', $orig_url);
	$orig_url = preg_replace('/(.*)&no_bl=y(.*)/', '$1$2', $orig_url);
	if ($prefs['feature_sefurl'] == 'y') {
		include_once('tiki-sefurl.php');
		$orig_url = filter_out_sefurl($orig_url);
	}

	if ($item_url){
		if ($prefs['feature_sefurl'] == 'y') {
			$orig_url = $orig_url."?".$item_url;
		}
		elseif(!strstr($_SERVER['HTTP_REFERER'], 'tiki-index.php') && !strstr($_SERVER['HTTP_REFERER'], 'tiki-read_article.php')) {
			$orig_url = $orig_url."&".$item_url;
		}
	}
}

if (isset($_GET['language'])) {
	setLanguage($_GET['language']);
}
header("location: $orig_url");
exit;
