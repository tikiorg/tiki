<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class TikiWebdav_PathFactories_Wiki implements ezcWebdavPathFactory
{
	protected $baseUri = '';
	protected $baseUriLength = 0;
	protected $collectionPathes = array();

	public function parseUriToPath($uri)
	{
		$requestPath = rawurldecode(trim($uri));

		if (empty($requestPath)) {
			$requestPath = '/';
		} elseif (substr($requestPath, -1, 1) === '/') {
			$this->collectionPathes[substr($requestPath, 0, -1)] = true;
		}

		return $requestPath;
	}

	public function generateUriFromPath($path)
	{
		global $base_url;

		$result = $base_url . 'tiki-webdav.php/Wiki%20Pages' . implode('/', array_map('rawurlencode', explode('/', $path)));

		print_debug("generateUriFromPath($path): $result\n");
		return $result;
	}
}
