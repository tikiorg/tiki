<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: File.php 25720 2010-02-25 19:45:26Z changi67 $

class TikiWebdav_PathFactories_Wiki implements ezcWebdavPathFactory
{
	protected $baseUri = '';
	protected $baseUriLength = 0;
	protected $collectionPathes = array();

	public function __construct()
	{
		global $base_url;
		if ( ( $pos = strpos($base_url, 'tiki-webdav-wiki.php/') ) !== false ) {
			$this->baseUri = substr($base_url, 0, strpos($base_url, 'tiki-webdav-wiki.php') + 20);
		} else {
			$this->baseUri = $base_url . 'tiki-webdav-wiki.php';
		}
		$this->baseUriLength = strlen($this->baseUri);
	}

	public function parseUriToPath( $uri )
	{
		$requestPath = rawurldecode(substr( trim( $uri ), $this->baseUriLength )) ;

		if ( empty($requestPath) ) {
			$requestPath = '/';
		}
		elseif ( substr( $requestPath, -1, 1 ) === '/' ) {
			$this->collectionPathes[substr( $requestPath, 0, -1 )] = true;
		}

		return $requestPath;
	}

	public function generateUriFromPath( $path )
	{
		return $this->baseUri . implode( '/', array_map( 'rawurlencode', explode( '/', $path ) ) );
	}
}
