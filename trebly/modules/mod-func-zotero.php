<?php

function module_zotero_info()
{
	return array(
		'name' => tra('Bibliography Search'),
		'description' => tra('Search the group\'s Zotero library for entries with the specified tags'),
		'prefs' => array('zotero_enabled'),
		'params' => array(
		),
	);
}

function module_zotero($mod_reference, $module_params)
{
}

