<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}

include_once("tikihelplib.php");

/**
 *
 */
class Breadcrumb
{
	public $title;
	public $description;
	public $url;
	public $helpUrl;
	public $helpDescription;
	public $hidden;

    /**
     * @param $title
     * @param string $desc
     * @param string $url
     * @param string $helpurl
     * @param string $helpdesc
     */
    function __construct($title, $desc='', $url='', $helpurl='', $helpdesc='')
	{
		if ($title == '') {
			$this->title = 'Home';
		} else {
			$this->title = $title;
		}
		$this->description = $desc;
		$this->url = $url;
		$this->helpUrl = $helpurl;
		$this->helpDescription = $helpdesc;
		$this->hidden = false;
	}
	/* end of class */
}

/* static */
/**
 * @param $crumbs
 * @return bool|string
 */
function breadcrumb_buildHeadTitle($crumbs)
{
	global $prefs;
	if ($prefs['feature_breadcrumbs'] === 'y') {
		if ( _is_assoc($crumbs) ) {
			return false;
		}
		if ( is_array($crumbs) ) {
			$ret = array();
			foreach ($crumbs as $crumb) {
				if ($crumb->title !== $prefs['browsertitle']) {
					$ret[] = breadcrumb_buildHeadTitle($crumb);
				}
			}
			return implode(" : ", $ret);
		} elseif ($prefs['site_title_breadcrumb'] == 'desc') {
			return $crumbs->description;
		} else {
			return htmlspecialchars($crumbs->title);
		}
	} else {
		if ( is_array($crumbs) ) {
			return $crumbs[count($crumbs) - 1]->title;
		} else {
			return $crumbs->title;
		}
	}
}

/**
 *  @param crumbs array of breadcrumb instances
 *  @param loc where the description will be used: {site|page} site header or page header
 */
/* static */
function breadcrumb_buildTrail($crumbs, $loc, $showLinks = true)
{
	global $prefs, $info;
	if ($prefs['feature_breadcrumbs'] == 'y') {
		if ($loc == 'page' && ($prefs['feature_siteloc'] == 'page' || ($prefs['feature_page_title'] == 'y' && $info) ) ) {
			return _breadcrumb_buildTrail($crumbs, -1, -1, $showLinks);
		} else if (($loc == 'site' || $loc == 'location') && $prefs['feature_siteloc'] == 'y') {
			return _breadcrumb_buildTrail($crumbs, -1, -1, $showLinks);
		} else if ($loc != 'page' && $loc != 'site' && $loc != 'location' && $loc != 'admin') {
			return _breadcrumb_buildTrail($crumbs, -1, -1, $showLinks);
		}
	} else if ($loc == "admin" && $prefs['feature_breadcrumbs'] == 'y') {
		return _breadcrumb_buildTrail($crumbs, -1, -1, $showLinks);
	}
}

/**
 *  @param crumbs array of breadcrumb instances
 */
/* private static */
function _breadcrumb_buildTrail($crumbs, $len=-1, $cnt=-1, $showLinks = true)
{
	global $structure, $structure_path, $prefs, $info;

	$seper = ' ' . htmlentities($prefs['site_crumb_seper'], ENT_QUOTES, "UTF-8") . ' ';

	switch ($prefs['feature_sitetitle']) {
		case ('y'):
			$loclass = "pagetitle";
			$hiclass = "pagetitle";
			break;
		case ('title'):
			$loclass = "crumblink";
			$hiclass = "pagetitle";
			break;
		case ('n'):
		default:
			$loclass = "crumblink";
			$hiclass = "crumblink";
			break;
	}

	if ($prefs['feature_breadcrumbs'] == 'n') {
		$loclass = "crumblink";
		$hiclass = "pagetitle";
	}

	if ($len == -1) {
		$len = count($crumbs);
	}

	if ( _is_assoc($crumbs) ) {
		return false;
	}

	if ( is_array($crumbs) ) {
		$ret = array();
		if ( ($structure == 'y') && $info ) {
			$cnt +=1;
			$ret = breadcrumb_buildStructureTrail($structure_path, $cnt, $loclass, $showLinks);
			// prepend the root crumb
			array_unshift($ret, _breadcrumb_buildCrumb($crumbs[$cnt], $cnt, $loclass, $showLinks));
			if (count($crumbs) > 1) {
				$ret[] = _breadcrumb_buildCrumb($crumbs[count($crumbs) - 1], count($ret) - 1, $loclass, $showLinks);
			}
		} else {
			foreach ($crumbs as $crumb) {
				$cnt += 1;
				$ret[] = _breadcrumb_buildCrumb($crumb, $cnt, $loclass, $showLinks);
			}
		}
		$ret = array_filter($ret);
		return implode($seper, $ret);
	} else {
		return _breadcrumb_buildCrumb($crumbs, $cnt, $loclass, $showLinks);
	}
}

