<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class PdfGenerator
{
	private $mode;
	private $location;

	function __construct() {
		global $prefs;
		$this->mode = 'none';

		if( $prefs['print_pdf_from_url'] == 'webkit' ) {
			$path = $prefs['print_pdf_webkit_path'];
			if( ! empty( $path ) && is_executable( $path ) ) {
				$this->mode = 'webkit';
				$this->location = $path;
			}
		} elseif( $prefs['print_pdf_from_url'] == 'webservice' ) {
			if( ! empty( $prefs['print_pdf_webservice_url'] ) ) {
				$this->mode = 'webservice';
				$this->location = $prefs['print_pdf_webservice_url'];
			}
		}
	}

	function getPdf( $file, array $params ) {
		global $prefs, $base_url, $tikiroot;

		if( $prefs['auth_token_access'] == 'y' ) {
			$perms = Perms::get();

			require_once 'lib/auth/tokens.php';
			$tokenlib = AuthTokens::build( $prefs );
			$params['TOKEN'] = $tokenlib->createToken( $tikiroot . $file, $params, $perms->getGroups(), array(
				'timeout' => 60,
			) );
		}

		$url = $base_url . $file . '?' . http_build_query( $params, '', '&' );

		return $this->{$this->mode}( $url );
	}

	private function none( $url ) {
		return null;
	}

	private function webkit( $url ) {
		$arg = escapeshellarg( $url );

		return `{$this->location} $arg -`;
	}

	private function webservice( $url ) {
		global $tikilib;

		$target = $this->location . '?' . $url;
		return $tikilib->httprequest( $target );
	}
}

