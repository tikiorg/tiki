<?php

// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class TikiSecure
{
	var $certName = "Tiki Secure Certificate";
	var $bits = 1024;
	
	function __construct($certName = "", $bits = 0)
	{
		if (!empty($certName)) $this->certName = $certName;
		if ($bits > 0) $this->bits = $bits;
	}
	
	function encrypt($data = "")
	{
		$keys = $this->getKeys();
		
		$path = get_include_path();
		set_include_path("lib/phpseclib/");
		require_once('Crypt/RSA.php');
		$rsa = new Crypt_RSA();
		
		$rsa->loadKey($keys->privatekey);
		
		set_include_path($path);
		
		return $rsa->encrypt($data);
	}
	
	function decrypt($cipher)
	{
		if ($this->hasKeys() == false) return "";
		
		$keys = $this->getKeys();

		$rsa = new Crypt_RSA();
		
		$rsa->loadKey($keys->privatekey);
		$rsa->loadKey($keys->publickey);
		
		echo $rsa->decrypt($cipher);
	}
	
	function hasKeys()
	{
		return FileGallery_File::filename($this->certName)->exists();
	}
	
	function getKeys()
	{
		//Get existing certificate if it exists
		$keys = json_decode(FileGallery_File::filename($this->certName)->data());
		
		if (empty($keys)) {
			$keys = $this->newKeys();
		}
		
		return $keys;
	}
	
	private function newKeys()
	{
		$path = get_include_path();
		set_include_path("lib/phpseclib/");
		require_once('Crypt/RSA.php');
		
		$rsa = new Crypt_RSA();
		$keys = $rsa->createKey($this->bits);
		
		set_include_path($path);
		
		FileGallery_File::filename($this->certName)
			->setParam("description", $this->certName)
			->replace(json_encode($keys));
		
		return $keys;
	}
	
	function timestamp($hash, $otherData = "")
	{
		return $this->encrypt(json_encode(array(
			"hash"=>		$hash,
			"otherData"=>	$otherData,
			"date"=>		now(),
			"signer"=>		TikiLib::tikiUrl()
		)));
	}
	
	function verifyTimestamp($cipher)
	{
		return json_decode($this->decrypt($cipher));
	}
}