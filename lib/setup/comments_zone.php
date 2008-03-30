<?php

// $Id$
// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for
// details.

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'],'tiki-setup.php')!=FALSE) {
  header('location: index.php');
  exit;
}

/*
 * Show comments zone on page load by default
 */
$comzone = $_REQUEST['comzone'];
if ( $comzone == 'show' ) {
	if ( strstr($_SERVER['REQUEST_URI'], 'tiki-read_article') and $prefs['feature_article_comments'] == 'y' ) $prefs['show_comzone'] = 'y';
	if ( strstr($_SERVER['REQUEST_URI'], 'tiki-poll_results') and $prefs['feature_poll_comments'] == 'y' ) $prefs['show_comzone'] = 'y';
	if ( strstr($_SERVER['REQUEST_URI'], 'tiki-index') and $prefs['feature_wiki_comments'] == 'y' ) $prefs['show_comzone'] = 'y';
	if ( strstr($_SERVER['REQUEST_URI'], 'tiki-view_faq') and $prefs['feature_faq_comments'] == 'y' ) $prefs['show_comzone'] = 'y';
	if ( strstr($_SERVER['REQUEST_URI'], 'tiki-browse_gallery') and $prefs['feature_image_galleries_comments'] == 'y' ) $prefs['show_comzone'] = 'y';
	if ( strstr($_SERVER['REQUEST_URI'], 'tiki-list_file_gallery') and $prefs['feature_file_galleries_comments'] == 'y' ) $prefs['show_comzone'] = 'y';
	if ( strstr($_SERVER['REQUEST_URI'], 'tiki-view_blog') and $prefs['feature_blog_comments'] == 'y' ) $prefs['show_comzone'] = 'y';
	if ( strstr($_SERVER['REQUEST_URI'], 'tiki-view_blog_post') and $prefs['feature_blogposts_comments'] == 'y' ) $prefs['show_comzone'] = 'y';
	if ( strstr($_SERVER['REQUEST_URI'], 'tiki-map') and $prefs['feature_map_comments'] == 'y' ) $prefs['show_comzone'] = 'y';
	if ( $prefs['show_comzone'] == 'y' ) $smarty->assign('show_comzone', 'y');
}
