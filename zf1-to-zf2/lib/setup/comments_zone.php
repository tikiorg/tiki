<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
$access->check_script($_SERVER['SCRIPT_NAME'], basename(__FILE__));

/*
 * Show comments zone on page load by default
 */
$comzone = $_REQUEST['comzone'];
if ( $comzone == 'show' ) {
	if ( strstr($_SERVER['REQUEST_URI'], 'tiki-read_article') and $prefs['feature_article_comments'] == 'y' )
		$prefs['show_comzone'] = 'y';

	if ( strstr($_SERVER['REQUEST_URI'], 'tiki-poll_results') and $prefs['feature_poll_comments'] == 'y' )
		$prefs['show_comzone'] = 'y';

	if ( strstr($_SERVER['REQUEST_URI'], 'tiki-index') and $prefs['feature_wiki_comments'] == 'y' )
		$prefs['show_comzone'] = 'y';

	if ( strstr($_SERVER['REQUEST_URI'], 'tiki-view_faq') and $prefs['feature_faq_comments'] == 'y' )
		$prefs['show_comzone'] = 'y';

	if ( strstr($_SERVER['REQUEST_URI'], 'tiki-browse_gallery') and $prefs['feature_image_galleries_comments'] == 'y' )
		$prefs['show_comzone'] = 'y';

	if ( strstr($_SERVER['REQUEST_URI'], 'tiki-list_file_gallery') and $prefs['feature_file_galleries_comments'] == 'y' )
		$prefs['show_comzone'] = 'y';

	if ( strstr($_SERVER['REQUEST_URI'], 'tiki-view_blog_post') and $prefs['feature_blogposts_comments'] == 'y' )
		$prefs['show_comzone'] = 'y';

	if ( strstr($_SERVER['REQUEST_URI'], 'tiki-map') and $prefs['feature_map_comments'] == 'y' )
		$prefs['show_comzone'] = 'y';

	if ( $prefs['show_comzone'] == 'y' )
		$smarty->assign('show_comzone', 'y');
}
