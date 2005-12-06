<?php 
// $Header: /cvsroot/tikiwiki/tiki/freetag_apply.php,v 1.3 2005-12-06 20:10:53 lfagundes Exp $

// Copyright (c) 2002-2005, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

//this script may only be included - so its better to err & die if called directly.
//smarty is not there - we need setup
require_once('tiki-setup.php');  
$access->check_script($_SERVER["SCRIPT_NAME"],basename(__FILE__));

global $feature_freetags;

if ($feature_freetags == 'y') {

    global $freetaglib;
    if (!is_object($freetaglib)) {
	include_once('lib/freetag/freetaglib.php');
    }

    $tag_string = $_REQUEST['freetag_string'];
    
    // Use same parameters passed to categorize.php, makes simpler implementation
    // and keep consistency
    $old_tags = $freetaglib->get_tags_on_object($cat_objid, $cat_type);

    global $user;

    $freetaglib->add_object($cat_type, $cat_objid, $cat_desc, $cat_name, $cat_href);	
    $freetaglib->update_tags($user, $cat_objid, $cat_type, $tag_string);

}

?>
