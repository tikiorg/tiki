<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class TikiDb_Initializer
{
	private $connectors = array(
		'pdo' => 'TikiDb_Initializer_Pdo',
		'adodb' => 'TikiDb_Initializer_Adodb',
	);
	private $preferred;
	private $initializeCallback;

	function setPreferredConnector($connector)
	{
		if (isset($this->connectors[$connector])) {
			$this->preferred = $connector;
		}
	}

	function setInitializeCallback($callback)
	{
		$this->initializeCallback = $callback;
	}

	function getConnection(array $credentials)
	{
		if ( $connector = $this->getInitializer($this->preferred)) {
			return $this->initialize($connector, $credentials);
		}

		foreach (array_keys($this->connectors) as $name) {
			if ($connector = $this->getInitializer($name)) {
				return $this->initialize($connector, $credentials);
			}
		}
	}

	private function initialize($connector, $credentials)
	{
		if ($db = $connector->getConnection($credentials)) {
			if ($callback = $this->initializeCallback) {
				$callback($db);
			}

			return $db;
		}
	}

	private function getInitializer($name)
	{
		if (! isset($this->connectors[$name])) {
			return false;
		}

		$connector = new $this->connectors[$name];
		if ($connector->isSupported()) {
			return $connector;
		}
	}
}

