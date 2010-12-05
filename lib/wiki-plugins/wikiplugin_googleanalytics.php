<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_googleanalytics_info() {
	return array(
		'name' => tra('Google Analytics'),
		'documentation' => tra('PluginGoogleAnalytics'),	
		'description' => tra('Add the tracking code for Google Analytics.'),
		'prefs' => array( 'wikiplugin_googleanalytics' ),
		'params' => array(
			'account' => array(
				'required' => true,
				'name' => tra('Account Number'),
				'description' => tra('The account number for the site. Your account number from google looks like UA-XXXXXXX-YY. All you need to enter is XXXXXXX-YY'),
				'default' => ''
			),
		),
	);
}

function wikiplugin_googleanalytics($data, $params) {
	extract($params,EXTR_SKIP);
	if (empty($account)) {
		return tra('Missing parameter');
	}
	$ret = '<script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src=\'" + gaJsHost + "google-analytics.com/ga.js\' type=\'text/javascript\'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
var pageTracker = _gat._getTracker("UA-'.$account.'");
pageTracker._trackPageview();
</script>';
	return '~np~'.$ret.'~/np~';
}
