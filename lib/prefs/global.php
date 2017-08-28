<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_global_list($partial = false)
{
	return array(
		'browsertitle' => array(
			'name' => tra('Browser title'),
			'description' => tra('Visible label in the browser\'s title bar on all pages. Also appears in search engine results.'),
			'type' => 'text',
			'default' => '',
			'tags' => array('basic'),
			'public' => true,
		),
		'validateUsers' => array(
			'name' => tra('Validate new user registrations by email'),
			'description' => tra('Tiki will send an email message to the user. The message contains a link that must be clicked to validate the registration. After clicking the link, the user will be validated. You can use this option to limit false registrations or fake email addresses.'),
			'type' => 'flag',
			'dependencies' => array(
				'sender_email',
			),
			'default' => 'y',
			'tags' => array('basic'),
		),
		'wikiHomePage' => array(
			'name' => tra('Wiki homepage'),
			'description' => tra('The default home page of the wiki when no other page is specified. The page will be created if it does not already exist.'),
			'keywords' => 'homepage',
			'type' => 'text',
			'size' => 20,
			'default' => 'HomePage',
			'tags' => array('basic'),
			'profile_reference' => 'wiki_page',
		),
		'useGroupHome' => array(
			'name' => tra('Use group homepages'),
			'description' => tra('Users can be directed to different pages upon logging in, depending on their default group.'),
			'type' => 'flag',
			'help' => 'Groups',
			'keywords' => 'group home page pages',
			'default' => 'n',
		),
		'limitedGoGroupHome' => array(
			'name' => tra('Go to the group homepage only if logging in from the default homepage'),
			'type' => 'flag',
			'dependencies' => array(
				'useGroupHome',
			),
			'keywords' => 'group home page pages',
			'default' => 'n',
		),
		'cachepages' => array(
			'name' => tra('Cache external pages'),
			'type' => 'flag',
			'default' => 'n',
		),
		'cacheimages' => array(
			'name' => tra('Cache external images'),
			'type' => 'flag',
			'default' => 'n',
		),
		'tmpDir' => array(
			'name' => tra('Temporary directory'),
			'description' => tra('Directory on your server, relative to your Tiki installation, for storing temporary files. Tiki must have full read and write access to this directory.'),
			'type' => 'text',
			'size' => 30,
			'default' => TikiInit::tempdir(),
			'perspective' => false,
		),
		'helpurl' => array(
			'name' => tra('Help URL'),
			'description' => tra('The default help system may not be complete. You can contribute to the Tiki documentation, which is a community-edited wiki.'),
			'help' => 'Welcome+Authors',
			'type' => 'text',
			'size' => '50',
			'dependencies' => array(
				'feature_help',
			),
			'default' => "http://doc.tiki.org/",
			'public' => true,
		),
		'popupLinks' => array(
			'name' => tra('Open external links in new window'),
			'type' => 'flag',
			'default' => 'y',
			'tags' => array('basic'),
		),
		'wikiLicensePage' => array(
			'name' => tra('License page'),
			'type' => 'text',
			'size' => '30',
			'default' => '',
		),
		'wikiSubmitNotice' => array(
			'name' => tra('Submit notice'),
			'type' => 'text',
			'size' => '30',
			'default' => '',
		),
		'gdaltindex' => array(
			'name' => tra('Full path to gdaltindex'),
			'type' => 'text',
			'size' => '50',
			'help' => 'Maps',
			'perspective' => false,
			'default' => '',
		),
		'ogr2ogr' => array(
			'name' => tra('Full path to ogr2ogr'),
			'type' => 'text',
			'size' => '50',
			'help' => 'Maps',
			'perspective' => false,
			'default' => '',
		),
		'mapzone' => array(
			'name' => tra('Map Zone'),
			'type' => 'list',
			'help' => 'Maps',
			'options' => array(
				'180' => '[-180 180]',
				'360' => '[0 360]',
			),
			'default' => '180',
		),
		'modallgroups' => array(
			'name' => tra('Always display modules to all groups'),
			'type' => 'flag',
			'default' => 'n',
		),
		'modseparateanon' => array(
			'name' => tra('Hide anonymous-only modules from registered users'),
			'type' => 'flag',
			'default' => 'n',
		),
		'modhideanonadmin' => array(
			'name' => tra('Hide anonymous-only modules from Admins'),
			'type' => 'flag',
			'default' => 'n',
		),
		'maxArticles' => array(
			'name' => tra('Maximum number of articles on the articles homepage'),
			'type' => 'text',
			'size' => '5',
			'filter' => 'digits',
			'units' => tra('articles'),
			'default' => 10,
		),
		'sitead' => array(
			'name' => tra('Site Ads and Banners Content'),
			'hint' => tra('Example:') . ' ' . "{banner zone='" . tra('Test') . "'}",
			'type' => 'textarea',
			'size' => '5',
			'default' => '',
		),
		'urlOnUsername' => array(
			'name' => tra('URL to go to when clicking on a username'),
			'type' => 'text',
			'description' => tra('URL to go to when clicking on a username.').' '.tra('Default').': tiki-user_information.php?userId=%userId% <em>('.tra('Use %user% for login name and %userId% for userId)').')</em>',
			'default' => '',
		),
		'forgotPass' => array(
			'name' => tra('Forgot password'),
			'description' => tra('Users can request a password reset. They will receive a link by email.'),
			'type' => 'flag',
			'detail' => tra("Since passwords are encrypted, it's not possible to tell the user what the password is. It's only possible to change it."),
			'default' => 'y',
			'tags' => array('basic'),
		),
		'useGroupTheme' => array(
			'name' => tra('Group theme'),
			'description' => tra('Each group can have its own theme'),
			'type' => 'flag',
			'default' => 'n',
		),
		'sitetitle' => array(
			'name' => tra('Site title'),
			'type' => 'text',
			'description' => tr('The displayed title of the website.'),
			'size' => '50',
			'default' => '',
			'tags' => array('basic'),
			'public' => true,
		),
		'sitesubtitle' => array(
			'name' => tra('Subtitle'),
			'type' => 'text',
			'description' => tr('A short descriptive phrase.'),
			'size' => '50',
			'default' => '',
			'tags' => array('basic'),
			'public' => true,
		),
		'maxRecords' => array(
			'name' => tra('Maximum number of records in listings'),
			'type' => 'text',
			'size' => '3',
			'units' => tra('records'),
			'default' => 25,
			'tags' => array('basic'),
			'public' => true,
		),
		'maxVersions' => array(
			'name' => tra('Maximum number of versions:'),
			'type' => 'text',
			'units' => tra('versions'),
			'size' => '5',
			'hint' => tra('0 for unlimited versions'),
			'default' => 0,
		),
		'allowRegister' => array(
			'name' => tra('Users can register'),
			'description' => tra('This will allow users to register, using the webform. The Login module will include a Register link. If disabled, the admin will have to create new users manually on the Admin Users page. '),
			'type' => 'flag',
			'default' => 'n',
			'tags' => array('basic'),
		),
		'validateEmail' => array(
			'name' => tra("Validate user's email server"),
			'description' => tra('Tiki will attempt to validate the user’s email address by examining the syntax of the email address. It must be a string of letters, or digits or _ or . or - follows by a @ follows by a string of letters, or digits or _ or . or -. Tiki will perform a DNS lookup and attempt to open a SMTP session to validate the email server.'),
			'type' => 'list',
			'tip' => tra('Some web servers may disable this functionality, thereby disabling this feature. If you are not in in a high security site or if you are on an open users site, do not use this option.'),
			'options' => array(
				'n'=> tra('No'),
				'y'			=> tra('Yes'),
				'd'	=> tra('Yes, with "deep MX" search'),	// filters out reserved IP addresses and uses checkdnsrr to check for a valid "A" record
			),
			'default' => 'n',
		),
		'validateRegistration' => array(
			'name' => tra('Require validation by Admin'),
			'description' => tra('The administrator will receive an email for each new user registration, and must validate the user before the user can log in.'),
			'type' => 'flag',
			'dependencies' => array(
				'sender_email',
			),
			'default' => 'n',
		),
		'useRegisterPasscode' => array(
			'name' => tra('Require passcode to register'),
			'description' => tra('Users must enter an alphanumeric code to register.  The site administrator must inform users of this code. This is to restrict registration to invited users.'),
			'type' => 'flag',
			'default' => 'n',
			'tags' => array('basic'),
		),
		'registerPasscode' => array(
			'name' => tra('Passcode'),
			'type' => 'text',
			'size' => 15,
			'hint' => tra('Alphanumeric code required to complete the registration'),
			'default' => '',
			'tags' => array('basic'),
		),
		'showRegisterPasscode' => array(
			'name' => tra('Show passcode on registration form'),
			'description' => tra("Displays the required passcode on the registration form. This is helpful for legitimate users who want to register while making it difficult for automated robots because the passcode is unique for each site and because it is displayed in JavaScript."),
			'type' => 'flag',
			'default' => 'n',
			'tags' => array('basic'),
		),
		'registerKey' => array(
			'name' => tra('Registration page key'),
			'hint' => tra('Key required to be on included the URL to access the registration page (if not empty).'),
			'description' => tra('for example, to register, users need to go to: tiki-register.php?key=yourregistrationkeyvalue'),
			'type' => 'text',
			'size' => 15,
			'default' => '',
			'tags' => array('basic'),
		),
		'userTracker' => array(
			'name' => tra('Use a tracker to collect more user information'),
			'description' => tra('Display a tracker (form) for the user to complete as part of the registration process. This tracker will be used to store additional information about each user.'),
			'type' => 'flag',
			'help' => 'User+Tracker',
			'dependencies' => array(
				'feature_trackers',
			),
			'hint' => tra('Go to the "Admin Groups" page to select which tracker and fields to display'),
			'default' => 'n',
		),
		'groupTracker' => array(
			'name' => tra('Use tracker to collect more group information'),
			'type' => 'flag',
			'help' => 'Group+Tracker',
			'dependencies' => array(
				'feature_trackers',
			),
			'hint' => tra('Go to the "Admin Groups" page to select which tracker and fields to display'),
			'default' => 'n',
		),
		'eponymousGroups' => array(
			'name' => tra('Create a new group for each user'),
			'description' => tra('Tiki will automatically create a group for the user.'),
			'type' => 'flag',
			'hint' => tra("The group name will be the same as the user's username"),
			'help' => 'Groups',
			'default' => 'n',
			'keywords' => 'eponymous groups',
		),
		'syncGroupsWithDirectory' => array(
			'name' => tra('Synchronize Tiki groups with a directory'),
			'type' => 'flag',
			'hint' => tra('Define the directory within the "LDAP" tab'),
			'default' => 'n',
		),
		'syncUsersWithDirectory' => array(
			'name' => tra('Synchronize Tiki users with a directory'),
			'type' => 'flag',
			'hint' => tra('Define the directory within the "LDAP" tab'),
			'default' => 'n',
		),
		'rememberme' => array(
			'name' => tra('Remember me'),
			'description' => tra("Use this option to have Tiki remember users. They will automatically be logged in if they leave, then return to the site."),
			'type' => 'list',
			'help' => 'Login-General-Preferences#Remember_Me',
			'options' => array(
				'disabled'=> tra('Disabled'),
				'all'			=> tra("User's choice"),
				'always'	=> tra('Always'),
			),
			'default' => 'disabled',
			'tags' => array('basic'),
		),
		'remembertime' => array(
			'name' => tra('Duration'),
			'description' => tra('You can define the length of time that Tiki will “remember” the user.'),
			'type' => 'list',
			'options' => array(
				'300'		=> '5 ' . tra('minutes'),
				'900'		=> '15 ' . tra('minutes'),
				'1800'		=> '30 ' . tra('minutes'),
				'3600'		=> '1 ' . tra('hour'),
				'7200'		=> '2 ' . tra('hours'),
				'36000'		=> '10 ' . tra('hours'),
				'72000'		=> '20 ' . tra('hours'),
				'86400'		=> '1 ' . tra('day'),
				'604800'	=> '1 ' . tra('week'),
				'2629743'	=> '1 ' . tra('month'),
				'31556926'	=> '1 ' . tra('year'),
			),
			'default' => 7200,
			'tags' => array('basic'),
		),
		'urlIndexBrowserTitle' => array(
			'name' => tra('Homepage Browser title'),
			'description' => tra('Customize Browser title for the custom homepage'),
			'type' => 'text',
			'size' => 50,
			'default' => 'Homepage',
			'tags' => array('basic'),
			'dependencies' => array(
				'useUrlIndex',
			),
		),
		'urlIndex' => array(
			'name' => tra('Homepage URL'),
			'type' => 'text',
			'size' => 50,
			'default' => '',
			'tags' => array('basic'),
			'dependencies' => array(
				'useUrlIndex',
			),
		),
		'useUrlIndex' => array(
			'name' => tra('Use custom homepage'),
			'description' => tra('Use the top page of a Tiki feature or another homepage'),
			'warning' => tra('This option will override the Use Tiki feature as homepage setting.'),
			'type' => 'flag',
			'default' => 'n',
			'tags' => array('basic'),
		),
		'tikiIndex' => array(
			'name' => tra('Use the top page of a Tiki feature as the homepage'),
			'description' => tra('Select the Tiki feature to provide the site homepage. Only enabled features are listed.'),
			'type' => 'list',
			'options' => feature_home_pages($partial),
			'default' => 'tiki-index.php',
			'tags' => array('basic'),
		),
		'disableJavascript' => array(
			'name' => tra('Disable JavaScript'),
			'description' => tra('Disable JavaScript for the purpose of testing even if enabled in the browser.'),
			'warning' => tra('Use only for testing or troubleshooting. When enabled, many Tiki features will not operate correctly.'),
			'type' => 'flag',
			'default' => 'n',
		),
		'maxRowsGalleries' => [
			'name' => tra('Maximum rows per page'),
			'type' => 'text',
			'units' => tra('rows'),
			'default' => '10',
		],
		'rowImagesGalleries' => [
			'name' => tra('Images per row'),
			'type' => 'text',
			'units' => tra('images'),
			'default' => '6',
		],
		'thumbSizeXGalleries' => [
			'name' => tra('Thumbnail width in pixels'),
			'type' => 'text',
			'units' => tra('pixels'),
			'default' => '80',
		],
		'thumbSizeYGalleries' => [
			'name' => tra('Thumbnail height in pixels'),
			'type' => 'text',
			'units' => tra('pixels'),
			'default' => '80',
		],
		'scaleSizeGalleries' => [
			'name' => tra('Default scale size in pixels'),
			'type' => 'text',
			'units' => tra('pixels'),
			'default' => '',
		],
	);
}

