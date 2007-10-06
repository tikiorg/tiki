<?php

// $Header: /cvsroot/tikiwiki/tiki/lib/setup/comments_zone.php,v 1.1 2007-10-06 15:18:43 nyloth Exp $
// Copyright (c) 2002-2005, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
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
	if ( strstr($_SERVER['REQUEST_URI'], 'tiki-read_article') and $feature_article_comments == 'y' ) $show_comzone = 'y';
	if ( strstr($_SERVER['REQUEST_URI'], 'tiki-poll_results') and $feature_poll_comments == 'y' ) $show_comzone = 'y';
	if ( strstr($_SERVER['REQUEST_URI'], 'tiki-index') and $feature_wiki_comments == 'y' ) $show_comzone = 'y';
	if ( strstr($_SERVER['REQUEST_URI'], 'tiki-view_faq') and $feature_faq_comments == 'y' ) $show_comzone = 'y';
	if ( strstr($_SERVER['REQUEST_URI'], 'tiki-browse_gallery') and $feature_image_galleries_comments == 'y' ) $show_comzone = 'y';
	if ( strstr($_SERVER['REQUEST_URI'], 'tiki-list_file_gallery') and $feature_file_galleries_comments == 'y' ) $show_comzone = 'y';
	if ( strstr($_SERVER['REQUEST_URI'], 'tiki-view_blog') and $feature_blog_comments == 'y' ) $show_comzone = 'y';
	if ( strstr($_SERVER['REQUEST_URI'], 'tiki-view_blog_post') and $feature_blogposts_comments == 'y' ) $show_comzone = 'y';
	if ( strstr($_SERVER['REQUEST_URI'], 'tiki-map') and $feature_map_comments == 'y' ) $show_comzone = 'y';
	if ( $show_comzone == 'y' ) $smarty->assign('show_comzone', 'y');
}
