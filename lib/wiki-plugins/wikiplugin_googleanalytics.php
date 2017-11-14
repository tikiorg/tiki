<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
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
		'introduced' => 14,
		'params' => array(
			'account' => array(
				'required' => true,
				'name' => tra('Account Number'),
				'description' => tr('The account number for the site. Your account number from Google looks like
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
	global $feature_no_cookie, $prefs;	// set according to cookie_consent_feature pref in tiki-setup.php

	extract($params, EXTR_SKIP);
	if (empty($account)) {
		return tra('Missing parameter');
	}
	if ($feature_no_cookie) {
		return '';
	}
	if ($prefs['site_google_analytics_gtag'] !== 'y') {
		$ret = <<<HTML
<script>
(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
    (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
})(window,document,'script','//www.google-analytics.com/analytics.js','ga');

ga('create', 'UA-$account', 'auto');  // Replace with your property ID.
ga('send', 'pageview');

</script>
HTML;

	} else {
		$ret = <<<HTML
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-$account"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-$account');
</script>
HTML;

	}
	return $ret;
}