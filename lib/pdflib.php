<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 *
 */
class PdfGenerator
{
	private $mode;
	private $location;

    /**
     *
     */
    function __construct()
	{
		global $prefs;
		$this->mode = 'none';

		if ( $prefs['print_pdf_from_url'] == 'webkit' ) {
			$path = $prefs['print_pdf_webkit_path'];
			if ( ! empty($path) && is_executable($path) ) {
				$this->mode = 'webkit';
				$this->location = $path;
			}
		} elseif ( $prefs['print_pdf_from_url'] == 'webservice' ) {
			if ( ! empty( $prefs['print_pdf_webservice_url'] ) ) {
				$this->mode = 'webservice';
				$this->location = $prefs['print_pdf_webservice_url'];
			}
		}
	}

    /**
     * @param $file
     * @param array $params
     * @return mixed
     */
    function getPdf( $file, array $params )
	{
		global $prefs, $base_url, $tikiroot;

		if ( $prefs['auth_token_access'] == 'y' ) {
			$perms = Perms::get();

			require_once 'lib/auth/tokens.php';
			$tokenlib = AuthTokens::build($prefs);
			$params['TOKEN'] = $tokenlib->createToken(
				$tikiroot . $file, $params, $perms->getGroups(),
				array('timeout' => 60,)
			);
		}

		$url = $base_url . $file . '?' . http_build_query($params, '', '&');

		return $this->{$this->mode}( $url );
	}

    /**
     * @param $url
     * @return null
     */
    private function none( $url )
	{
		return null;
	}

    /**
     * @param $url
     * @return mixed
     */
    private function webkit( $url )
	{
		$arg = escapeshellarg($url);

		// Write a temporary file, instead of using stdout
		// There seemed to be encoding issues when using stdout (on Windows 7 64 bit).

		// Use temp/public. It is cleaned up during a cache clean, in case some files are left
		$filename = 'temp/public/out'.rand().'.pdf';
		
		// Run shell_exec command to generate out file
		// NOTE: this requires write permissions
		`{$this->location} $arg $filename`;
		
		// Read the out file
		$pdf = file_get_contents($filename);
		
		// Delete the outfile
		unlink($filename);
		
		return $pdf;
	}

    /**
     * @param $url
     * @return bool
     */
    private function webservice( $url )
	{
		global $tikilib;

		$target = $this->location . '?' . $url;
		return $tikilib->httprequest($target);
	}
}

