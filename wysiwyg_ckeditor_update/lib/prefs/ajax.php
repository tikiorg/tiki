<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: feature.php 29132 2010-09-11 19:56:47Z jonnybradley $

function prefs_ajax_list() {

	return array(

		'ajax_xajax' => array(
			'name' => tra('Ajax Xajax'),
			'description' => tra('Use "xajax" library to "ajaxify" most Tiki pages.'),
			'help' => 'Ajax',
			'type' => 'flag',
			'dependencies' => array(
				'feature_ajax',
			),
		),
		'ajax_autosave' => array(
			'name' => tra('Ajax auto-save'),
			'description' => tra('Saves your edits as you go along enabling you to recover your work after an "interruption". Also enables "Live" preview and is required for wysiwyg plugin processing.'),
			'help' => 'Lost+Edit+Protection',
			'type' => 'flag',
			'dependencies' => array(
				'feature_ajax',
			),
		),
		
	);
}
