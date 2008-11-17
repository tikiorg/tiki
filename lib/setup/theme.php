<?php

// $Id$
// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for
// details.

//this script may only be included - so its better to die if called directly.
$access->check_script($_SERVER["SCRIPT_NAME"],basename(__FILE__));

if ( isset($_SESSION['try_style']) ) {
	$prefs['style'] = $_SESSION['try_style'];
} elseif ( $prefs['change_theme'] != 'y' ) {
	// Use the site value instead of the user value if the user is not allowed to change the theme
	$prefs['style'] = $prefs['site_style'];
	$prefs['style_option'] = $prefs['site_style_option'];
}

if ( ! is_file('styles/'.$prefs['style']) and ! is_file('styles/'.$tikidomain.'/'.$prefs['style']) ) {
	$prefs['style'] = 'tikineat.css';
}

if ($group_style = $userlib->get_user_group_theme()) {
	$prefs['style'] = $group_style;
	$smarty->assign_by_ref('group_style', $group_style);
}
		
include_once("lib/csslib.php");
if ( $prefs['transition_style_ver'] == 'css_specified_only' ) {
	$transition_style = $csslib->transition_css('styles/'.$prefs['style'], '');
} elseif ( $prefs['transition_style_ver'] != '' && $prefs['transition_style_ver'] != 'none') {
	$transition_style = $csslib->transition_css('styles/'.$prefs['style'], $prefs['transition_style_ver']);
} else {
	$transition_style = '';
}

if ( $transition_style != '' ) $headerlib->add_cssfile('styles/transitions/'.$transition_style,50);

if ( $tikidomain and is_file('styles/'.$tikidomain.'/'.$prefs['style']) ) {
	$headerlib->add_cssfile('styles/'.$tikidomain.'/'.$prefs['style'], 51);
} else {
	$headerlib->add_cssfile('styles/'.$prefs['style'], 51);
}

$stlstl = split("-|\.", $prefs['style']);
$style_base = $stlstl[0];

// Allow to have an ie6.css file for the theme's specific hacks for IE 6
$style_ie6_css = '';
if ( $tikidomain and is_file('styles/'.$tikidomain.'/'.$style_base.'/ie6.css') ) {
	$style_ie6_css = 'styles/'.$tikidomain.'/'.$style_base.'/ie6.css';
} elseif ( is_file('styles/'.$style_base.'/ie6.css') ) {
	$style_ie6_css = 'styles/'.$style_base.'/ie6.css';
}
// include optional "options" cascading stylesheet if set
if ( isset($prefs['style_option']) && $prefs['style_option'] != '' ) {
	if ($tikidomain && is_file('styles/'.$tikidomain.'/'.$style_base.'/options/'.$prefs['style_option']) ) {
		$headerlib->add_cssfile('styles/'.$tikidomain.'/'.$style_base.'/options/'.$prefs['style_option'], 52);
	} else if (is_file('styles/'.$style_base.'/options/'.$prefs['style_option'])) {
		$headerlib->add_cssfile('styles/'.$style_base.'/options/'.$prefs['style_option'], 52);
	}
}

