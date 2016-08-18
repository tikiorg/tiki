<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_twitter_info()
{
	return array(
		'name' => tra('Twitter'),
		'documentation' => 'PluginTwitter',
		'description' => tra('Display the activity for a twitter account'),
		'prefs' => array('wikiplugin_twitter'),
		'body' => '',
		'iconname' => 'twitter',
		'introduced' => 7,
		'params' => array(
			'tweet' => array(
				'required' => true,
				'name' => tra('Twitter Path'),
				'description' => tr('Depends on the type of timeline (Users, Collections, Favorites or Lists). For a
					 User, it is the Account Name (like %0twitterdev%1), for Favorites, something like
					 %0twitterdev/favorites%1. For lists, something like %0twitterdev/lists/listname%1, etc. ',
					'<code>', '</code>'),
				'since' => '7.0',
				'filter' => 'text',
				'default' => ''
			),
			'widgetId' => array(
				'required' => true,
				'name' => tra('Widget Id'),
				'description' => tra('Numeric identifier of the widget'),
				'since' => '13.1',
				'filter' => 'digits',
				'default' => ''
			),
			'theme' => array(
				'required' => false,
				'name' => tra('Theme'),
				'description' => tr('Embedded timelines are available in light and dark themes for customization.
					The light theme is for pages that use a light colored background, while the dark theme is for pages
					that use a dark colored background. Default is %0light%1.', '<code>', '</code>'),
				'since' => '13.1',
				'filter' => 'alpha',
				'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('light'), 'value' => 'light'),
					array('text' => tra('dark'), 'value' => 'dark'),
				),
				'default' => 'light'
			),
			'tweetcolor' => array(
				'required' => false,
				'name' => tra('Link Color'),
				'description' => tra('Text color for individual tweets. Default is theme default.'),
				'since' => '7.0',
				'accepted' => tra('Valid HTML color codes (with beginning #) or names.'),
				'filter' => 'text'
			),
			'tweetbg' => array(
				'required' => false,
				'name' => tra('Border Color'),
				'description' => tra('Change the border color used by the widget. Default is theme default.'),
				'since' => '7.0',
				'accepted' => tra('Valid HTML color codes (with beginning #) or names.'),
				'filter' => 'text'
			),
			'height' => array(
				'required' => false,
				'name' => tra('Height'),
				'description' => tr('Height of widget in pixels. Default is %0.', '<code>300</code>'),
				'since' => '7.0',
				'filter' => 'digits',
				'default' => 300
			),
			'width' => array(
				'required' => false,
				'name' => tra('Width'),
				'description' => tr('Width of widget in pixels or \'auto\' to fit to width of page. Default is %0.',
					'<code>auto</code>'),
				'accepted' => tra('Number of pixels or the word \'auto\'.'),
				'since' => '7.0',
				'filter' => 'text',
				'default' => 'auto'
			),
			'noheader' => array(
				'required' => false,
				'advanced' => true,
				'name' => tra('No Header'),
				'description' => tra('Default is with Header'),
				'since' => '13.1',
				'filter' => 'text',
				'options' => array(
					array('text' => tra('Header'), 'value' => ''),
					array('text' => tra('No Header'), 'value' => 'y'),
				),
			),
			'nofooter' => array(
				'required' => false,
				'advanced' => true,
				'name' => tra('No Footer'),
				'description' => tra('Default is with Footer'),
				'since' => '13.1',
				'filter' => 'text',
				'options' => array(
					array('text' => tra('Footer'), 'value' => ''),
					array('text' => tra('No Footer'), 'value' => 'y'),
				),
			),
			'noborders' => array(
				'required' => false,
				'advanced' => true,
				'name' => tra('Layout Option: No Borders'),
				'description' => tra('Default is with Borders'),
				'since' => '13.1',
				'filter' => 'text',
				'options' => array(
					array('text' => tra('Borders'), 'value' => ''),
					array('text' => tra('No Borders'), 'value' => 'y'),
				),
			),
			'noscrollbar' => array(
				'required' => false,
				'advanced' => true,
				'name' => tra('No Scrollbar'),
				'description' => tra('Default is with Scrollbar'),
				'since' => '13.1',
				'filter' => 'text',
				'options' => array(
					array('text' => tra('Scrollbar'), 'value' => ''),
					array('text' => tra('No Scrollbar'), 'value' => 'y'),
				),
			),
			'shellbg' => array(
				'required' => false,
				'advanced' => true,
				'name' => tra('Transparent Background'),
				'description' => tra('Transparent Shell Background. Default is theme default'),
				'since' => '7.0',
				'filter' => 'text',
				'options' => array(
					array('text' => tra('Theme default'), 'value' => ''),
					array('text' => tra('Transparent'), 'value' => 'transparent'),
				),
			),
		),
	);
}

