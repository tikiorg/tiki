<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_twitter_info() {
	return array(
		'name' => tra('Twitter'),
		'documentation' => 'PluginTwitter',
		'description' => tra('Include a tweet'),
		'prefs' => array('wikiplugin_twitter'),
		'body' => '',
		'icon' => 'pics/icons/twitter.png',
		'params' => array(
			'tweet' => array(
				'required' => true,
				'name' => tra('Tweet name'),
				'filter' => 'text',
				'default' => ''
			),
			'shellbg' => array(
				'required' => false,
				'name' => tra('Shell background color'),
				'filter' => 'text',
				'default' => '#f15a2b'
			),
			'shellcolor' => array(
				'required' => false,
				'name' => tra('Shell text color'),
				'filter' => 'text',
				'default' => '#ffffff'
			),
			'tweetbg' => array(
				'required' => false,
				'name' => tra('Tweet background color'),
				'filter' => 'text',
				'default' => 'white'
			),
			'tweetcolor' => array(
				'required' => false,
				'name' => tra('Tweet text color'),
				'filter' => 'text',
				'default' => 'black'
			),
			'height' => array(
				'required' => false,
				'name' => tra('Height'),
				'filter' => 'text',
				'default' => 300
			),
			'width' => array(
				'required' => false,
				'name' => tra('Width'),
				'filter' => 'text',
				'default' => 'auto'
			),
		),
	);
}

function wikiplugin_twitter($data, $params) {
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
