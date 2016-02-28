<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_webservice_info()
{
	return array(
		'name' => tra('Web Service'),
		'documentation' => 'PluginWebservice',
		'description' => tra('Display remote information exposed in JSON or YAML or SOAP XML'),
		'prefs' => array( 'wikiplugin_webservice' ),
		'body' => tr('Template to apply to the data provided. Template format uses smarty templating engine using
			double brackets as delimiter. Output must provide wiki syntax. Body can be sent to a parameter instead by
			using the %0 parameter.', '<code>bodyname</code>'),
		'validate' => 'all',
		'iconname' => 'move',
		'introduced' => 3,
		'params' => array(
			'url' => array(
				'required' => false,
				'name' => tra('URL'),
				'description' => tra('Complete service URL'),
				'since' => '3.0',
				'default' => '',
			),
			'service' => array(
				'required' => false,
				'safe' => true,
				'name' => tra('Service Name'),
				'description' => tra('Registered service name.'),
				'since' => '3.0',
				'default' => '',
			),
			'template' => array(
				'required' => false,
				'safe' => true,
				'name' => tra('Template Name'),
				'description' => tra('For use with registered services, name of the template to be used to display the
					service output. This parameter will be ignored if a body is provided.'),
				'since' => '3.0',
				'default' => '',
			),
			'bodyname' => array(
				'required' => false,
				'filter' => 'word',
				'safe' => true,
				'name' => tra('Body as Parameter'),
				'description' => tra('Name of the argument to send the body as for services with complex input.
					Named service required for this to be useful.'),
				'since' => '3.0',
				'default' => '',
			),
			'params' => array(
				'required' => false,
				'safe' => true,
				'name' => tra('Parameters'),
				'description' => tra('Parameters formatted like a query')
					. ': <code>param1=value1&amp;param2=value2</code>',
				'since' => '7.0',
				'default' => '',
			),
		),
	);
}

function wikiplugin_webservice( $data, $params )
{
	require_once 'lib/ointegratelib.php';

	if ( isset( $params['bodyname'] ) && ! empty($params['bodyname']) ) {
		$params[ $params['bodyname'] ] = $data;
		unset($params['bodyname']);
		$data = '';
	}

	if ( isset( $params['params'] )) {
		parse_str($params['params'], $request_params);
		$params = array_merge($params, $request_params);
	}

	if ( ! empty( $data ) ) {
		$templateFile = $GLOBALS['tikipath'] . 'temp/cache/' . md5($data); 

		if ( ! file_exists($templateFile) )
			file_put_contents($templateFile, $data);
	} else {
		$templateFile = '';
	}

	if ( isset( $params['url'] ) ) {
		// When URL is specified, always use the body as template
		$request = new OIntegrate;
		$response = $request->performRequest($params['url']);

		if ( ! empty( $templateFile ) )
			return $response->render('smarty', 'tikiwiki', 'tikiwiki', $templateFile);
	} elseif ( isset($params['service']) && (isset($params['template']) || !empty( $templateFile ) )) {
		require_once 'lib/webservicelib.php';

		if ( $service = Tiki_Webservice::getService($params['service']) ) {
			if ( ! empty( $templateFile ) ) {
				// Render using function body
				$response = $service->performRequest($params);

				return $response->render('smarty', 'tikiwiki', 'tikiwiki', $templateFile);
			} elseif ( $template = $service->getTemplate($params['template']) ) {
				$response = $service->performRequest($params);

				return $template->render($response, 'tikiwiki');
			} else {
				return '^' . tra('Unknown Template') . '^';
			}
		} else {
			return '^' . tra('Unknown Service') . '^';
		}
	} else {
		return '^' . tra('Missing parameters') . '^';
	}
}
