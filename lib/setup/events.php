<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$events = TikiLib::events();

if( $prefs['quantify_changes'] == 'y' && $prefs['feature_multilingual'] == 'y' ) {
	$events->bind('tiki.wiki.update', Event_Lib::defer('quantify', 'wiki_update'));
}

if ($prefs['unified_incremental_update'] == 'y') {
	$events->bind('tiki.save', 'tiki_save_refresh_index');
}

// Chain events
$events->bind('tiki.wiki.update', 'tiki.wiki.save');
$events->bind('tiki.wiki.create', 'tiki.wiki.save');
$events->bind('tiki.wiki.save', 'tiki.save');

function tiki_save_refresh_index($args) {
	require_once('lib/search/refresh-functions.php');
	refresh_index($args['type'], $args['object']);
}

