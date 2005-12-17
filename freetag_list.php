<?php

// $Header: /cvsroot/tikiwiki/tiki/freetag_list.php,v 1.4 2005-12-17 19:32:50 lfagundes Exp $

// Copyright (c) 2002-2005, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

//this script may only be included - so its better to err & die if called directly.
//smarty is not there - we need setup
require_once('tiki-setup.php');  
$access->check_script($_SERVER["SCRIPT_NAME"],basename(__FILE__));

global $feature_freetags;
global $tiki_p_view_freetags;

if ($feature_freetags == 'y' and $tiki_p_view_freetags == 'y') {

    global $freetaglib;
    if (!is_object($freetaglib)) {
	include_once('lib/freetag/freetaglib.php');
    }

    if (isset($cat_objid)) {

	$tags = $freetaglib->get_tags_on_object($cat_objid, $cat_type);
	
	$taglist = '';
	for ($i=0; $i<sizeof($tags['data']); $i++) {
	    $taglist .= $tags['data'][$i]['tag'] . ' ';
	}

	$smarty->assign('taglist',$taglist);
    } else {
	$taglist = '';
    }

    $suggestion = $freetaglib->get_tag_suggestion($taglist,10);

    $smarty->assign('tag_suggestion',$suggestion);
}

?>
