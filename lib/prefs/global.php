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
			'hint' => tra('Example:') . ' ' . "{banner zone='" . tra('Test') . "'}", 
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
		'useGroupTheme' => array(
			'name' => tra('Each group can have its theme'),
			'type' => 'flag',
		),
	'sitemycode' => array(
		'name' => tra('Content'),
		'hint' => tra ('Example:') . ' ' .  "{if \$user neq ''}<div align=\"right\" style=\"float: right; font-size: 10px\">{tr}logged as{/tr}: {\$user}</div>{/if}",
		'type' => 'textarea',
		'size' => '6',
		),
		'sitetitle' => array(
			'name' => tra('Site title'),
			'type' => 'text',
			'size' => '50',
		),
		'sitesubtitle' => array(
			'name' => tra('Subtitle'),
			'type' => 'text',
			'size' => '50',
		),
		'maxRecords' => array(
			'name' => tra('Maximum number of records in listings'),
			'type' => 'text',
			'size' => '3',
		),
		'maxVersions' => array(
			'name' => tra('Maximum number of versions:'),
			'type' => 'text',
			'size' => '5',
			'hint' => tra('0 for unlimited versions'),
		),
		'allowRegister' => array(
			'name' => tra('Users can register'),
			'type' => 'flag',
		),
		'validateEmail' => array(
			'name' => tra("Validate user's email server"),
			'type' => 'flag',
		),
		'validateRegistration' => array(
			'name' => tra('Require validation by Admin'),
			'type' => 'flag',
			'dependencies' => array(
				'sender_email',
			),
		),
		'useRegisterPasscode' => array(
			'name' => tra('Require passcode to register'),
			'type' => 'flag',
		),
		'registerPasscode' => array(
			'name' => tra('Passcode'),
			'type' => 'password',
			'size' => 15,
			'hint' =>  tra('Users must enter this code to register'),
		),
		'userTracker' => array(
			'name' => tra('Use tracker to collect more user information'),
			'type' => 'flag',
			'help' => 'User+Tracker',
			'dependencies' => array(
				'feature_trackers',
			),
			'hint' => tra('Use the "Admin Groups" page to select which tracker and fields to display'),
		),
		'groupTracker' => array(
			'name' => tra('Use tracker to collect more group information'),
			'type' => 'flag',
			'help' => 'User+Tracker',
			'dependencies' => array(
				'feature_trackers',
			),
			'hint' => tra('Use the "Admin Groups" page to select which tracker and fields to display'),
		),
		'eponymousGroups' => array(
			'name' => tra('Create a new group for each user'),
			'type' => 'flag',
			'hint' => tra("The group will be named identical to the user's username"),
			'help' => 'Groups',
		),
		'remembermethod' => array(
			'name' => tra('Method'),
			'type' => 'list',
			'options' => array(
				'' => tra('Standard'),
				'simple' => tra('Simple'),
			),
			'hint' => tra('"Standard" uses the client\'s IP and is more secure. "Simple" uses a unique ID and is more reliable'),
		),
		'remembertime' => array(
			'name' => tra('Duration'),
			'type' => 'list',
			'options' => array(
				'300'				=> tra('5') . tra('minutes'),
				'900'				=> tra('15') . tra('minutes'),
				'1800'			=> tra('30') . tra('minutes'),
				'3600'			=> tra('1') . tra('hour'),
				'7200'			=> tra('2') . tra('hours'),
				'36000'			=> tra('10') . tra('hours'),
				'72000'			=> tra('20') . tra('hours'),
				'86400'			=> tra('1') .  tra('day'),
				'604800'		=> tra('1') .  tra('week'),
				'2629743'		=> tra('1') .  tra('month'),
				'31556926'	=> tra('1') .  tra('year'),
			),
		),
	);
}
