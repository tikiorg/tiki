<?php
// $Id: /cvsroot/tikiwiki/tiki/lib/wiki-plugins/wikiplugin_articles.php,v 1.27.2.1 2007-12-27 21:46:42 pkdille Exp $

function wikiplugin_googleanalytics_info() {
	return array(
		'name' => tra('Google Analytics'),
		'documentation' => 'PluginGoogleAnalytics',	
		'description' => tra('Add the tracking code for Google Analytics.'),
		'prefs' => array( 'wikiplugin_googleanalytics' ),
		'params' => array(
			'account' => array(
				'required' => true,
				'name' => tra('Account number'),
				'description' => tra('The account number for the site.'),
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
