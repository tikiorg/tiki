<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_global_list($partial = false) {
	global $tikilib, $prefs, $url_host;

	$all_styles = array();
	$languages = array();

	if (! $partial) {
		$all_styles = $tikilib->list_styles();
		$languages = $tikilib->list_languages( false, null, true);
	}

	$styles = array();

	foreach ($all_styles as $style) {
		$styles[$style] = substr( $style, 0, strripos($style, '.css'));
	}
	
	$map = array();

	foreach( $languages as $lang ) {
		$map[ $lang['value'] ] = $lang['name'];
	}

	return array(
		'style' => array(
			'name' => tra('Theme'),
			'type' => 'list',
			'help' => 'Themes',
			'description' => tra('Style of the site, sometimes called a skin or CSS. See http://themes.tiki.org for more Tiki themes.'),
			'options' => $styles,
			'default' => 'fivealive.css',
		),
		'browsertitle' => array(
			'name' => tra('Browser title'),
			'description' => tra('Label visible in the browser\'s title bar on all pages. Also appears in search engines.'),
			'type' => 'text',
			'default' => '',
		),
		'validateUsers' => array(
			'name' => tra('Validate new user registrations by email'),
			'description' => tra('Upon registration, the new user will receive an email containing a link to confirm validity.'),
			'type' => 'flag',
			'dependencies' => array(
				'sender_email',
			),
			'default' => 'y',
		),
		'wikiHomePage' => array(
			'name' => tra('Home page'),
			'description' => tra('Landing page used for the wiki when no page is specified. The page will be created if it does not exist.'),
			'type' => 'text',
			'size' => 20,
			'default' => 'HomePage',
		),
		'useGroupHome' => array(
			'name' => tra('Use group homepages'),
			'description' => tra('Users can be sent to different pages upon login, depending on their default group.'),
			'type' => 'flag',
			'help' => 'Groups',
			'keywords' => 'group home page pages',
			'default' => 'n',
		),
		'limitedGoGroupHome' => array(
			'name' => tra('Go to group homepage only if login from default homepage'),
			'type' => 'flag',
			'dependencies' => array(
				'useGroupHome',
			),
			'keywords' => 'group home page pages',
		),
		'language' => array(
			'name' => tra('Default language'),
			'description' => tra('Site language used when no other language is specified by the user.'),
			'filter' => 'lang',
			'help' => 'I18n',
			'type' => 'list',
			'options' => $map,
			'default' => 'en',
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
			'type' => 'text',
			'description' => tra('Tiki requires full read and write access to this directory.'),
			'size' => 30,
			'default' => TikiInit::tempdir(),
			'perspective' => false,
			'default' => 'temp',
		),
		'helpurl' => array(
			'name' => tra('Help URL'),
			'description' => tra('The default help system may not be complete. You can help with the Tiki documentation.'),
			'help' => 'Welcome+Authors',
			'type' => 'text',
			'size' => '50',
			'dependencies' => array(
				'feature_help',
			),
			'default' => "http://doc.tiki.org/",
		),
		'popupLinks' => array(
			'name' => tra('Open external links in new window'),
			'type' => 'flag',
			'default' => 'y',
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
				'180' => tra('[-180 180]'),
				'360' => tra('[0 360]'),
			),
			'default' => '180',
		),
		'modallgroups' => array(
			'name' => tra('Display modules to all groups always'),
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
		),
		'maxArticles' => array(
			'name' => tra('Maximum number of articles on articles home page'),
			'type' => 'text',
			'size' => '5',
			'filter' => 'digits',
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
			'name' => tra('Remind/forgot password'),
			'type' => 'flag',
			'description' => tra('If passwords <em>are not</em> plain text, reset instructions will be emailed to the user.').' '. tra('If passwords <em>are stored</em> as plain text, the password will be emailed to the user'),
			'default' => 'y',
		),
		'useGroupTheme' => array(
			'name' => tra('Each group can have its theme'),
			'type' => 'flag',
			'default' => 'n',
		),
		'sitetitle' => array(
			'name' => tra('Site title'),
			'type' => 'text',
			'size' => '50',
			'default' => '',
		),
		'sitesubtitle' => array(
			'name' => tra('Subtitle'),
			'type' => 'text',
			'size' => '50',
			'default' => '',
		),
		'maxRecords' => array(
			'name' => tra('Maximum number of records in listings'),
			'type' => 'text',
			'size' => '3',
			'default' => 25,
		),
		'maxVersions' => array(
			'name' => tra('Maximum number of versions:'),
			'type' => 'text',
			'size' => '5',
			'hint' => tra('0 for unlimited versions'),
			'default' => 0,
		),
		'allowRegister' => array(
			'name' => tra('Users can register'),
			'type' => 'flag',
			'default' => 'n',
		),
		'validateEmail' => array(
			'name' => tra("Validate user's email server"),
			'type' => 'flag',
			'description' => tra('Tiki will perform a DNS lookup and attempt to open a SMTP session to validate the email server.'),
			'default' => 'n',
		),
		'validateRegistration' => array(
			'name' => tra('Require validation by Admin'),
			'type' => 'flag',
			'description' => tra('The administrator will receive an email for each new user registration, and must validate the user before the user can login.'),
			'dependencies' => array(
				'sender_email',
			),
			'default' => 'n',
		),
		'useRegisterPasscode' => array(
			'name' => tra('Require passcode to register'),
			'type' => 'flag',
			'description' => tra('Users must enter a code to register.  You must inform users of this code. Use to restrict registration to invited users only.'),
			'default' => 'n',
		),
		'registerPasscode' => array(
			'name' => tra('Passcode'),
			'type' => 'text',
			'size' => 15,
			'hint' =>  tra('Alphanumeric code required to complete the registration'),
			'default' => '',
		),
		'userTracker' => array(
			'name' => tra('Use tracker to collect more user information'),
			'type' => 'flag',
			'help' => 'User+Tracker',
			'description' => tra('Display a tracker (form) for the user to complete, as part of the registration process. Use this tracker to store additional information about each user.'),
			'dependencies' => array(
				'feature_trackers',
			),
			'hint' => tra('Use the "Admin Groups" page to select which tracker and fields to display'),
			'default' => 'n',
		),
		'groupTracker' => array(
			'name' => tra('Use tracker to collect more group information'),
			'type' => 'flag',
			'help' => 'Group+Tracker',
			'dependencies' => array(
				'feature_trackers',
			),
			'hint' => tra('Use the "Admin Groups" page to select which tracker and fields to display'),
			'default' => 'n',
		),
		'eponymousGroups' => array(
			'name' => tra('Create a new group for each user'),
			'type' => 'flag',
			'hint' => tra("The group will be named identical to the user's username"),
			'help' => 'Groups',
			'default' => 'n',
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
			'type' => 'list',
			'help' => 'Login+Config#Remember_Me',
			'options' => array(
				'disabled'=> tra('Disabled'),
				'all'			=> tra("User's choice"),
				'always'	=> tra('Always'),
			),
			'default' => 'disabled',
		),
		'remembertime' => array(
			'name' => tra('Duration'),
			'type' => 'list',
			'options' => array(
				'300'				=> '5 ' . tra('minutes'),
				'900'				=> '15 ' . tra('minutes'),
				'1800'			=> '30 ' . tra('minutes'),
				'3600'			=> '1 ' . tra('hour'),
				'7200'			=> '2 ' . tra('hours'),
				'36000'			=> '10 ' . tra('hours'),
				'72000'			=> '20 ' . tra('hours'),
				'86400'			=> '1 ' . tra('day'),
				'604800'		=> '1 ' . tra('week'),
				'2629743'		=> '1 ' . tra('month'),
				'31556926'	=> '1 ' . tra('year'),
			),
			'default' => 7200,
		),
		'urlIndex' => array(
			'name' => tra('Use different URL as homepage'),
			'type' => 'text',
			'size' => 50,
			'default' => '',
		),
		'useUrlIndex' => array(
			'name' => tra('Use URL Index'),
			'description' => tra('Use a Tiki feature homepage or another homepage'),
			'type' => 'flag',
			'default' => 'n',
		),
		'tikiIndex' => array(
			'name' => tra('Use Tiki feature as homepage'),
			'type' => 'list',
			'options' => feature_home_pages($partial),
			'description' => tra('Select the Tiki feature to use as the site homepage. Only enabled features are listed.'),
			'default' => 'tiki-index.php',
		),
		'disableJavascript' => array(
			'name' => tra('Disable JavaScript'),
			'type' => 'flag',
			'description' => tra('Disable JavaScript for testing purpose even if the browser allows it'),
			'default' => 'n',
		),

		// Kaltura
		'partnerId' => array(
			'name' => tra('Partner ID'),
			'description' => tra('Kaltura Partner ID'),
			'type' => 'text',
			'filter' => 'digits',
			'size' => 10,
			'default' => '',
		),
		'secret' => array(
			'name' => tra('User secret'),
			'description' => tra('Kaltura partner setting user secret.'),
			'type' => 'text',
			'size' => 45,
			'filter' => 'alnum',
		),
		'adminSecret' => array(
			'name' => tra('Admin secret'),
			'description' => tra('Kaltura partner setting admin secret.'),
			'type' => 'text',
			'size' => 45,
			'filter' => 'alnum',
			'default' => '',		
		),
		'kdpUIConf' => array(
			'name' => tra('KDP UI Configuration ID'),
			'description' => tra('Kaltura Dynamic Player (KDP) user interface configuration ID'),
			'type' => 'text',
			'size' => 20,
			'default' => '1913592',
		),
		'kdpWidget' => array(
			'name' => tra('KDP Widget ID'),
			'description' => tra('Kaltura Dynamic Player (KDP) Widget ID. This configuration is specific to your account.'),
			'hint' => tra("If you don't know better, use '_yourPartnerID'"),
			'type' => 'text',
			'size' => 20,
			'default' => '',
		),
		'kcwUIConf' => array(
			'name' => tra('KCW UI Configuration ID'),
			'description' => tra('Kaltura Configuration Wizard (KCW) user interface configuration ID'),
			'type' => 'text',
			'size' => 20,
			'default' => '1913682',
		),
		'kseUIConf' => array(
			'name' => tra('Kaltura Simple Editor UI Configuration ID'),
			'type' => 'text',
			'size' => 20,
			'default' => '2434291',
		),
		'kaeUIConf' => array(
			'name' => tra('Kaltura Advanced Editor UI Configuration ID'),
			'type' => 'text',
			'size' => 20,
			'default' => '1000865',
		),
		'kuser' => array(
			'name' => tra('Kaltura "User"'),
			'description' => tra('Owner of content shared by all Tiki users on this site. If empty then each Tiki user can only see their own media entries.'),
			'hint' => tra("You could use your server name for this. e.g. $url_host"),
			'type' => 'text',
			'size' => 20,
			'default' => $url_host,
		),
		'kServiceUrl' => array(
			'name' => tra('Kaltura Service URL'),
			'description' => tra('e.g. http://www.kaltura.com/'),
			'type' => 'text',
			'size' => 40,
			'default' => 'http://www.kaltura.com/',
		),
		// End Kaltura
	);
}

/**
 *  Computes the alternate homes for each feature
 *		(used in admin general template)
 * 
 * @access public
 * @return array of url's and labels of the alternate homepages
 */
function feature_home_pages($partial = false)
{
	global $prefs, $tikilib, $commentslib;
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
			global $bloglib; require_once('lib/blogs/bloglib.php');
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
		require_once ('lib/comments/commentslib.php');
		if (!isset($commentslib)) {
			$commentslib = new Comments;
		}
		if ($prefs['home_forum'] != '0') {
			$hforuminfo = $commentslib->get_forum($prefs['home_forum']);
			$home_forum_name = substr($hforuminfo['name'], 0, 20);
		} else {
			$home_forum_name = tra('Set Forum homepage first');
		}
		$tikiIndex['tiki-view_forum.php?forumId=' . $prefs['home_forum']] = tra('Forum:') . $home_forum_name;
	}
	
	// Custom home
	$tikiIndex['tiki-custom_home.php'] = tra('Custom home');

		return $tikiIndex;
}
