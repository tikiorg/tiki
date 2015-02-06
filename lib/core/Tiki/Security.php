<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Tiki_Security
{
	private $salt;

	public static function get()
	{
		$tikilib = TikiLib::lib('tiki');
		return new self($tikilib->get_site_hash());
	}

	public function __construct($salt)
	{
		$this->salt = $salt;
	}

	/**
	 * Encodes and sign data as a string to be sent to the browser and back to the server,
	 * ensuring the content has not been altered. This allows for the controllers to be stateless.
	 */
	public function encode(array $data)
	{
		$hash = $this->getHash($data);

		return base64_encode(json_encode(array('data' => $data, 'hash' => $hash)));
	}

	public function decode($string)
	{
		if (! $string = base64_decode($string)) {
			return null;
		}

		if (! $decoded = json_decode($string, true)) {
			return null;
		}

		$hash = $this->getHash($decoded['data']);

		if ($hash === $decoded['hash']) {
			return $decoded['data'];
		}
	}

	private function getHash(array $data)
	{
		$string = json_encode($data);
		return sha1($string . $this->salt);
	}
}

