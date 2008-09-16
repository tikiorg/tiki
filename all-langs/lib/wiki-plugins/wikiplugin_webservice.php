<?php

function wikiplugin_webservice_info() {
	return array(
		'name' => tra('Web Service'),
		'description' => tra('Obtains and display remote information exposed in JSON or YAML. The plugin can be used to display registered or unregistered services. Registered services may use more parameters not defined in this interface.'),
		'prefs' => array(),
		'body' => tra('Template to apply to the data provided. Template format uses smarty templating engine using double brackets as delimiter. Output must provide wiki syntax.'),
		'validate' => 'all',
		'params' => array(
			'url' => array(
				'required' => false,
				'name' => tra('URL'),
				'description' => tra('Complete service URL'),
			),
			'service' => array(
				'required' => false,
				'safe' => true,
				'name' => tra('Service Name'),
				'description' => tra('Registered service name.'),
			),
			'template' => array(
				'required' => false,
				'safe' => true,
				'name' => tra('Template Name'),
				'description' => tra('For use with registered services, name of the template to be used to display the service output. This parameter will be ignored if a body is provided.'),
			),
		),
	);
}

function wikiplugin_webservice( $data, $params ) {
	require_once 'lib/ointegratelib.php';
	require_once( 'Horde/Yaml.php' );
	require_once( 'Horde/Yaml/Loader.php' );
	require_once( 'Horde/Yaml/Node.php' );
	require_once( 'Horde/Yaml/Exception.php' );

	if( isset( $params['url'] ) ) {
		// When URL is specified, always use the body as template
		$request = new OIntegrate;
		$response = $request->performRequest( $params['url'] );

		$templateFile = realpath( './temp/cache/' . md5($data) );

		if( ! file_exists( $templateFile ) )
			file_put_contents( $templateFile, $data );

		return $response->render( 'smarty', 'tikiwiki', 'tikiwiki', $templateFile );
	} elseif( isset($params['service']) && isset($params['template']) ) {
		require_once 'lib/webservicelib.php';

		if( $service = Tiki_Webservice::getService( $params['service'] ) ) {
			if( $template = $service->getTemplate( $params['template'] ) ) {
				$response = $service->performRequest( $params );

				return $template->render( $response, 'tikiwiki' );
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

?>
