<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function prefs_categorypath_list()
{
	return array(
		'categorypath_excluded' => array(
			'name' => tra('Exclude these category IDs'),
			'description' => tra('Category path won\'t appear for these category IDs.'),
			'separator' => ',',
			'type' => 'text',
			'size' => '15',
			'profile_reference' => 'category',
			'default' => array(''), //empty string needed to keep preference from setting unexpectedly
		),
		'categorypath_format' => array(
			'name' => tr('Category path format'),
			'description' => tr('Alter how the category path will be rendered.'),
			'type' => 'list',
			'default' => 'link_when_visible',
			'options' => array(
				'link_when_visible' => tr('Links to the category when the category is visible'),
				'text_when_visible' => tr('Text label when the category is visible'),
				'link_or_text' => tr('Link when visible, text label otherwise'),
				'always_text' => tr('Always display the category path labels'),
			),
		),
	);
}