/**
 *  Returns a single html-formatted crumb
 *  @param crumb a breadcrumb instance
 *  @param cnt the position of this crumb in the trail, starting at 1
 */
/* static */
function _breadcrumb_buildCrumb($crumb, $cnt, $loclass, $showLinks = true)
{
	if ($crumb->hidden) {
		return '';
	}
	include_once('tiki-sefurl.php');
	$url = filter_out_sefurl($crumb->url);

	$cnt += 1;
	$ret = '';
	if ($showLinks) {
		$ret .= '<a class="'.$loclass.'" title="';
		$ret .= tra($crumb->description);
		$ret .= '" accesskey="'.($cnt);
		$ret .= '" href="'.$url.'">';
	}
	$ret .= tra($crumb->title);
	if ($showLinks) {
		$ret .= '</a>';
		$ret .= help_doclink(array('crumb'=>$crumb));
	}
	return $ret;
}

/**
 *  Returns an html-formatted partial trail for this structure_path
 *  @param structure_path the structure path from which to build the trail
 *  @param cnt starting position in trail
 *  @param loclass the css class
 */
/* static */
function breadcrumb_buildStructureTrail($structure_path, $cnt, $loclass, $showLinks = true)
{
	global $structure, $info, $page;
	$len = count($structure_path) + $cnt;
	TikiLib::lib('smarty')->loadPlugin('smarty_function_sefurl');		// special sefurl only for structures - TODO merge with others

	if ($structure != 'y' || !$info) {
		return false;
	}

	$res = array();

	foreach ($structure_path as $crumb) {
		$cnt += 1;
		if ( $len!=$cnt ) {

			$ret = '';
			if ($showLinks && ($crumb['pageName'] != $page || $crumb['page_alias'] != $page)) {
				$url = smarty_function_sefurl(array('page' => $crumb['pageName'], 'structure' => $structure_path[0]['pageName']), TikiLib::lib('smarty'));
				$ret .= '<a class="' . $loclass . '" accesskey="' . ($cnt) . '" href="' . $url . '">';
			}
			if ($crumb['page_alias']) {
				$ret .= $crumb['page_alias'];
			} else {
				$ret .= $crumb['pageName'];
			}
			if ($showLinks && ($crumb['pageName'] != $page || $crumb['page_alias'] != $page)) {
				$ret .= '</a>';
			}
			$res[] = $ret;

		} else {
			$res[] = '';
		}
	}
	return $res;
}

/**
 * @param $crumbs
 * @param $menuId
 * @param null $startLevel
 * @param null $stopLevel
 * @return array
 */
function breadcrumb_buildMenuCrumbs($crumbs, $menuId, $startLevel = null, $stopLevel = null)
{

	include_once('lib/smarty_tiki/function.menu.php');
	list($menu_info, $menuOptions) = get_menu_with_selections(array('id' => $menuId));
	$newCrumbs = array();
	if (count($crumbs) > 0) {
		$newCrumbs[] = $crumbs[0];
	}

	$level = 0;
	$foundSelected = false;

	foreach ($menuOptions['data'] as $option) {
		if (!empty($option['selectedAscendant']) || !empty($option['selected'])) {
			$foundSelected = true;
			if ($startLevel === null || $level >= $startLevel) {
				if ($stopLevel === null || $level <= $stopLevel) {
					$newCrumbs[] = new Breadcrumb($option['name'], '', $option['sefurl']);
				}
			}
			$level++;
		}
	}

	if (!$foundSelected && count($crumbs) > 1) {
		$newCrumbs[] = $crumbs[1];
	}
	return $newCrumbs;
}

/**
 *  Returns the html-formatted page title, if appropriate
 *  @param crumbs array of breadcrumb instances
 *  @param loc where the description will be used: {site|page} site header or page header
 */
/* static */
function breadcrumb_getTitle($crumbs, $loc)
{
	global $prefs, $info;

	if ($prefs['feature_breadcrumbs'] == 'n' && ($prefs['feature_wiki_description'] == 'y' && $info)) {
		return _breadcrumb_getTitle($crumbs, $loc);
	} else if ($prefs['feature_breadcrumbs'] == 'n' && $loc == "admin") {
		return _breadcrumb_getTitle($crumbs, $loc);
	} else if ($prefs['feature_breadcrumbs'] == 'y') {
		if ($loc == 'page' && ($prefs['feature_siteloc'] == 'page' || ($prefs['feature_page_title'] == 'y' && $info) ) ) {
			return _breadcrumb_getTitle($crumbs, $loc);
		} else if (($loc == 'site' || $loc == 'location') && $prefs['feature_siteloc'] == 'y') {
			return _breadcrumb_getTitle($crumbs, $loc);
		}
	} else if ($loc == "admin") {
		return _breadcrumb_getTitle($crumbs, 'page');
	} else if ($prefs['feature_breadcrumbs'] != 'y' && $loc == "page" && $prefs['feature_page_title'] == 'y') {// for previous compatibility
		return _breadcrumb_getTitle($crumbs, 'page');
	}
	return;
}