/**
 *  Computes the alternate homes for each feature
 *   (used in admin general template)
 *
 * @param $partial bool
 * 
 * @access public
 * @return array of url's and labels of the alternate homepages
 */
function feature_home_pages($partial = false)
{
	global $prefs;
	$tikilib = TikiLib::lib('tiki');
	$tikiIndex = array();

	//wiki
	$tikiIndex['tiki-index.php'] = tra('Wiki');
	
	// Articles
	if (! $partial && $prefs['feature_articles'] == 'y') {
		$tikiIndex['tiki-view_articles.php'] = tra('Articles');
	}
	// Blog
	if (! $partial && $prefs['feature_blogs'] == 'y') {
		if ( $prefs['home_blog'] != '0' ) {
			$bloglib = TikiLib::lib('blog');
			$hbloginfo = $bloglib->get_blog($prefs['home_blog']);
			$home_blog_name = substr($hbloginfo['title'], 0, 20);
		} else {
			$home_blog_name = tra('Set blogs homepage first');
		}
		$tikiIndex['tiki-view_blog.php?blogId=' . $prefs['home_blog']] = tra('Blog:') . $home_blog_name;
	}
	
	// Image gallery
	if ( ! $partial && $prefs['feature_galleries'] == 'y' ) {
		if ($prefs['home_gallery'] != '0') {
			$hgalinfo = $tikilib->get_gallery($prefs['home_gallery']);
			$home_gal_name = substr($hgalinfo["name"], 0, 20);
		} else {
			$home_gal_name = tra('Set Image gal homepage first');
		}
		$tikiIndex['tiki-browse_gallery.php?galleryId=' . $prefs['home_gallery']] = tra('Image Gallery:') . $home_gal_name;
	}

	// File gallery
	if ( ! $partial && $prefs['feature_file_galleries'] == 'y' ) {
			$filegallib = TikiLib::lib('filegal');
			$hgalinfo = $filegallib->get_file_gallery($prefs['home_file_gallery']);
			$home_gal_name = substr($hgalinfo["name"], 0, 20);
			$tikiIndex['tiki-list_file_gallery.php?galleryId=' . $prefs['home_file_gallery']] = tra('File Gallery:') . $home_gal_name;
	}
	
	// Forum
	if ( ! $partial && $prefs['feature_forums'] == 'y' ) {

		if ($prefs['home_forum'] != '0') {
			$hforuminfo = TikiLib::lib('comments')->get_forum($prefs['home_forum']);
			$home_forum_name = substr($hforuminfo['name'], 0, 20);
		} else {
			$home_forum_name = tra('Set Forum homepage first');
		}
		$tikiIndex['tiki-view_forum.php?forumId=' . $prefs['home_forum']] = tra('Forum:') . $home_forum_name;
	}
	
	return $tikiIndex;
}
