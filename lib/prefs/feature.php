<?php

function prefs_feature_list() {
	return array(
		'feature_wiki' => array(
			'name' => tra('Wiki'),
			'description' => tra('Collaboratively authored documents with history of changes.'),
			'type' => 'flag',
			'help' => 'Wiki',
		),
		'feature_blogs' => array(
			'name' => tra('Blog'),
			'description' => tra('Online diaries or journals.'),
			'type' => 'flag',
			'help' => 'Blogs',
		),
		'feature_galleries' => array(
			'name' => tra('Image Gallery'),
			'description' => tra('Collections of graphic images for viewing or downloading (photo album)'),
			'type' => 'flag',
			'help' => 'Image+Gallery',
		),
		'feature_machine_translation' => array(
			'name' => tra('Machine Translation (by Google Translate)'),
			'description' => tra('Uses Google Translate to translate the content of wiki pages to other languages.'),
			'help' => 'Translating+Tiki+Content',
			'warning' => tra('Experimental. This feature is still under development.'),
			'type' => 'flag',
		),	
		'feature_trackers' => array(
			'name' => tra('Trackers'),
			'description' => tra('Database & form generator'),
			'help' => 'Trackers',
			'type' => 'flag',
		),
		'feature_forums' => array(
			'name' => tra('Forums'),
			'description' => tra('Online discussions on a variety of topics. Threaded or flat.'),
			'help' => 'Forums',
			'type' => 'flag',
		),
		'feature_file_galleries' => array(
			'name' => tra('File Gallery'),
			'description' => tra('Computer files, videos or software for downloading. With check-in & check-out (lock)'),
			'help' => 'File Gallery',
			'type' => 'flag',
		),
		'feature_articles' => array(
			'name' => tra('Articles'),
			'description' => tra('Articles can be used for date-specific news and announcements. You can configure articles to automatically publish and expire at specific times or to require that submissions be approved before becoming "live."'),
			'help' => 'Article',
			'type' => 'flag',
		),
		'feature_polls' => array(
			'name' => tra('Polls'),
			'description' => tra('Brief list of votable options; appears in module (left or right column)'),
			'help' => 'Poll',
			'type' => 'flag',
		),
		'feature_newsletters' => array(
			'name' => tra('Newletters'),
			'description' => tra('Content mailed to registered users.'),
			'help' => 'Newsletters',
			'type' => 'flag',
		),
		'feature_calendar' => array(
			'name' => tra('Calendar'),
			'description' => tra('Events calendar with public, private and group channels.'),
			'help' => 'Calendar',
			'type' => 'flag',
		),
		'feature_banners' => array(
			'name' => tra('Banners'),
			'description' => tra('Insert, track, and manage advertising banners.'),
			'help' => 'Banners',
			'type' => 'flag',
		),
		'feature_categories' => array(
			'name' => tra('Category'),
			'description' => tra('Global category system. Items of different types (wiki pages, articles, tracker items, etc) can be added to one or many categories. Categories can have permissions.'),
			'help' => 'Category',
			'type' => 'flag',
		),
		'feature_score' => array(
			'name' => tra('Score'),
			'description' => tra('Score is a game to motivate participants to increase their contribution by comparing to other users.'),
			'help' => 'Score',
			'type' => 'flag',
		),
		'feature_search' => array(
			'name' => tra('Search'),
			'description' => tra('Enables searching for content on the website.'),
			'help' => 'Search',
			'type' => 'flag',
		),
		'feature_freetags' => array(
			'name' => tra('Freetags'),
			'description' => tra('Allows to set tags on pages and various objects within the website and generate tag cloud navigation patterns.'),
			'help' => 'Tags',
			'type' => 'flag',
		),
		'feature_actionlog' => array(
			'name' => tra('Action Log'),
			'description' => tra('Allows to keep track of what users are doing and produce reports on a per-user or per-category basis.'),
			'help' => 'Action+Log',
			'type' => 'flag',
		),
		'feature_contribution' => array(
			'name' => tra('Contribution'),
			'description' => tra('Allows users to specify the type of contribution they are making while editing objects. The contributions are then displayed as color-coded in history and other reports.'),
			'help' => 'Contribution',
			'type' => 'flag',
		),
		'feature_multilingual' => array(
			'name' => tra('Multilingual'),
			'description' => tra('Enables internationalization features and multilingual support for then entire site.'),
			'help' => 'Internationalization',
			'type' => 'flag',
		),
	);
}

?>