/**
 *  Returns the html-formatted page title, if appropriate
 *  @param crumbs array of breadcrumb instances
 *  @param loc where the description will be used: {site|page} site header or page header
 */
/* static */
function _breadcrumb_getTitle($crumbs, $loc)
{
	global $prefs, $print_page, $info, $structure, $structure_path;
	$smarty = TikiLib::lib('smarty');
	$tikilib = TikiLib::lib('tiki');
	$len = count($crumbs);

	if ( $prefs['feature_breadcrumbs'] == 'n' || $prefs['feature_sitetitle'] == 'title' ) {
		$smarty->loadPlugin('smarty_modifier_sefurl');
		$smarty->loadPlugin('smarty_modifier_escape');

		$class = "";
		$metadata = '';

		$current = current_object();
		$escapedHref = smarty_modifier_escape(smarty_modifier_sefurl($current['object'], $current['type']));

		if ($coordinates = TikiLib::lib('geo')->get_coordinates($current['type'], $current['object'])) {
			$class = ' geolocated primary';
			$metadata = " data-geo-lat=\"{$coordinates['lat']}\" data-geo-lon=\"{$coordinates['lon']}\"";

			if (isset($coordinates['zoom'])) {
				$metadata .= " data-geo-zoom=\"{$coordinates['zoom']}\"";
			}
		}

		$ret = '<a class="'.$class.'"' . $metadata . ' title="'.tra("refresh").'" href="' . $escapedHref . '">';
	} else {
		$class = "crumblink";
		$ret = '<a class="'.$class.'" title="';
		if ( ($structure == 'y') && $info ) {
			$cnt = count($structure_path);
		} else {
			$cnt = count($crumbs);
		}
		$ret .= tra("go back to this crumb");
		$ret .= '" accesskey="'.($cnt);
		include_once('tiki-sefurl.php');
		$ret .= '" href="'.filter_out_sefurl($crumbs[$len-1]->url).'">';
	}
	if ($prefs['feature_breadcrumbs'] == 'n' && $loc == "admin") {
		$ret .= tra("Administration:")." ";
	}

	// Should show alias if in structure
	$cur_title = $crumbs[$len-1]->title;
	if ($structure == 'y') {
		foreach ($structure_path as $crumb){
			if ($crumb['pageName'] == $cur_title && $crumb['page_alias'] != '') {
				$cur_title = $crumb['page_alias'];
			}
		}
	}
	if (!empty($prefs['wiki_pagename_strip']) || $prefs['namespace_indicator_in_page_title'] == 'y') {
		include_once('lib/smarty_tiki/modifier.pagename.php');
		$ret .= tra(smarty_modifier_pagename($cur_title)).'</a>';
	} else {
		$ret .= htmlentities(tra($cur_title), ENT_QUOTES, 'UTF-8').'</a>';
	}
	$ret .= help_doclink(array('crumb'=>$crumbs[$len-1]));
	if ( isset($info['flag']) && $info['flag'] == 'L' && $print_page != 'y' ) {
		$smarty->loadPlugin('smarty_function_icon');
		$ret .= smarty_function_icon(['name' => 'lock', 'iclass' => 'tips', 'ititle' => ':' . tra('Locked by')
			. $info['user']], $smarty);
	}
	return $ret;
}

/**
 *  Returns the html-formatted page description if appropriate
 *  @param crumbs array of breadcrumb instances
 *  @param loc where the description will be used: {site|page} site header or page header
 */
/* static */
function breadcrumb_getDescription($crumbs, $loc)
{
	global $prefs, $info;
	$len = count($crumbs);
	if ($prefs['feature_breadcrumbs'] == 'y') {
		if ($loc == 'page' && ($prefs['feature_sitedesc'] == 'page' || ($prefs['feature_wiki_description'] == 'y' && $info) )) {
			return '<span id="description">'.tra($crumbs[$len-1]->description).'</span>';
		} else if ($loc == 'site' && $prefs['feature_sitedesc'] == 'y' ) {
			return '<span id="description">'.tra($crumbs[$len-1]->description).'</span>';
		} else if ($loc == 'head') {
			return tra($crumbs[$len-1]->description);
		}
	} else if ( !($prefs['feature_wiki_description'] == 'n' && $info)) {
		return tra($crumbs[$len-1]->description);
	}
}

/* private */
/**
 * @param $var
 * @return bool
 */
function _is_assoc($var)
{
	return is_array($var) && array_keys($var) !== range(0, count($var) - 1);
}

