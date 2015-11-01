<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_includeurl_info()
{
	return array(
		'name' => tra('IncludeURL'),
		'documentation' => 'PluginIncludeURL',
		'description' => tra('Include the body content from a URL'),
		'prefs' => array('wikiplugin_includeurl'),
		'icon' => 'img/icons/link.png',
		'tags' => array( 'basic' ),
		'format' => 'html',
		'params' => array(
			'url' => array(
				'required' => false,
				'name' => tra('URL'),
				'description' => tra('URL to external file to include.'),
				'default' => '',
			),
		),
	);
}

function wikiplugin_includeurl($dataIn, $params)
{
	// Validate that "url" is set.
	if (empty($params['url'])) {
		return ("<b>{tr}Missing url for plugin includeurl{/tr}</b><br />");
	} else {
		$url = $params['url'];
		$html = file_get_contents($url);

		// Only include the body part of the html file
		$matches = array();
		if (preg_match("/<body.*\/body>/s", $html, $matches)) {
			// Find and strip the body
			$taggedBody = $matches[0];
			$bodyEndIdx = strpos($taggedBody,'>');
			if ($bodyEndIdx > 0) {
				$taggedBody = substr($taggedBody, $bodyEndIdx+1);
			}
			$body = substr($taggedBody, 0, -7);
		} else {
			// No body tag. Return whole html
			$body = $html;
		}
		return $body;
	}
}
