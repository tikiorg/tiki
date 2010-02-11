<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to err & die if called directly.
//smarty is not there - we need setup
require_once('tiki-setup.php');  
$access->check_script($_SERVER["SCRIPT_NAME"],basename(__FILE__));

global $prefs;
global $tiki_p_view_freetags;

if ($prefs['feature_freetags'] == 'y' and $tiki_p_view_freetags == 'y') {

    global $freetaglib;
    if (!is_object($freetaglib)) {
	include_once('lib/freetag/freetaglib.php');
    }

    if (isset($cat_objid)) {

	$tags = $freetaglib->get_tags_on_object($cat_objid, $cat_type);	
	$tagarray = array(); 
	$taglist = '';
	if (!empty($tags['data'])) {
		foreach ($tags['data'] as $tag) {
			if (strstr($tag['tag'], ' ')) {
				$taglist .= '"'.$tag['tag'] . '" ';
			} else {
				$taglist .= $tag['tag'] . ' ';
			}
		    $tagarray[] = $tag['tag'];
		}
	}

    if ($prefs['feature_wikiapproval'] == 'y' && $prefs['wikiapproval_combine_freetags'] == 'y'
	 && $cat_type == 'wiki page' && $approved = $tikilib->get_approved_page($cat_objid)) {
	 	// to combine tags from approved page 
		$approvedPageName = $approved;
		$approvedTags = $freetaglib->get_tags_on_object($approvedPageName, $cat_type);
		foreach($approvedTags['data'] as $approvedTag) {
	    	if (!in_array($approvedTag['tag'],$tagarray)) {
					$taglist .= $approvedTag['tag'] . ' ';
				}
		}		
	}
	
	$smarty->assign('taglist',$taglist);
    } else {
	$taglist = '';
    }

	if( !isset($cat_lang))
		$cat_lang = null;

    $suggestion = $freetaglib->get_tag_suggestion($taglist,$prefs['freetags_browse_amount_tags_suggestion'],$cat_lang);

    $smarty->assign('tag_suggestion',$suggestion);
}
