<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_transclude_info() {
	return array(
		'name' => tra('Transclusion'),
		'documentation' => tra('PluginTransclusion'),
		'description' => tra('Includes the content of a wiki page and replaces values in the body of the included page. All additional parameters will be replaced. For example %%%title%%% will be replaced with the parameter title in the plugin.'),
		'prefs' => array('wikiplugin_transclude', 'feature_wiki'),
		'extraparams' => true,
		'defaultfilter' => 'text',
		'params' => array(
			'page' => array(
				'required' => true,
				'name' => tra('Page Name'),
				'description' => tra('Name of the wiki page to use as a template for the values.'),
				'default' => '',
			),
		),
	);
}

class WikiPlugin_Transclude_Replacer
{
	private $params;

	function __construct( array $params ) {
		$this->params = $params;
	}

	function callback( $matches ) {
		if( isset( $this->params[$matches[1]] ) ) {
			return $this->params[$matches[1]];
		}
	}
}

function wikiplugin_transclude( $data, $params ) {
	if( ! isset( $params['page'] ) ) {
		return WikiParser_PluginOutput::argumentError( array( 'page' ) );
	}
	
	$page = $params['page'];
	unset( $params['page'] );

	global $tikilib;

	if( ! Perms::get( 'wiki page', $page )->view ) {
		return WikiParser_PluginOutput::error( tra('Permission Denied'), tra('Attempt to include a page that cannot be viewed.') );
	}

	if( $info = $tikilib->get_page_info( $page ) ) {
		$parts = preg_split('/%%%text%%%/', $info['data']);
		$data = $tikilib->parse_data($data);
                $pass = $parts[0] . $data . $parts[1];
		return preg_replace_callback(
			'/%%%([A-z0-9]+)%%%/',
			array( new WikiPlugin_Transclude_Replacer( $params ), 'callback' ),
			$pass
		);
	} else {
		return WikiParser_PluginOutput::error( tr('Page not found'), tr('Page named "%0" does not exist at this time.', $page ) );
	}
}

