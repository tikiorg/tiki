<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
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

		$multilinguallib = TikiLib::lib('multilingual');
		if ( $multilinguallib->useBestLanguage()) {

			if (empty($_REQUEST['page_id'])) {
				if (!empty($_REQUEST['page'])) {
					$info = $tikilib->get_page_info($_REQUEST['page']);
					$_REQUEST['page_id'] = $info['page_id'];
				} elseif (!empty($_REQUEST['page_ref_id'])) {
					$structlib = TikiLib::lib('struct');
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
		$profilesLink = 'tiki-admin.php?profile=&categories%5B%5D=15.x&categories%5B%5D=Featured+profiles' .
										'&repository=http%3a%2f%2fprofiles.tiki.org%2fprofiles&page=profiles&preloadlist=y&list=List#step2';

		// Default HomePage content
		$homePageContent = '';
		$is_html = false;
		if (($prefs['feature_wysiwyg'] === 'y') && $prefs['wysiwyg_htmltowiki'] !== 'y') {

			$is_html = true;

			$homePageContent .= '<h1>' . tr('Congratulations') . "</h1>\n";
			$homePageContent .= tr('This is the default homepage for your Tiki. If you are seeing this page, your installation was successful.') . "\n\n<br>";
			$homePageContent .= tr('You can change this page after logging in. Please review the [http://doc.tiki.org/wiki+syntax|wiki syntax] for editing details.') . "\n\n\n<br>";
			$homePageContent .= '<h2>'. tr('Get started.') . "</h2>\n";
			$homePageContent .= tr('To begin configuring your site:') . "\n";
			$homePageContent .= "<ul>\n";
			$homePageContent .= "<li>".tr('1) Log in with your newly created password.') . "</li>\n";
			$homePageContent .= "<li>".tr('2) Manually [tiki-admin.php?page=features|Enable specific Tiki features] that you didn\'t enable with the Admin wizard.') . "</li>\n";
			$homePageContent .= "<li>".tr('3) Run [tiki-admin.php?page=profiles|Tiki Profiles] to quickly get up and running.') . "</li>\n";
			$homePageContent .= "</ul>\n\n<br>";
			$homePageContent .= '<h2>' . tr('Need help?') . "</h2>\n";
			$homePageContent .= tr('For more information:') . "\n<br>";
			$homePageContent .= '*' . tr('[https://tiki.org/Introduction|Learn more about Tiki].') . "\n<br>";
			$homePageContent .= '*' . tr('[https://tiki.org/|Get help], including the [http://doc.tiki.org|official documentation] and [http://tiki.org/forums|support forums].') . "\n<br>";
			$homePageContent .= '*' . tr('[https://tiki.org/Join|Join the Tiki community].') . "\n<br>";
		} else {
			$homePageContent .= '!' . tr('Congratulations') . "\n";
			$homePageContent .= tr('This is the default homepage for your Tiki. If you are seeing this page, your installation was successful.') . "\n\n";
			$homePageContent .= tr('You can change this page after logging in. Please review the [http://doc.tiki.org/wiki+syntax|wiki syntax] for editing details.') . "\n\n\n";
			$homePageContent .= '!!'. tr('Get started.') . "\n";
			$homePageContent .= tr('To begin configuring your site:') . "\n";
			$homePageContent .= "{FANCYLIST()}\n";
			$homePageContent .= tr('1) Log in with your newly created password.') . "\n";
			$homePageContent .= tr('2) Manually [tiki-admin.php?page=features|Enable specific Tiki features] that you didn\'t enable with the Admin wizard.') . "\n";
			$homePageContent .= tr('3) Run [tiki-admin.php?page=profiles|Tiki Profiles] to quickly get up and running.') . "\n";
			$homePageContent .= "{FANCYLIST}\n\n";
			$homePageContent .= '!!' . tr('Need help?') . "\n";
			$homePageContent .= tr('For more information:') . "\n";
			$homePageContent .= '*' . tr('[https://tiki.org/Introduction|Learn more about Tiki].') . "\n";
			$homePageContent .= '*' . tr('[https://tiki.org/|Get help], including the [http://doc.tiki.org|official documentation] and [http://tiki.org/forums|support forums].') . "\n";
			$homePageContent .= '*' . tr('[https://tiki.org/Join|Join the Tiki community].') . "\n";
		}
			
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
			$is_html,	// is_html
			null,
			$is_html ? 'y' : 'n',	// wysiwyg,
			''
		);

		unset($homePageContent, $homePageLang);
	}
}
