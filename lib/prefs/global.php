<?php

function prefs_global_list() {
	global $tikilib;
	$languages = $tikilib->list_languages( false, null, true);
	$map = array();
	
	foreach( $languages as $lang ) {
		$map[ $lang['value'] ] = $lang['name'];
	}

	return array(
		'browsertitle' => array(
			'name' => tra('Browser title'),
			'description' => tra('Label visible in the browser\'s title bar on all pages. Also appears in search engines.'),
			'type' => 'text',
		),
		'validateUsers' => array(
			'name' => tra('Validate new user registrations by email'),
			'description' => tra('Upon registration, the new user will receive an email containing a link to confirm validity.'),
			'type' => 'flag',
			'dependencies' => array(
				'sender_email',
			),
		),
		'wikiHomePage' => array(
			'name' => tra('Home page'),
			'description' => tra('Landing page used for the wiki when no page is specified. The page will be created if it does not exist.'),
			'type' => 'text',
			'size' => 20,
		),
		'useGroupHome' => array(
			'name' => tra('Use group homepages'),
			'description' => tra('Use group homepages'),
			'type' => 'flag',
			'help' => 'Group',
		),
		'limitedGoGroupHome' => array(
			'name' => tra('Go to group homepage only if login from default homepage'),
			'description' => tra('Go to group homepage only if login from default homepage'),
			'type' => 'flag',
			'dependencies' => array(
				'useGroupHome',
			),
		),
		'language' => array(
			'name' => tra('Default language'),
			'description' => tra('Site language used when no other language is specified by the user.'),
			'filter' => 'lang',
			'help' => 'Internationalization',
			'type' => 'list',
			'options' => $map,
		),
		'cachepages' => array(
			'name' => tra('Cache external pages'),
			'description' => tra('Cache external pages'),
			'type' => 'flag',
		),
		'cacheimages' => array(
			'name' => tra('Cache external images'),
			'description' => tra('Cache external images'),
			'type' => 'flag',
		),
		'tmpDir' => array(
			'name' => tra('Temporary directory'),
			'description' => tra('Temporary directory'),
			'type' => 'text',
			'size' => 30,
			'default' => TikiSetup::tempdir(),
		),
		'helpurl' => array(
			'name' => tra('Help URL'),
			'description' => tra('The default help system may not be complete. You can help with the TikiWiki documentation.'),
			'help' => 'Welcome+Authors',
			'type' => 'text',
			'size' => '50',
			'dependencies' => array(
				'feature_help',
			),
		),
		'popupLinks' => array(
			'name' => tra('Open external links in new window'),
			'type' => 'flag',
		),
		'wikiLicensePage' => array(
			'name' => tra('License page'),
			'type' => 'text',
			'size' => '30',
		),
		'wikiSubmitNotice' => array(
			'name' => tra('Submit notice'),
			'type' => 'text',
			'size' => '30',
		),
		'gdaltindex' => array(
			'name' => tra('Full path to gdaltindex'),
			'type' => 'text',
			'size' => '50',
		),
		'ogr2ogr' => array(
			'name' => tra('Full path to ogr2ogr'),
			'type' => 'text',
			'size' => '50',
		),
		'mapzone' => array(
			'name' => tra('Map Zone'),
			'type' => 'list',
			'options' => array(
				'180' => tra('[-180 180]'),
				'360' => tra('[0 360]'),
			),
		),
		'modallgroups' => array(
			'name' => tra('Display modules to all groups always'),
			'type' => 'flag',
		),
		'modseparateanon' => array(
			'name' => tra('Hide anonymous-only modules from registered users'),
			'type' => 'flag',
		),
		'maxArticles' => array(
			'name' => tra('Maximum number of articles on articles home page'),
			'type' => 'text',
			'size' => '5',
			'filter' => 'digits',
		),
		'sitead' => array(
			'name' => tra('Content'),
			'type' => 'textarea',
			'size' => '5',
		),
		'urlOnUsername' => array(
			'name' => tra('Url to go when clicking on a username.'),
			'type' => 'text',
			'description' => tra('Url to go when clicking on a username.').' '.tra('Default: ').'tiki-user_information.php?userId=',
		),
		'forgotPass' => array(
			'name' => tra('Remind/forgot password'),
			'type' => 'flag',
			'description' => tra('If passwords <em>are not</em> plain text, reset instructions will be emailed to the user.').' '. tra('If passwords <em>are stored</em> as plain text, the password will be emailed to the user'),
		),
	);
}
