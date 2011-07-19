<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_quote_info() {
	return array(
		'name' => tra('Quote'),
		'documentation' => 'PluginQuote',
		'description' => tra('Format text as a quote'),
		'prefs' => array( 'wikiplugin_quote' ),
		'body' => tra('Quoted text'),
		'icon' => 'pics/icons/quotes.png',
		'filter' => 'text',
		'params' => array(
			'replyto' => array(
				'required' => false,
				'name' => tra('Reply To'),
				'description' => tra('Name of the quoted person.'),
				'filter' => 'text',
				'default' => '',
			),
		),
	);
}

function wikiplugin_quote($data, $params) {
	/* set default values for some args */
	
	// Remove first <ENTER> if exists...
//	if (substr($data, 0, 2) == "\r\n") $data = substr($data, 2);
	// trim space/returns from beginning and end
	$data = trim($data);
    
	extract ($params, EXTR_SKIP);
	if (!empty($replyto)) {
		$caption = $replyto .' '.tra('wrote:');
	} else {
		$caption = tra('Quote:');
	}
    
	$begin  = "<div class='quoteheader'>";
    $begin .= "$caption</div><div class='quotebody'>";
	$end = "</div>";
		// Prepend any newline char with br
		$data = preg_replace("/\\n/", "<br />", $data);
    // Insert "\n" at data begin if absent (so start-of-line-sensitive syntaxes will be parsed OK)
//    if (substr($data, 0, 1) != "\n") $data = "\n".$data;
	return $begin . $data . $end;
}
