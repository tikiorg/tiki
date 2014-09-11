<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
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
		'icon' => 'img/icons/twitter.png',
		'params' => array(
			'tweet' => array(
				'required' => true,
				'name' => tra('Twitter Path'),
				'description' => tra('Depends on the type of timeline (Users, Collections, Favorites or Lists). For an User, it is the Account Name (like twitterdev), for Favorites it is like (twitterdev/favorites), for lists it is like twitterdev/lists/listname, etc. '),
				'filter' => 'text',
				'default' => ''
			),
			'widgetId' => array(
				'required' => true,
				'name' => tra('Widget Id'),
				'description' => tra('Numeric identifier of the widget'),
				'filter' => 'digits',
				'default' => ''
			),
			'shellbg' => array(
				'required' => false,
				'name' => tra('Transparent Shell Background'),
				'description' => tra('Background color for the overall widget, i.e., header, footer and outside border. Default is theme default'),
				'filter' => 'text',
				'options' => array(
					array('text' => tra('theme default'), 'value' => ''),
					array('text' => tra('transparent'), 'value' => 'transparent'),
				),
			),
			'theme' => array(
				'required' => false,
				'name' => tra(''),
				'description' => tra('Embedded timelines are available in light and dark themes for customization. The light theme is for pages that use a light colored background, while the dark theme is for pages that use a dark colored background. Default is light'),
				'filter' => 'alpha',
				'options' => array(
					array('text' => tra('light'), 'value' => 'light'),
					array('text' => tra('dark'), 'value' => 'dark'),
				),
				'default' => 'light'
			),
			'tweetbg' => array(
				'required' => false,
				'name' => tra('Border color'),
				'description' => tra('Change the border color used by the widget. Default is theme default.'),
				'accepted' => tra('Valid HTML color codes (with beginning #) or names.'),
				'filter' => 'text'
			),
			'tweetcolor' => array(
				'required' => false,
				'name' => tra('Link color'),
				'description' => tra('Text color for individual tweets. Default is theme default.'),
				'accepted' => tra('Valid HTML color codes (with beginning #) or names.'),
				'filter' => 'text'
			),
			'height' => array(
				'required' => false,
				'name' => tra('Height'),
				'description' => tra('Height of widget in pixels. Default is 300.'),
				'filter' => 'digits',
				'default' => 300
			),
			'width' => array(
				'required' => false,
				'name' => tra('Width'),
				'description' => tra('Width of widget in pixels or \'auto\' to fit to width of page. Default is auto.'),
				'accepted' => tra('Number of pixels or the word \'auto\'.'),
				'filter' => 'text',
				'default' => 'auto'
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
	if ( $shellbg != 'transparent' ) { $shellbg = ''; }
	if ( $width != 'auto' ) { $width = preg_replace('/[^0-9]/','',$width); }
	$height = (int)$height;

	// Inspiration: http://stackoverflow.com/questions/14303710/how-to-customize-twitter-widget-style
	// and https://dev.twitter.com/web/embedded-timelines
	// Note: the $widgetId is more important than the $tweet in defining what is displayed
	$html = "<a class=\"twitter-timeline\"  href=\"https://twitter.com/$tweet\" data-widget-id=\"$widgetId\"
data-chrome=' " . (empty($shellbg)?"":"transparent") . "' 
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
