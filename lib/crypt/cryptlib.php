<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/*
 * CryptLib (aims to) safely store encrypted data, e.g. passwords for external systems, in Tiki.
 * The encrypted data can only be decrypted by the owner/user.
 *
 * CryptLib will use mcrypt if the PHP extension is available.
 * Otherwise it reverts to (near-plaintext) Base64 encoding.
 *
 * In order to use mcrypt
 * 1. The mcrypt PHP extension must be available
 * 2. Call the init method before using cryptlib
 *
 * The method setUserPassword encrypts the value and stores a user preference
 * getUserPassword reads it back into cleartext
 *
 * The secret key phrase is the MD5 sum of the user's Tiki password.
 * The secret key is thus 1) personal 2) not stored anywhere in Tiki.
 *
 * Each encryption uses its own initialization vector (seed).
 * Rehashing the same value should thus yield a different result every time.
 *
 * When a user logs in, Tiki calls onUserLogin, which registers the current secret key in a session variable.
 * This session variable is used to decrypt the stored user passwords when needed.
 *
 * When a user changes the password, Tiki will call onChangeUserPassword. There the value must be rehashed.
 * Changing a user's Tiki password directly in the database will not fire onChangeUserPassword,
 * making the stored passwords unreadable.
 *
 */
class CryptLib extends TikiLib
{
	private $key;			// mcrypt key
	private $mcrypt;		// mcrypt object
	private $iv;			// mcrypt initialization vector

	//
	// Init and release
	////////////////////////////////

	function __construct()
	{
		parent::__construct();
	}

	function __destruct()
	{
		$this->release();
	}

	function init()
	{
		$phraseMD5 = $_SESSION['cryptphrase'];
		$this->initSeed($phraseMD5);
	}

	function initSeed($phraseMD5)
	{
		if (extension_loaded('mcrypt') && $this->mcrypt == null) {
			$this->key = $phraseMD5;

			// Using Rijndael 256 in CBC mode.
			$this->mcrypt = mcrypt_module_open(MCRYPT_RIJNDAEL_256, '', 'cbc', '');

			if (TikiInit::isWindows()) {
				$this->iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($this->mcrypt), MCRYPT_RAND);
			} else {
				$this->iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($this->mcrypt), MCRYPT_DEV_RANDOM);
			}
		}
	}
	function release()
	{
		if ($this->mcrypt != null) {
			mcrypt_module_close($this->mcrypt);
			$this->key = null;
			$this->mcrypt = null;
			$this->iv = null;
		}
	}

	//
	// User password utilities
	////////////////////////////////


	// Encrypt and save the password in the user preferences
	// Return false on failure otherwise the generated crypt text
	function setUserPassword($user, $userprefKey, $cleartext)
	{
		if (empty($cleartext)) {
			return false;
		}
		$crypttext = $this->encrypt($cleartext, $this->iv);

		// Save iv in the stored password
		$storedPwd64 = base64_encode($this->iv.$crypttext);

		$this->set_user_preference($user, $userprefKey, $storedPwd64);
		return $storedPwd64;
	}

	// Get the password from the user preferences.
	// Decrypt and return cleartext
	// Return false, if no stored password is found
	function getUserPassword($user, $userprefKey)
	{
		$storedPwd64 = $this->get_user_preference($user, $userprefKey);
		if (empty($storedPwd64)) {
			return false;
		}

		// Extract the iv and crypttext
		$storedPwd = base64_decode($storedPwd64);
		$ivSize = mcrypt_enc_get_iv_size($this->mcrypt);
		$iv = substr($storedPwd, 0, $ivSize);
		$crypttext = substr($storedPwd, $ivSize);

		// Decrypt
		$cleartext = $this->decrypt($crypttext, $iv);
		return $cleartext;
	}

	//
	// Tiki events
	////////////////////////////////

	static function onUserLogin($user, $cleartextPwd)
	{
		// Encode the phrase
		$phraseMD5 = md5($cleartextPwd);

		// Store the pass phrase in a session variable
		$_SESSION['cryptphrase'] = $phraseMD5;
	}

	// static function
	// Change/Rehash the password, given the old and the new key phrases
	static function onChangeUserPassword($user, $oldCleartextPwd, $newCleartextPwd)
	{
		// Lookup pref key that are encrypted data
		$prefKeys = array('userkey');	// HARDCODE for now

		// Rehash encrypted preferences
		foreach($prefKeys as $userprefKey) {
			self::changeUserPassword($user, $userprefKey, md5($oldCleartextPwd), md5($newCleartextPwd));
		}
	}

	// static function
	// Change/Rehash the password, given the old and the new key phrases
	// Return true on success; false, if no stored password is found
	static function changeUserPassword($user, $userprefKey, $oldPhraseMD5, $newPhraseMD5)
	{
		// Retrieve the old password
		$cryptOld = new CryptLib();
		$cryptOld->initSeed($oldPhraseMD5);
		if (!$cryptOld->hasCrypt()) {
			// Crypt is not available.
			// Only Base64 encoding. No conversion needed
			return false;
		}
		$cleartextPwd = $cryptOld->getUserPassword($user, $userprefKey);
		$cryptOld->release();
		if ($cleartextPwd == false) {
			return false;
		}

		// Rehash and save
		$cryptNew = new CryptLib();
		$cryptNew->initSeed($newPhraseMD5);
		$cryptPwd = $cryptNew->setUserPassword($user, $userprefKey, $cleartextPwd);
		$cryptNew->release();
		if ($cryptPwd == false) {
			return false;
		}

		// Rehashed OK
		return true;
	}


	//
	// Crypt
	////////////////////////////////


	// Check if encryption is used (and not Base64)
	function hasCrypt()
	{
		return $this->mcrypt != null;
	}


	// Use MCrypt if available. Otherwise Base64 encode only
	// Return base64 encoded string, containing either the crypttext or cleartext (if on base64 encoding is used)
	function encrypt($cleartext, $iv)
	{
		if ($this->hasCrypt()) {
			$crypttext = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $this->key, $cleartext, MCRYPT_MODE_CBC, $iv);
		} else {
			// Use Base64 encoding
			$crypttext = base64_encode($cleartext);
		}
		return $crypttext;
	}

	// Use MCrypt if available. Otherwise Base64 decode
	// Return cleartext
	function decrypt($crypttext, $iv)
	{
		if ($this->hasCrypt()) {
			$rawcleartext = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $this->key, $crypttext, MCRYPT_MODE_CBC, $iv);
			// Clear trailing null-characters
			$cleartext = rtrim($rawcleartext);
		} else {
			// Use Base64 encoding
			$cleartext = base64_decode($crypttext);
		}
		return $cleartext;
	}
}
