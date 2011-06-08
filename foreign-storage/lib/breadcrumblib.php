<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

include_once("tikihelplib.php");

class Breadcrumb
{
	var $title;
	var $description;
	var $url;
	var $helpUrl;
	var $helpDescription;

	function Breadcrumb($title, $desc='', $url='', $helpurl='', $helpdesc='') {
                if ($title == '') {
		    $this->title = 'Home';
		} else {
		    $this->title = $title;
		}
		$this->description = $desc;
		$this->url = $url;
		$this->helpUrl = $helpurl;
		$this->helpDescription = $helpdesc;
        }
/* end of class */
}

/* static */
function breadcrumb_buildHeadTitle($crumbs) {
	global $prefs;
	if ($prefs['feature_breadcrumbs'] === 'y') {
	    if( _is_assoc($crumbs) ) {
	        return false;
	    }
	    if( is_array($crumbs) ) {
	        $ret = array();
	        foreach($crumbs as $crumb) {
	            $ret[] = breadcrumb_buildHeadTitle($crumb);
	        }
	        return implode(" : ", $ret);
		} elseif ($prefs['site_title_breadcrumb'] == 'desc') {
			return $crumbs->description;
	    } else {
	        return htmlspecialchars($crumbs->title);
	    }
	} else {
	    if( is_array($crumbs) ) {
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
function breadcrumb_buildTrail($crumbs, $loc) {
    global $prefs, $info;
    if($prefs['feature_breadcrumbs'] == 'y') {
        if ($loc == 'page' && ($prefs['feature_siteloc'] == 'page' || ($prefs['feature_page_title'] == 'y' && $info) ) ) {
            return _breadcrumb_buildTrail($crumbs);
        } else if (($loc == 'site' || $loc == 'location') && $prefs['feature_siteloc'] == 'y') {
            return _breadcrumb_buildTrail($crumbs);
        } else if ($loc != 'page' && $loc != 'site' && $loc != 'location' && $loc != 'admin') {
            return _breadcrumb_buildTrail($crumbs);
        }
    } else if ($loc == "admin" && $prefs['feature_breadcrumbs'] == 'y') {
        return _breadcrumb_buildTrail($crumbs);
    }
}

/**
 *  @param crumbs array of breadcrumb instances
 */
/* private static */
function _breadcrumb_buildTrail($crumbs, $len=-1, $cnt=-1) {
    global $structure, $structure_path, $prefs, $print_page, $info;

    $seper = ' '.htmlentities($prefs['site_crumb_seper'],ENT_QUOTES,"UTF-8").' ';
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
    if( _is_assoc($crumbs) ) {
        return false;
    }
    if( is_array($crumbs) ) {                             
        $ret = array();
        if ( ($structure == 'y') && $info ) {
            $cnt +=1;
            $ret = breadcrumb_buildStructureTrail($structure_path, $cnt, $loclass);
            // prepend the root crumb
            array_unshift($ret, _breadcrumb_buildCrumb($crumbs[$cnt], $cnt, $loclass));
        } else {
            foreach($crumbs as $crumb) {
    	    $cnt+=1;
    	    if( $len!=$cnt+1 ) {
                    $ret[] = _breadcrumb_buildCrumb($crumb, $cnt, $loclass);
                } else {
                    $ret[] = '';
                }
            }
        }
        return implode($seper, $ret);
    } else {                         
        return _breadcrumb_buildCrumb($crumbs, $cnt, $loclass);
    }
}

/**
 *  Returns a single html-formatted crumb
 *  @param crumb a breadcrumb instance
 *  @param cnt the position of this crumb in the trail, starting at 1
 */
/* static */
function _breadcrumb_buildCrumb($crumb, $cnt, $loclass) {
    $cnt+=1;
    $ret = '<a class="'.$loclass.'" title="';
    $ret .= tra($crumb->description);
    $ret .= '" accesskey="'.($cnt);
    $ret .= '" href="'.$crumb->url.'">'.tra($crumb->title).'</a>';
    $ret .= help_doclink(array('crumb'=>$crumb));
    return $ret;
}

/**
 *  Returns an html-formatted partial trail for this structure_path
 *  @param structure_path the structure path from which to build the trail
 *  @param cnt starting position in trail
 *  @param loclass the css class
 */
/* static */
function breadcrumb_buildStructureTrail($structure_path, $cnt, $loclass) {
    global $structure, $info, $page;
    $len = count($structure_path) + $cnt;

    if ($structure != 'y' || !$info) { return false; }
    $res = array();
    foreach ($structure_path as $crumb) {
        $cnt+=1;
        if( $len!=$cnt ) {

        $ret = '';
        if ($crumb['pageName'] != $page || $crumb['page_alias'] != $page) {
            $ret .= '<a class="'.$loclass.'" accesskey="'.($cnt).'" href="tiki-index.php?page_ref_id='.$crumb['page_ref_id'].'">';
        }
        if ($crumb['page_alias']) {
            $ret .= $crumb['page_alias'];
        } else {
            $ret .= $crumb['pageName'];
        }
        if ($crumb['pageName'] != $page || $crumb['page_alias'] != $page) {
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
 *  Returns the html-formatted page title, if appropriate
 *  @param crumbs array of breadcrumb instances
 *  @param loc where the description will be used: {site|page} site header or page header
 */
/* static */
function breadcrumb_getTitle($crumbs, $loc) {
    global $prefs, $print_page, $info;

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
function _breadcrumb_getTitle($crumbs, $loc) {
    global $prefs, $print_page, $info, $structure, $structure_path, $tikilib, $smarty;

	$len = count($crumbs);
	
    if ( $prefs['feature_breadcrumbs'] == 'n' || $prefs['feature_sitetitle'] == 'title' ) {
		require_once 'lib/smarty_tiki/modifier.sefurl.php';
		if (! function_exists('smarty_modifier_escape')) {
			require_once 'lib/smarty_tiki/modifier.escape.php';
		}

        $class = "pagetitle";
		$metadata = '';

		$current = current_object();
		$escapedHref = smarty_modifier_escape( smarty_modifier_sefurl( $current['object'], $current['type'] ) );

		if ($coordinates = TikiLib::lib('geo')->get_coordinates($current['type'], $current['object'])) {
			$class = ' geolocated primary';
			$metadata = " data-geo-lat=\"{$coordinates['lat']}\" data-geo-lon=\"{$coordinates['lon']}\"";
			
			if (isset($coordinates['zoom'])) {
				$metadata .= " data-geo-zoom=\"{$coordinates['zoom']}\"";
			}
		}

        $ret = '<strong><a class="'.$class.'"' . $metadata . ' title="'.tra("refresh").'" href="' . $escapedHref . '">';
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
		$ret .= '" href="'.filter_out_sefurl($crumbs[$len-1]->url, $smarty).'">';
    }
    if ($prefs['feature_breadcrumbs'] == 'n' && $loc == "admin")
        $ret .= tra("Administration:")." ";
        if ($prefs['wikiapproval_hideprefix'] == 'y' && $approved = $tikilib->get_approved_page( $crumbs[$len-1]->title ) ) { 
        	$crumbs[$len-1]->title = $approved;
		}
    			if (!empty($prefs['wiki_pagename_strip'])) {
    				include_once('lib/smarty_tiki/modifier.pagename.php');
    				$ret .= tra(smarty_modifier_pagename($crumbs[$len-1]->title)).'</a>';
    			} else {
    				$ret .= htmlentities(tra($crumbs[$len-1]->title), ENT_QUOTES, 'UTF-8').'</a>';
    			}
    $ret .= help_doclink(array('crumb'=>$crumbs[$len-1]));
    if( isset($info['flag']) && $info['flag'] == 'L' && $print_page != 'y' ) {
        $ret .= ' <img src="pics/icons/lock.png" height="16" width="16" alt="'.tra('locked').'" title="'.tra('locked by').' '.$info['user'].'" />';
    }
    if( $prefs['feature_breadcrumbs'] == 'n' || $prefs['feature_sitetitle'] == 'title' ) {
        $ret .= '</strong>';          
    }
    return $ret;
}

/**
 *  Returns the html-formatted page description if appropriate
 *  @param crumbs array of breadcrumb instances
 *  @param loc where the description will be used: {site|page} site header or page header
 */
/* static */
function breadcrumb_getDescription($crumbs, $loc) {
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
function _is_assoc($var) {
   return is_array($var) && array_keys($var)!==range(0,count($var)-1);
}
		
