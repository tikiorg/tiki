<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_googleanalytics_info()
{
	return array(
		'name' => tra('Google Analytics'),
		'documentation' => 'PluginGoogleAnalytics',
		'description' => tra('Add the tracking code for Google Analytics'),
		'prefs' => array( 'wikiplugin_googleanalytics' ),
		'iconname' => 'chart',
		'format' => 'html',
		'introduced' => 3,
		'params' => array(
			'account' => array(
				'required' => true,
				'name' => tra('Account Number'),
				'description' => tr('The account number for the site. Your account number from google looks like
					%0. All you need to enter is %1', 'UA-XXXXXXX-YY','<code>XXXXXXX-YY</code>'),
				'since' => '3.0',
				'filter' => 'text',
				'default' => ''
			),
		),
	);
}

function wikiplugin_googleanalytics($data, $params)
{
	global $feature_no_cookie;	// set according to cookie_consent_feature pref in tiki-setup.php

	extract($params, EXTR_SKIP);
	if (empty($account)) {
		return tra('Missing parameter');
	}
	if ($feature_no_cookie) {
		return '';
	}
	$ret = <<<JS
<script type="text/javascript">
  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-$account']);
  _gaq.push(['_trackPageview']);
  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();
</script>
JS
;
	return $ret;
}
