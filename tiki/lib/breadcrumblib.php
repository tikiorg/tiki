<?php
/**
 * $Header: /cvsroot/tikiwiki/tiki/lib/breadcrumblib.php,v 1.11 2006-11-21 12:05:14 sylvieg Exp $
 * Copyright (c) 2002-2005, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
 * All Rights Reserved. See copyright.txt for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
 */

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

include_once("tikihelplib.php");

class Breadcrumb {
	var $title;
	var $description;
	var $url;
	var $helpUrl;
	var $helpDescription;

	function Breadcrumb($title, $desc='', $url='', $helpurl='', $helpdesc='') {
                $this->title = $title;
		$this->description = $desc;
		$this->url = $url;
		$this->helpUrl = $helpurl;
		$this->helpDescription = $helpdesc;
        }
/* end of class */
}

    /* static */
    function breadcrumb_buildHeadTitle($crumbs) {
        if( _is_assoc($crumbs) ) {
            return false;
        }
        if( is_array($crumbs) ) {
            $ret = array();
            foreach($crumbs as $crumb) {
                $ret[] = breadcrumb_buildHeadTitle($crumb);
            }
            return implode(" : ", $ret);
        } else {
            return $crumbs->title;
        }
    }
    
    /**
     *  @param crumbs array of breadcrumb instances
     *  @param loc where the description will be used: {site|page} site header or page header
     */
    /* static */
    function breadcrumb_buildTrail($crumbs, $loc) {
        global $feature_siteidentity, $feature_siteloc, $feature_page_title, $info, $feature_breadcrumbs;
        if( $feature_siteidentity == 'y' && $feature_breadcrumbs == 'y') {
            if ($loc == 'page' && ($feature_siteloc == 'page' || ($feature_page_title == 'y' && $info) ) ) {
                return _breadcrumb_buildTrail($crumbs);
            } else if (($loc == 'site' || $loc == 'location') && $feature_siteloc == 'y') {
                return _breadcrumb_buildTrail($crumbs);
            } else if ($loc != 'page' && $loc != 'site' && $loc != 'location' && $loc != 'admin') {
                return _breadcrumb_buildTrail($crumbs);
            }
        } else if ($loc == "admin" && $feature_breadcrumbs == 'y') {
            return _breadcrumb_buildTrail($crumbs);
        }
    }

    /**
     *  @param crumbs array of breadcrumb instances
     */
    /* private static */
    function _breadcrumb_buildTrail($crumbs, $len=-1, $cnt=-1) {
        global $structure, $structure_path, $feature_breadcrumbs, $feature_siteidentity;
        global $feature_sitetitle, $print_page, $info, $site_crumb_seper;

        $seper = ' '.htmlentities($site_crumb_seper,ENT_QUOTES,"UTF-8").' ';
        switch ($feature_sitetitle) {
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
        if ($feature_siteidentity == 'n' || $feature_breadcrumbs == 'n') {
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
        global $feature_siteidentity, $feature_wiki_description, $print_page, $info, $feature_siteloc, $feature_breadcrumbs;
        global $feature_page_title;

        if ((($feature_siteidentity == 'n') ||  $feature_breadcrumbs == 'n' ) && ($feature_wiki_description == 'y' && $info)) {
            return _breadcrumb_getTitle($crumbs, $loc);
        } else if ($feature_breadcrumbs == 'n' && $loc == "admin") {
            return _breadcrumb_getTitle($crumbs, $loc);
        } else if ($feature_siteidentity == 'y' &&  $feature_breadcrumbs == 'y') {
            if ($loc == 'page' && ($feature_siteloc == 'page' || ($feature_page_title == 'y' && $info) ) ) {
                return _breadcrumb_getTitle($crumbs, $loc);
            } else if (($loc == 'site' || $loc == 'location') && $feature_siteloc == 'y') {
                return _breadcrumb_getTitle($crumbs, $loc);
            }
        } else if ($loc == "admin") {
            return _breadcrumb_getTitle($crumbs, 'page');
         } else if ($feature_breadcrumbs != 'y' && $loc == "page" && $feature_page_title == 'y') {// for previous compatibility
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
        global $feature_siteidentity, $feature_wiki_description, $feature_sitetitle, $print_page, $info, $feature_breadcrumbs;
        global $structure, $structure_path, $wiki_pagename_strip;
    
        if ( ($feature_siteidentity == 'n'|| $feature_breadcrumbs == 'n') || $feature_sitetitle == 'title' ) {
            $class = "pagetitle";
            $ret = '<h1><a class="'.$class.'" title="';
        } else if ( $feature_sitetitle == 'y' ) {
            $class = "pagetitle";
	    $ret = '<a class="'.$class.'" title="';
        } else {
            $class = "crumblink";
	    $ret = '<a class="'.$class.'" title="';
        }
        $len = count($crumbs);
        if ( ($structure == 'y') && $info ) {
            $cnt = count($structure_path);
        } else {
            $cnt = count($crumbs);                      
        }
        $ret .= 'refresh';
        $ret .= '" accesskey="'.($cnt);
        $ret .= '" href="'.$crumbs[$len-1]->url.'">';
        if ($feature_breadcrumbs == 'n' && $loc == "admin")
            $ret .= tra("Administration:")." ";
				if (!empty($wiki_pagename_strip)) {
					$wiki_strip = '~'. preg_quote($wiki_pagename_strip).'[a-zA-Z0-9]*$~';
					$ret .= tra(preg_replace($wiki_strip,'',$crumbs[$len-1]->title)).'</a>';
				} else {
        	$ret .= tra($crumbs[$len-1]->title).'</a>';
				}
        $ret .= help_doclink(array('crumb'=>$crumbs[$len-1]));
        if( $info['flag'] == 'L' && $print_page != 'y' ) {
            $ret .= ' <img src="pics/icons/lock.png" border="0" height="16" width="16" alt="'.tra('locked').'" title="'.tra('locked by').' '.$info['user'].'" />';
        }
        if( ($feature_siteidentity == 'n' || $feature_breadcrumbs == 'n') || $feature_sitetitle == 'title' ) {
            $ret .= '</h1>';          
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
        global $feature_siteidentity, $feature_sitedesc, $feature_wiki_description, $info, $feature_breadcrumbs;
        $len = count($crumbs);
        if ($feature_siteidentity == 'y' && $feature_breadcrumbs == 'y') {
            if ($loc == 'page' && ($feature_sitedesc == 'page' || ($feature_wiki_description == 'y' && $info) )) {
                return '<div id="description">'.tra($crumbs[$len-1]->description).'</div>';
            } else if ($loc == 'site' && $feature_sitedesc == 'y' ) {
                return '<div id="description">'.tra($crumbs[$len-1]->description).'</div>';
            }
        } else if ( !($feature_wiki_description == 'n' && $info)) {
            return '<div id="description">'.tra($crumbs[$len-1]->description).'</div>';
        }
    }

    /* private */
    function _is_assoc($var) {
       return is_array($var) && array_keys($var)!==range(0,sizeof($var)-1);
    }
		
?>
