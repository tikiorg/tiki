<?php

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}

$plugins = array();
foreach( $tikilib->plugin_get_list() as $name ) {
	$info = $tikilib->plugin_info( $name );
	if( isset( $info['prefs'] ) && is_array( $info['prefs'] ) && count( $info['prefs'] ) > 0 )
		$plugins[$name] = $info;
}

$smarty->assign( 'plugins', $plugins );

if (isset($_REQUEST["textareasetup"])) {
	ask_ticket('admin-inc-textarea');

	$pref_toggles = array(
		"feature_antibot",
		"feature_hotwords",
		"feature_hotwords_nw",
		"feature_dynamic_content",
		"feature_filegals_manager",
		"feature_use_quoteplugin",
		"feature_comments_post_as_anonymous",
		"feature_smileys",
		"popupLinks",
		"feature_autolinks",
		"quicktags_over_textarea",
		"feature_wiki_protect_email",
		"feature_wiki_ext_icon",
		);

	foreach ($pref_toggles as $toggle) {
		simple_set_toggle ($toggle);
	}

	foreach( $plugins as $key => $info ) {
		$key = 'wikiplugin_' . $key;
		if( in_array( $key, $info['prefs'] ) ) {
			simple_set_toggle( $key );
		}
	}

	$pref_simple_values = array(
		"default_rows_textarea_wiki",
		"default_rows_textarea_comment",
		"default_rows_textarea_forum",
		"default_rows_textarea_forumthread",
	);

	foreach ($pref_simple_values as $svitem) {
		simple_set_value ($svitem);
	}
}

$headerlib->add_cssfile('css/admin.css');

?>
