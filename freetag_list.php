<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to err & die if called directly.
//smarty is not there - we need setup
require_once('tiki-setup.php');  
$access->check_script($_SERVER["SCRIPT_NAME"], basename(__FILE__));

global $prefs;
global $tiki_p_view_freetags;

if ($prefs['feature_freetags'] == 'y' and $tiki_p_view_freetags == 'y') {

    $freetaglib = TikiLib::lib('freetag');

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

		$smarty->assign('taglist', $taglist);
    } else {
	$taglist = '';
    }

	if ( !isset($cat_lang))
		$cat_lang = null;

    $suggestion = $freetaglib->get_tag_suggestion($taglist, $prefs['freetags_browse_amount_tags_suggestion'], $cat_lang);

    $smarty->assign('tag_suggestion', $suggestion);
}
