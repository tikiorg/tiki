<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_lang_list()
{
	return [
		'lang_use_db' => [
			'name' => tra('Use database for translation'),
			'description' => tra('Use the database to store the translated strings and allow using interactive translation'),
			'type' => 'flag',
			'help' => 'Translating+Tiki+interface',
			'default' => 'n',
			'hint' => tr('Edit, export and import languages'),
		],
		'lang_control_contribution' => [
			'name' => tra('Manage contribution of translated strings'),
			'description' => tra('Offers to set whether each translated string should be contributed to the Tiki community or kept locally'),
			'type' => 'flag',
			'default' => 'n',
			'tags' => ['advanced'],
		],
		'lang_machine_translate_implementation' => [
			'name' => tr('Machine translation implementation'),
			'description' => tr('Select between alternate impementations for machine translation. Depending on the implementation, different API keys may be required.'),
			'type' => 'list',
			'options' => [
				'google' => tr('Google Translate'),
				'bing' => tr('Bing Translate'),
			],
			'default' => '',
		],
		'lang_machine_translate_wiki' => [
			'name' => tr('Enable machine translation of wiki pages'),
			'description' => tr('Makes additional languages available to the list of languages on the page.'),
			'type' => 'flag',
			'default' => 'n',
		],
		'lang_google_api_key' => [
			'name' => tr('Google Translate API Key'),
			'description' => tr('The key must be generated from the Google console. Choose to create a server key.'),
			'type' => 'text',
			'default' => '',
		],
		'lang_bing_api_client_id' => [
			'name' => tr('Bing Translate Client ID'),
			'description' => tr('The application must be registered.'),
			'type' => 'text',
			'default' => '',
		],
		'lang_bing_api_client_secret' => [
			'name' => tr('Bing Translate Client Secret'),
			'description' => tr('The application must be registered.'),
			'type' => 'text',
			'default' => '',
		],
		'lang_available_translations_dropdown' => [
			'name' => tr('Display available translations as dropdown'),
			'description' => tr('Rather than show a simple icon for listing available languages or translation options, show a more visible drop-down.'),
			'type' => 'flag',
			'default' => 'n',
		],
	];
}
