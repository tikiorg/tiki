<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class TikiWebdav_PathFactories_File implements ezcWebdavPathFactory
{
	protected $baseUri = '';
	protected $baseUriLength = 0;
	protected $collectionPathes = array();

	public function parseUriToPath($uri)
	{
		global $base_url;
		$filegallib = TikiLib::lib('filegal');

		$requestPath = preg_replace('#.*tiki-webdav\.php#', '', rawurldecode(trim($uri)));

		if (empty($requestPath)) {
			$requestPath = '/';
		} elseif (substr($requestPath, -1, 1) === '/') {
			$this->collectionPathes[substr($requestPath, 0, -1)] = true;
		} else {
			// MSIE sends requests for collections without the '/' at the end
			$objectId = $filegallib->get_objectid_from_virtual_path($requestPath);
			if ($objectId && $objectId['type'] == 'filegal') {
				$requestPath .= '/';
			}

			// @todo Some clients first send with / and then discover it is not a resource
			// therefore the upper todo might be refined.
			if (isset($this->collectionPathes[$requestPath])) {
				unset($this->collectionPathes[$requestPath]);
			}
		}

		print_debug("parseUriToPath($uri): $requestPath\n");
		return $requestPath;
	}

	public function generateUriFromPath($path)
	{
		global $base_url;
		$filegallib = TikiLib::lib('filegal');

		$result = $base_url . 'tiki-webdav.php' . implode('/', array_map('rawurlencode', explode('/', $path)));

		print_debug("generateUriFromPath($path): $result\n");
		return $result;
	}
}
