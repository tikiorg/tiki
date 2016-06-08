<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_quote_info()
{
	return array(
		'name' => tra('Quote'),
		'documentation' => 'PluginQuote',
		'description' => tra('Format text as a quote'),
		'prefs' => array( 'wikiplugin_quote' ),
		'body' => tra('Quoted text'),
		'iconname' => 'quotes',
		'introduced' => 1,
		'filter' => 'text',
		'tags' => array( 'basic' ),
		'params' => array(
			'replyto' => array(
				'required' => false,
				'name' => tra('Reply To'),
				'description' => tra('Name of the quoted person.'),
				'since' => '1',
				'filter' => 'text',
				'default' => '',
			),
		),
	);
}

function wikiplugin_quote($data, $params)
{
	$data = trim($data);
	extract($params, EXTR_SKIP);
	if (!empty($replyto)) {
		$caption = $replyto .' '.tra('wrote:');
	} else {
		$caption = tra('Quote:');
	}
    
	$begin  = "<div class='quote'><div class='quoteheader'>";
	$begin .= "<i class=\"fa fa-quote-left\" aria-hidden=\"true\"></i> ";
    $begin .= "$caption</div><div class='quotebody'>";
	$end = "</div></div>";

	// Prepend any newline char with br
	$data = preg_replace("/\\n/", "<br />", $data);
    // Insert "\n" at data begin if absent (so start-of-line-sensitive syntaxes will be parsed OK)
	return $begin . $data . $end;
}
