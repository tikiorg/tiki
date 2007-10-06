<?php

// $Header: /cvsroot/tikiwiki/tiki/lib/setup/theme.php,v 1.1 2007-10-06 15:18:45 nyloth Exp $
// Copyright (c) 2002-2005, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for
// details.

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'],'tiki-setup.php')!=FALSE) {
  header('location: index.php');
  exit;
}

$user_style = $site_style = $style;

if ( isset($_SESSION['style']) ) {
	$user_style = $_SESSION['style'];
}

if ($feature_userPreferences == 'y') {
	if ($user) {
		if (isset($_REQUEST['style'])) {
			$site_style = $_REQUEST['style'];
		}
		if ($change_theme == 'y') {
			$user_style = $tikilib->get_user_preference($user, 'theme', $style);
			if ($user_style and (is_file("styles/$user_style") or is_file("styles/$tikidomain/$user_style"))) {
				$site_style = $user_style;
			}
		}
	} else {
		$site_style = $user_style;
	}
} else {
	$site_style = $user_style;
}

if (!is_file("styles/$site_style") and !is_file("styles/$tikidomain/$site_style")) {
	$site_style = 'tikineat.css';
}
if ($tikidomain and is_file("styles/$tikidomain/$site_style")) {
	$site_style = "$tikidomain/$site_style";
}

# style
$smarty->assign('style', $style);           // that is the pref
$smarty->assign('site_style', $site_style); // that is the effective site style
$smarty->assign('user_style', $user_style); // that is the user-chosen style
include_once("csslib.php");
$transition_style = $csslib->transition_css('styles/'.$site_style);
if ( $transition_style != '' ) $headerlib->add_cssfile('styles/transitions/'.$transition_style,50);
$headerlib->add_cssfile('styles/'.$site_style,51);
$stlstl = split("-|\.", $site_style);
$style_base = $stlstl[0];
