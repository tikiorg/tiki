<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Search_Elastic_Connection
{
	private $dsn;

	function __construct($dsn)
	{
		$this->dsn = rtrim($dsn, '/');
	}

	function getStatus()
	{
		return $this->get('/');
	}

	function deleteIndex($index)
	{
		return $this->delete("/$index");
	}

	private function get($path)
	{
		try {
			$full = "{$this->dsn}$path";

			$tikilib = TikiLib::lib('tiki');
			$client = $tikilib->get_http_client($full);
			$response = $client->request('GET');

			if ($response->isSuccessful()) {
				return json_decode($response->getBody());
			}
		} catch (Exception $e) {
		}
	}

	private function delete($path)
	{
		try {
			$full = "{$this->dsn}$path";

			$tikilib = TikiLib::lib('tiki');
			$client = $tikilib->get_http_client($full);
			$response = $client->request('DELETE');

			if ($response->isSuccessful()) {
				return json_decode($response->getBody());
			}
		} catch (Exception $e) {
		}
	}
}