function wikiplugin_twitter($data, $params)
{
	$default = array('shellbg' => '', 'shellcolor' => '', 'tweetbg' => '', 'tweetcolor' => '', 'width' => 'auto', 'height' => 300);
	$params = array_merge($default, $params);
	extract($params, EXTR_SKIP);

	// Variables sanitizing
	$tweetlimit = (int)$tweetlimit;
	$tweetbg = preg_replace('/[^#0-9a-zA-Z]/','',$tweetbg);
	$tweetcolor = preg_replace('/[^#0-9a-zA-Z]/','',$tweetcolor);
	$tweet = preg_replace('/[^#0-9a-zA-Z%\/=]/','',$tweet);
	$widgetId = preg_replace('/[^0-9]/','',$widgetId);
	if ( $theme != 'dark' ) { $theme = 'light'; }
	$datachrome = array();
	if ( $noheader == 'y' ) { $datachrome[] = 'noheader'; }
	if ( $nofooter == 'y' ) { $datachrome[] = 'nofooter'; }
	if ( $noborders == 'y' ) { $datachrome[] = 'noborders'; }
	if ( $noscrollbar == 'y' ) { $datachrome[] = 'noscrollbar'; }
	if ( $shellbg == 'transparent' ) { $datachrome[] = 'transparent' ; }
	if ( count($datachrome) > 0 ) {
		$datachromehtml = "data-chrome=' " . implode(' ',$datachrome) . "' ";
	}
	if ( $width != 'auto' ) { $width = preg_replace('/[^0-9]/','',$width); }
	$height = (int)$height;

	// Inspiration: http://stackoverflow.com/questions/14303710/how-to-customize-twitter-widget-style
	// and https://dev.twitter.com/web/embedded-timelines
	// Note: the $widgetId is more important than the $tweet in defining what is displayed
	$html = "<a class=\"twitter-timeline\"  href=\"https://twitter.com/$tweet\" data-widget-id=\"$widgetId\"
$datachromehtml 
" . (empty($tweetlimit)?'':" data-tweet-limit='$tweetlimit'\n") . 
(empty($tweetcolor)?"":" data-link-color='$tweetcolor'\n") .
(empty($tweetbg)?"":" data-border-color='$tweetbg'\n") .
"data-theme='$theme' 
height='$height'
width='$width'
" . 
"data-show-replies='false'
data-aria-polite='polite'>Tweets from @$tweet</a>
<script>
!function(d,s,id){
	var js,fjs=d.getElementsByTagName(s)[0];

	if(!d.getElementById(id)){
		js=d.createElement(s);
		js.id=id;
		js.src=\"http://platform.twitter.com/widgets.js\";
		fjs.parentNode.insertBefore(js,fjs);
	}

}(document,'script','twitter-wjs');
</script>";

	//debug return '~np~'.nl2br(htmlspecialchars($html)).'~/np~';
	//debug return '~np~'.$html.nl2br(htmlspecialchars($html)).'~/np~';

	return '~np~'.$html.'~/np~';

}
