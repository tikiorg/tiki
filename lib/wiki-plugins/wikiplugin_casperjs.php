<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_casperjs_info()
{
	return array(
		'name' => tra('CasperJS'),
		'documentation' => 'CasperJS',
		'description' => tra('Enables to run CasperJS scripts from tiki'),
		'prefs' => array('wikiplugin_casperjs'),
		'packages_required' => array('jerome-breton/casperjs-installer'=>'CasperJsInstaller\Installer'),
		'body' => tra('text'),
		'introduced' => 17,
		'iconname' => 'monitor',
		'tags' => array('basic'),
		'validate' => 'arguments',
		'params' => array(),
	);
}

function wikiplugin_casperjs($data, $params)
{
	$htmlResult = '';

	$info = wikiplugin_casperjs_info();
	foreach ($info['packages_required'] as $class) {
		if (!class_exists($class)){
			return tra('CasperJS not available');
		}
	}

	$actionKey = md5(serialize(array($data, $params)));

	// Generate Link
	$label = tra('Execute CasperJS script');
	$urlParts = parse_url($_SERVER['REQUEST_URI']);
	$path = isset($urlParts['path']) ? $urlParts['path'] : '/';
	if (isset($urlParts['query'])) {
		parse_str($urlParts['query'], $requestParams);
	} else {
		$requestParams = array();
	}
	$requestParams[$actionKey] = $label;
	$requestParamStr = http_build_query($requestParams, null, '&');
	$link = '<a href='.$path.'?'.$requestParamStr.' class="btn btn-default btn-sm">'.$label.'</a>';

	$htmlResult .= $link;

	$executionResult = '';
	// Process request if the action key is set
	if (isset($_REQUEST[$actionKey])) {
		$executionResult .= "<h3>CasperJs Execution Details</h3>";
		$matches = WikiParser_PluginMatcher::match($data);
		foreach ($matches as $match) {
			if ($match->getName() === 'source') {
				$runner = new WikiPlugin_Casperjs_Runner();
				$result = $runner->run($match->getBody());
				$executionResult .= WikiPlugin_Casperjs_Render::resultAsHTML($result);
			}
		}
	}

	$htmlResult .= $executionResult;

	return $htmlResult;
}


