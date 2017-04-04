<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_xmpp_info()
{
	return array(
		'name' => tra('XMPP'),
		'documentation' => 'PluginXMPP',
		'description' => tra('Hold a chat session using XMPP'),
		'format' => 'html',
		'prefs' => array( 'wikiplugin_xmpp', 'xmpp_feature' ),
		'iconname' => 'webchat',
		'introduced' => 5,
		'tags' => array( 'basic' ),
		'params' => array(
		),
	);
}

function wikiplugin_xmpp( $data, $params )
{
	global $prefs, $user;
	$headerlib = TikiLib::lib('header');
	$xmpplib = TikiLib::lib('xmpp');

	// Converse.js overwrite Tiki jQuery breaks other libraries, so we have to
	// save tikiJQuery before loading converse.js. After load converse.js, we
	// can restore tikiJQuery to $.
	// TODO: talk to JC and ask to release a version without jQuery bundled
	
	$headerlib->add_jq_onready(
		 'var tikiJQuery = $;'
		.'var xmpp_service_url = $.service("xmpp", "prebind");'

		.'jQuery("<link>")'
		.    '.attr("rel", "stylesheet")'
		.    '.attr("href", "vendor_bundled/vendor/jcbrand/converse.js/css/converse.css")'
		.    '.appendTo("head");'

		.'function tiki_restore_jquery() {'
		.    'window.$ = tikiJQuery;'
		.'}'

		.'function tiki_initialiaze_conversejs() {'
		.    'converse.initialize({'
		.        'bosh_service_url: "' . $xmpplib->server_http_bind . '",'
		.        'jid: "' . $xmpplib->get_user_jid($user) . '",'
		.        'authentication: "prebind",'
		.        'prebind_url: xmpp_service_url,'
		.    '});'
		.'}'

		.'$.getScript("vendor_bundled/vendor/jcbrand/converse.js/dist/converse.js")'
		.    '.done(tiki_initialiaze_conversejs)'
		.    '.always(tiki_restore_jquery);'
	);

	return '';
}
