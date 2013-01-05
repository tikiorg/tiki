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
				'name' => tra('Account Name'),
				'description' => tra('Name of the twitter account'),
				'filter' => 'text',
				'default' => ''
			),
			'shellbg' => array(
				'required' => false,
				'name' => tra('Shell Background Color'),
				'description' => tra('Background color for the overall widget, i.e., header, footer and outside border. Default is red (color code #15a2b)'),
				'accepted' => tra('Valid HTML color codes (with beginning #) or names.'),
				'filter' => 'text',
				'default' => '#f15a2b'
			),
			'shellcolor' => array(
				'required' => false,
				'name' => tra('Shell Text Color'),
				'description' => tra('Text color for the widget header and footer. Default is white (color code #ffffff)'),
				'accepted' => tra('Valid HTML color codes (with beginning #) or names.'),
				'filter' => 'text',
				'default' => '#ffffff'
			),
			'tweetbg' => array(
				'required' => false,
				'name' => tra('Tweet Background Color'),
				'description' => tra('Background color for individual tweets. Default is white.'),
				'accepted' => tra('Valid HTML color codes (with beginning #) or names.'),
				'filter' => 'text',
				'default' => 'white'
			),
			'tweetcolor' => array(
				'required' => false,
				'name' => tra('Tweet Text Color'),
				'description' => tra('Text color for individual tweets. Default is black.'),
				'accepted' => tra('Valid HTML color codes (with beginning #) or names.'),
				'filter' => 'text',
				'default' => 'black'
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
	$default = array('shellbg' => '#f15a2b', 'shellcolor' => '#ffffff', 'tweetbg' => 'white', 'tweetcolor' => 'black', 'width' => 'auto', 'height' => 300);
	$params = array_merge($default, $params);
	extract($params, EXTR_SKIP);
	$html = "<script src=\"http://widgets.twimg.com/j/2/widget.js\"></script>
<script>
new TWTR.Widget({
  version: 2,
  type: 'profile',
  rpp: 5,
  interval: 6000,
  width: '$width',
  height: '$height',
  theme: {
    shell: {
      background: '$shellbg',
      color: '$shellcolor'
    },
    tweets: {
      background: '$tweetbg',
      color: '$tweetcolor',
    }
  },
  features: {
    scrollbar: false,
    loop: false,
    live: false,
    hashtags: true,
    timestamp: true,
    avatars: false,
    behavior: 'all'
  }
}).render().setUser('$tweet').start();
</script>";
	return '~np~'.$html.'~/np~';
}
