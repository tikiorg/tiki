<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_together_info()
{
	return array(
		'name' => tra('Together'),
		'documentation' => 'PluginTogether',
		'description' => tra('Collaborate in real time'),
		'iconname' => 'group',
		'introduced' => 12,
		'prefs' => array( 'wikiplugin_together' ),
		'additional' => tra('A service for collaborating on your website in real-time. TogetherJS lets users communicate,
			co-author, co-browse and guide each other. TogetherJS is implemented in Javascript; no software or plugins
			to install, and it is friendly with existing web pages, while still letting developers customize the
			experience.')." ".tra("Note: TogetherJS is alpha-quality software. We do not recommend using it in
			production at this time."),
		'params' => array(
			'buttonname' => array(
				'required' => false,
				'name' => tra('Button Name'),
				'description' => tra('Set the button name. Default is CoWrite with TogetherJS'),
				'since' => '12.0',
				'filter' => 'text',
				'default' => tra('CoWrite with TogetherJS')
			),
		)
	);
}

function wikiplugin_together($data, $params)
{

	if (!isset($params['buttonname'])) {
		$params['buttonname'] = tra('CoWrite with TogetherJS');
	}
	TikiLib::lib('header')->add_jsfile('https://togetherjs.com/togetherjs-min.js', true)
		->add_jq_onready('
TogetherJS.on("ready", function () {
	$(".page_actions a[href^=\'tiki-editpage.php?page=\'], #page-bar a[href^=\'tiki-editpage.php?page=\']").each(function () {
		var href = $(this).attr("href");
		$(this).attr("href", href + "&conflictoverride=y");	// add the conflictoverride param so the second user doesnt get the usual warning
	});
});

TogetherJS.config("getUserName", function () {
	return jqueryTiki.userRealName || jqueryTiki.username;
});

TogetherJS.config("getUserAvatar", function () {
	return jqueryTiki.userAvatar;
});
		');

	return '<button onclick="TogetherJS(this); return false;" class="btn btn-default">' . $params['buttonname'] . '</button>';
}
