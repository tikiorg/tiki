<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
$access->check_script($_SERVER['SCRIPT_NAME'], basename(__FILE__));

// Wiki pagename regexp

if ( $prefs['wiki_page_regex'] == 'strict' )
	$page_regex = '([A-Za-z0-9_])([\.: A-Za-z0-9_\-])*([A-Za-z0-9_])';
elseif ( $prefs['wiki_page_regex'] == 'full' )
	$page_regex = '([A-Za-z0-9_]|[\x80-\xFF])([\.: A-Za-z0-9_\-]|[\x80-\xFF])*([A-Za-z0-9_]|[\x80-\xFF])';
else
	$page_regex = '([^\n|\(\)])((?!(\)\)|\||\n)).)*?';

// Wiki dump

$wiki_dump_exists = 'n';
$dump_path = 'dump';

if ( $tikidomain ) {
	$dump_path .= "/$tikidomain";
}
if ( file_exists($dump_path.'/new.tar') ) {
	$wiki_dump_exists = 'y';
}
$smarty->assign('wiki_dump_exists', $wiki_dump_exists);

// find out the page name if url=tiki-index_x.php (can be needed in module)
if (strstr($_SERVER['SCRIPT_NAME'], 'tiki-index.php')
		|| strstr($_SERVER['SCRIPT_NAME'], 'tiki-index_p.php')
		|| strstr($_SERVER['SCRIPT_NAME'], 'tiki-index_raw.php')
) {
	$check = false;
	if (!isset($_REQUEST['page']) && !isset($_REQUEST['page_ref_id']) && !isset($_REQUEST['page_id'])) {
		$_REQUEST['page'] = $userlib->get_user_default_homepage2($user);
		$check = true;
	}

	if ( $prefs['feature_multilingual'] == 'y'
			&& (isset($_REQUEST['page']) || isset($_REQUEST['page_ref_id']) || isset($_REQUEST['page_id']))
	) { // perhaps we have to go to an another page

		global $multilinguallib; include_once('lib/multilingual/multilinguallib.php');
		if ( $multilinguallib->useBestLanguage()) {

			if (empty($_REQUEST['page_id'])) {
				if (!empty($_REQUEST['page'])) {
					$info = $tikilib->get_page_info($_REQUEST['page']);
					$_REQUEST['page_id'] = $info['page_id'];
				} elseif (!empty($_REQUEST['page_ref_id'])) {
					global $structlib; include_once('lib/structures/structlib.php');
					$info = $structlib->s_get_page_info($_REQUEST['page_ref_id']);
					$_REQUEST['page_id'] = $info['page_id'];
				}
			}
			if (!empty($_REQUEST['page_id'])) {
				if ( $multilinguallib->useBestLanguage() ) {
					$_REQUEST['page_id'] = $multilinguallib->selectLangObj('wiki page', $_REQUEST['page_id']);
				}
				if (!empty($_REQUEST['page_id'])) {
					$check = false;
				}
			}

		}

	}

	// If the HomePage does not exist, create it
	if ($check && !$tikilib->page_exists($_REQUEST['page'])) {

		$homePageLang = $prefs['language'];
		$profilesLink = 'tiki-admin.php?profile=&categories%5B%5D=10.x&categories%5B%5D=Featured+profiles' .
										'&repository=http%3a%2f%2fprofiles.tiki.org%2fprofiles&page=profiles&preloadlist=y&list=List#step2';

		// Default HomePage content
		$homePageContent = "{GROUP(groups=Admins)}\n";
		$homePageContent .= '!' . tr('Thank you for installing Tiki.') . "\n\n";
		$homePageContent .= tr('The entire Tiki Community would like to thank you and help you get introduced to Tiki.') . "\n\n";
		$homePageContent .= '!' . tr('How To Get Started') . "\n";
		$homePageContent .= tr('Tiki has more than 1000 features and settings.') . "\n\n";
		$homePageContent .= tr('This allows you to create both very simple and complex websites.') . "\n\n";
		$homePageContent .= tr('We understand that so many features might seem overwhelming at first. This is why we offer you two different ways to __Get Started__ with Tiki.') . "\n\n";
		$homePageContent .= "{DIV(width=\"48%\",float=\"right\")}\n";
		$homePageContent .= '-=' . tr('Manual Setup using Admin Panel') . "=-\n";
		$homePageContent .= '!![tiki-admin.php|' . tr('Get Started using Admin Panel') . "]\n";
		$homePageContent .= '__' . tr('Who Should Use This') . "__\n";
		$homePageContent .= '*' . tr('You are familiar with software Admin Panels') . "\n";
		$homePageContent .= '*' . tr('You enjoy exploring and playing with many options') . "\n";
		$homePageContent .= '*' . tr('You already know Tiki') . "\n\n";
		$homePageContent .= "{DIV}{DIV(width=\"48%\",float=\"left\")}\n";
		$homePageContent .= '-=' . tr('Easy Setup using Profiles') . "=-\n";
		$homePageContent .= '!![' . $profilesLink . '|' . tr('Get Started using Profiles') . "]\n";
		$homePageContent .= '__' . tr('Who Should Use This') . "__\n";
		$homePageContent .= '*' . tr('You want to get started quickly') . "\n";
		$homePageContent .= '*' . tr("You don't feel like learning the Admin Panel right away") . "\n";
		$homePageContent .= '*' . tr("You want to quickly test out some of Tiki's Features") . "\n\n";
		$homePageContent .= '!!' . tr('Featured Profiles') . "\n\n";
		$homePageContent .= tr('__Collaborative Community__ ([%0|apply profile now])', $profilesLink) . "\n";
		$homePageContent .= tr('Setup to help subject experts and enthusiasts work together to build a Knowledge Base') . "\n";
		$homePageContent .= '*' . tr('Wiki Editing') . "\n";
		$homePageContent .= '*' . tr('Personal Member Spaces'). "\n";
		$homePageContent .= '*' . tr('Forums') . "\n";
		$homePageContent .= '*'. tr('Blogs') . "\n\n";
		$homePageContent .= tr('__Personal Blog and Profile__ ([%0|apply profile now])', $profilesLink) . "\n";
		$homePageContent .= tr('Setup with many cool features to help you integrate the Social Web and establish a strong presence in the Blogosphere') . "\n";
		$homePageContent .= '*' . tr('Blog (Full set of blog related features)') . "\n";
		$homePageContent .= '*' . tr('Image Gallery') . "\n";
		$homePageContent .= '*' . tr('RSS Integration') . "\n";
		$homePageContent .= '*' . tr('Video Log') . "\n\n";
		$homePageContent .= tr('__Company Intranet__ ([%0|apply profile now])', $profilesLink) . "\n";
		$homePageContent .= tr('Setup for a Corporate Intranet of a typical medium-sized business.') . "\n";
		$homePageContent .= '*' . tr('Company News Articles') . "\n";
		$homePageContent .= '*' . tr('Executive Blog') . "\n";
		$homePageContent .= '*' . tr('File Repository & Management') . "\n";
		$homePageContent .= '*' . tr('Collaborative Wiki') . "\n\n";
		$homePageContent .= tr('__Small Organization Web Presence__ ([%0|apply profile now])', $profilesLink) . "\n";
		$homePageContent .= tr('Setup for a Web Presence of a typical small business or non-profit.') . "\n";
		$homePageContent .= '*' . tr('Company News & Updates') . "\n";
		$homePageContent .= '*' . tr("Highlight Company's Products and Services") . "\n";
		$homePageContent .= '*' . tr('File Gallery (great for Media Kit)'). "\n";
		$homePageContent .= '*' . tr('Contact Form') . "\n\n";
		$homePageContent .= "{DIV}{ELSE}\n\n";
		$homePageContent .= '!' . tr('Congratulations') . "\n";
		$homePageContent .= tr('This is the default homepage for your Tiki. If you are seeing this page, your installation was successful.') . "\n\n";
		$homePageContent .= tr('You can change this page after logging in. Please review the [http://doc.tiki.org/wiki+syntax|wiki syntax] for editing details.') . "\n\n\n";
		$homePageContent .= '!!'. tr('{img src=img/icons/star.png alt=\"Star\"} Get started.') . "\n";
		$homePageContent .= tr('To begin configuring your site:') . "\n";
		$homePageContent .= "{FANCYLIST()}\n";
		$homePageContent .= tr('1) Log in with your newly created password.') . "\n";
		$homePageContent .= tr('2) Manually Enable specific Tiki features.') . "\n";
		$homePageContent .= tr('3) Run Tiki Profiles to quickly get up and running.') . "\n";
		$homePageContent .= "{FANCYLIST}\n\n";
		$homePageContent .= '!!' . tr('{img src=img/icons/help.png alt=\"Help\"} Need help?') . "\n";
		$homePageContent .= tr('For more information:') . "\n";
		$homePageContent .= '*' . tr('[http://info.tiki.org/Learn+More|Learn more about Tiki].') . "\n";
		$homePageContent .= '*' . tr('[http://info.tiki.org/Help+Others|Get help], including the [http://doc.tiki.org|official documentation] and [http://tiki.org/forums|support forums].') . "\n";
		$homePageContent .= '*' . tr('[http://info.tiki.org/Join+the+community|Join the Tiki community].') . "\n";
		$homePageContent .= '{GROUP}';

		$tikilib->create_page(
			$_REQUEST['page'],
			0,
			$homePageContent,
			$tikilib->now,
			'Tiki initialization',
			'admin',
			'0.0.0.0',
			'',
			$homePageLang,
			false,
			null,
			'n',
			''
		);

		unset($homePageContent, $homePageLang);
	}
}
