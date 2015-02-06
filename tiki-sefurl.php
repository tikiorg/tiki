<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}
define('PATTERN_TO_CLEAN_TEXT', '/\W/u');
define('CLEAN_CHAR', '-');
define('TITLE_SEPARATOR', '-');

/**
 * Turns a traditional URL into a Search Engine Friendly one, if requested
 *
 * @param string $tpl_output	original "unfriendly" url
 * @param string $type			type of object (article|blog|blogpost etc)
 * @param string $title			title of object
 * @param null $with_next		Appends '?' or a '&amp;' to the end of the returned URL so you can join further parameters
 * @param string $with_title	Add the object title to the end of the URL
 * @return string				sefurl
 */


function filter_out_sefurl($tpl_output, $type = null, $title = '', $with_next = null, $with_title='y') 
{
	global $sefurl_regex_out, $prefs, $base_url, $in_installer;
	if ($prefs['feature_sefurl'] != 'y' || !empty($in_installer) || ( preg_match('#^http(|s)://#', $tpl_output) and strpos($tpl_output, $base_url) !== 0 ) ) {
		return $tpl_output;
	}
	$cachelib = TikiLib::lib('cache');
	$tikilib = TikiLib::lib('tiki');
	if (!is_array($sefurl_regex_out)) {
		if (! $sefurl_regex_out = $cachelib->getSerialized('sefurl_regex_out')) {
			$query = 'select * from `tiki_sefurl_regex_out` where `silent` != ? order by `order` asc';
			$result = $tikilib->query($query, array('y'));
			$sefurl_regex_out = array();
			if (!empty($result)) {
				while ($res = $result->fetchRow()) {
					if (empty($res['feature']) || $prefs[$res['feature']] == 'y') {
						$sefurl_regex_out[] = $res;
					}
				}
			}
			$cachelib->cacheItem('sefurl_regex_out', serialize($sefurl_regex_out));
		}
	}
	if ($type == 'article' && empty($with_next) && $with_title == 'y') {
		if ($prefs['feature_sefurl_title_article'] == 'y') {
			$artlib = TikiLib::lib('art');

			if (preg_match('/articleId=([0-9]+)/', $tpl_output, $matches) || preg_match('/article([0-9]+)/', $tpl_output, $matches)) {
				if (empty($title)) $title = $artlib->get_title($matches[1]);
				$title = preg_replace(PATTERN_TO_CLEAN_TEXT, CLEAN_CHAR, $tikilib->take_away_accent($title));
				$title = preg_replace('/' . CLEAN_CHAR . CLEAN_CHAR . '+/', '-', $title);
				$title = preg_replace('/' . CLEAN_CHAR . '+$/', '', $title);
			}
		} else {
			$title = '';
		}
	}
	if ($type == 'blog' && empty($with_next) && $with_title == 'y') {
		if ($prefs['feature_sefurl_title_blog'] == 'y') {
			$bloglib = TikiLib::lib('blog');
			if (preg_match('/blogId=([0-9]+)/', $tpl_output, $matches) || preg_match('/blog([0-9]+)/', $tpl_output, $matches)) {
				if (empty($title)) $title = $bloglib->get_title($matches[1]);
				$title = preg_replace(PATTERN_TO_CLEAN_TEXT, CLEAN_CHAR, $tikilib->take_away_accent($title));
				$title = preg_replace('/' . CLEAN_CHAR . CLEAN_CHAR . '+/', '-', $title);
				$title = preg_replace('/' . CLEAN_CHAR . '+$/', '', $title);
			}
		} else {
			$title = '';
		}
	}
	if ($type == 'blogpost' && empty($with_next) && $with_title == 'y') {
		if ($prefs['feature_sefurl_title_blog'] == 'y') {
			$bloglib = TikiLib::lib('blog');

			if (preg_match('/postId=([0-9]+)/', $tpl_output, $matches)|| preg_match('/blogpost([0-9]+)/', $tpl_output, $matches)) {
				if (empty($title)) {
					if ($post_info = $bloglib->get_post($matches[1])) $title = $post_info['title'];
				}
				$title = preg_replace(PATTERN_TO_CLEAN_TEXT, CLEAN_CHAR, $tikilib->take_away_accent($title));
				$title = preg_replace('/' . CLEAN_CHAR . CLEAN_CHAR . '+/', '-', $title);
				$title = preg_replace('/' . CLEAN_CHAR . '+$/', '', $title);
			}
		} else {
			$title = '';
		}
	}
	if ($type == 'tracker item' || $type == 'trackeritem') {
		if (preg_match('/itemId=([0-9]+)/', $tpl_output, $matches)) {
			$trklib = TikiLib::lib('trk');
			if ($prefs["feature_sefurl_tracker_prefixalias"] == 'y' && $pagealias = $trklib->get_trackeritem_pagealias($matches[1])) {
				$tpl_output = "./tiki-index.php?page=" . $pagealias;
			}
		}
	}
	if ($type == 'category' && !empty($title) && $with_title == 'y') {
		$title = preg_replace(PATTERN_TO_CLEAN_TEXT, CLEAN_CHAR, $tikilib->take_away_accent($title));
		$title = preg_replace('/' . CLEAN_CHAR . CLEAN_CHAR . '+/', '-', $title);
		$title = preg_replace('/' . CLEAN_CHAR . '+$/', '', $title);	
	}
	foreach ($sefurl_regex_out as $regex) {
		if ((empty($type) || $type == $regex['type']) &&
			preg_match('/tiki-index\.php\?page=[^&]*%2F/', $tpl_output) === 0) {	// slash (%2F here) in sefurl page name causes error 404
			// if a question mark in pattern, deal with possible additional terms
			// The '?&' isn't pretty but seems to work.
			//if ( strpos($regex['left'],'?') !== FALSE ) {
			//	$tpl_output = preg_replace( '/'.$regex['left'].'&/', $regex['right'].'?&', $tpl_output );
			//}
			$tpl_output = preg_replace('/' . $regex['left'] . '/', $regex['right'], $tpl_output);
		}
	}
	if (!empty($title) && $with_title == 'y') {
		$tpl_output.= TITLE_SEPARATOR . $title;
	}
	if (is_array($prefs['feature_sefurl_paths'])) {
		foreach ($prefs['feature_sefurl_paths'] as $path) {
			if (isset($_REQUEST[$path])) {
				$tpl_output = urlencode($_REQUEST[$path]) . "/$tpl_output";
			}
		}
	}

	if (strpos($tpl_output, '?') === false) {	// historically tiki has coped with malformed short urls with no ?
		$amppos = strpos($tpl_output, '&');		// route.php requires that we no longer do that
		$eqpos = strpos($tpl_output, '=');
		if ( $amppos !== false && ($eqpos === false || $eqpos > $amppos)) {
			if (substr($tpl_output, $amppos, 5) !== '&amp;') {
				$tpl_output{$amppos} = '?';
			} else {
				$tpl_output = substr($tpl_output, 0, $amppos) . '?' . substr($tpl_output, $amppos + 5);
			}
		}
	}

	if ($with_next) {
		if (strpos($tpl_output, '?') === false) {
			$tpl_output .= '?';
		} else {
			$tpl_output .= '&amp;';
		}
	}

	return $tpl_output;
}
