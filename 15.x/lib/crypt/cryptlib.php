<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}

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
 * The method setUserData encrypts the value and stores a user preference
 * getUserData reads it back into cleartext
 *
 * The secret key phrase is the MD5 sum of the username + Tiki password.
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
 * The system needs to have the username + both the old and new passwords in cleartext,
 * in order order to be able to rehash the encrypted data. This may not always be possible.
 * When an admin "hard" sets a user password, without having to know the previous password,
 * the old password is unknown. The encrypted data can then no longer be decrypted when the user logs in,
 * since the "secret key" has changed. The user will have to re-enter the lost data.
 * A recovery is possible. The recovery mechanism should call onChangeUserPassword.
 */
class CryptLib extends TikiLib
{
	private $key;			// mcrypt key
	private $mcrypt;		// mcrypt object
	private $iv;			// mcrypt initialization vector

	private $prefprefix = 'dp';		// prefix for user pref keys: 'test' => 'dp.test'

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
		if (!isset($_SESSION['cryptphrase'])) {
			throw new Exception(tra('Unable to locate cryptphrase'));
		}
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
	function makeCryptPhrase($username, $cleartextPwd)
	{
		return md5($username.$cleartextPwd);
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
	// Test/Check utilities
	////////////////////////////////

	// Check if encryption is used (and not Base64)
	function hasCrypt()
	{
		return $this->mcrypt != null;
	}

	// Check if any data exists the user preference.
	// Return true if data exit (not necessarily readable). false, if no stored data is found
	function hasUserData($userprefKey, $paramName = '')
	{
		global $user;

		if (!empty($paramName)) {
			$paramName = '.'.$paramName;
		}
		$storedPwd64 = $this->get_user_preference($user, $this->prefprefix.'.'.$userprefKey.$paramName);
		if (empty($storedPwd64)) {
			return false;
		}
		return true;
	}


	//
	// User data utilities
	////////////////////////////////


	/*
	 * Encrypt and save the data in the user preferences.
	 * The class specified prefix will be applied to the pref key.
	 * So, is the paramName, if specified. Given...
	 * $prefprefix = 'pwddom';
	 * $userprefKey = 'test'
	 * $paramName = ''
	 * => pwddom.test
	 * if $paramName = 'user', then
	 * * => pwddom.test.user
	 */
	//
	// Return false on failure otherwise the generated crypt text
	function setUserData($userprefKey, $cleartext, $paramName = '')
	{
		global $user;
		if (empty($cleartext)) {
			return false;
		}
		$storedPwd64 = $this->encryptData($cleartext);
		if (!empty($paramName)) {
			$paramName = '.'.$paramName;
		}
		$this->set_user_preference($user, $this->prefprefix.'.'.$userprefKey.$paramName, $storedPwd64);

		return $storedPwd64;
	}

	/*
	 * Encrypt and save the data in the user preferences for the specified user.
	 * The class specified prefix will be applied to the pref key.
	 * So, is the paramName, if specified. Given...
	 * $username = 'myuser'
	 * $prefprefix = 'pwddom';
	 * $userprefKey = 'test'
	 * $paramName = ''
	 * => pwddom.test
	 * if $paramName = 'user', then
	 * * => pwddom.test.user
	 */
	//
	// Return false on failure otherwise the generated crypt text
	function putUserData($username, $userprefKey, $cleartext, $paramName = '')
	{
		if (empty($cleartext)) {
			return false;
		}
		$storedPwd64 = $this->encryptData($cleartext);
		if (!empty($paramName)) {
			$paramName = '.'.$paramName;
		}
		$this->set_user_preference($username, $this->prefprefix.'.'.$userprefKey.$paramName, $storedPwd64);

		return $storedPwd64;
	}

	// Get the data from the user preferences.
	// Decrypt and return cleartext
	// Return false, if no stored data is found
	function getUserData($userprefKey, $paramName = '')
	{
		global $user;

		if (!empty($paramName)) {
			$paramName = '.'.$paramName;
		}
		$storedPwd64 = $this->get_user_preference($user, $this->prefprefix.'.'.$userprefKey.$paramName);
		if (empty($storedPwd64)) {
			return false;
		}
		$cleartext = $this->decryptData($storedPwd64);

		// Check if the cleartext contain any illigal password character.
		// 	If found, it indicates that the decryption has failed.
		if (!ctype_print ($cleartext)) {
			return false;
		}

		return $cleartext;
	}

	// Recover the stored cleartext data from the user preferences.
	// Return stored data in cleartext or false on error
	/*
	function recoverUserData($username, $cleartextPwd, $userprefKey, $paramName = '')
	{
		if (empty($cleartextPwd)) {
			return false;
		}
		// Initialize using the input params
		$cryptlib = new CryptLib();
		$phraseMD5 = md5($username.$cleartextPwd);
		$cryptlib->initSeed($phraseMD5);

		// Build the pref key
		if (!empty($paramName)) {
			$paramName = '.'.$paramName;
		}
		$prefKey = $cryptlib->prefprefix.'.'.$userprefKey.$paramName;

		// Get the stored data
		$storedPwd64 = $cryptlib->get_user_preference($username, $prefKey);
		if (empty($storedPwd64)) {
			return false;
		}

		// Decrypt
		$cleartext = $cryptlib->decryptData($storedPwd64);
		// Check if the cleartext contain any illigal password character.
		// 	If found, it indicates that the decryption has failed.
		if (!ctype_print ($cleartext)) {
			return false;
		}

		return $cleartext;
	}
*/
	function getPasswordDomains($use_prefix = false)
	{
		global $prefs;

		// Load the domain ddefinitions
		$domainsText = $prefs['feature_password_domains'];
		$domains = explode(',', $domainsText);

		// Trim whitespace from names
		foreach($domains as &$dom) {
			$dom = trim($dom);
		}

		// Add prefix
		if($use_prefix) {
			foreach($domains as &$dom) {
				$dom = $this->prefprefix.'.'.$dom;
			}
		}
		return $domains;
	}

	//
	// Data encryption
	////////////////////////////////

	// Encrypt data
	// Return encrypted data, or false on error
	function encryptData($cleartextData)
	{
		if(empty($cleartextData)) {
			return false;
		}

		// Encrypt the data
		$cryptData = $this->encrypt($cleartextData, $this->iv);
		if(empty($cryptData)) {
			return false;
		}

		// Save iv in the stored data
		$cryptData64 = base64_encode($this->iv.$cryptData);
		return $cryptData64;
	}

	// Decrypt data
	// Return cleartext data, or false on error
	function decryptData($cryptData64)
	{
		if(empty($cryptData64)) {
			return false;
		}

		// Extract the iv and crypttext
		$cryptData = base64_decode($cryptData64);
		$ivSize = mcrypt_enc_get_iv_size($this->mcrypt);
		$iv = substr($cryptData, 0, $ivSize);
		$crypttext = substr($cryptData, $ivSize);

		// Decrypt
		$cleartext = $this->decrypt($crypttext, $iv);
		return $cleartext;
	}

	//
	// Tiki events
	////////////////////////////////

	// User has logged in
	function onUserLogin($cleartextPwd)
	{
		global $user;

		// Encode the phrase
		$phraseMD5 = $this->makeCryptPhrase($user, $cleartextPwd);

		// Store the pass phrase in a session variable
		$_SESSION['cryptphrase'] = $phraseMD5;
	}

	// User has changed the password
	// Change/Rehash the password, given the old and the new key phrases
	function onChangeUserPassword($oldCleartextPwd, $newCleartextPwd)
	{
		global $user;

		// Lookup pref key that are encrypted data
		$domains = $this->getPasswordDomains();

		// Rehash encrypted preferences
		foreach($domains as $userprefKey) {
			$rc = $this->changeUserPassword($userprefKey, md5($user.$oldCleartextPwd), md5($user.$newCleartextPwd));

			// Also update the username, if defined
			if ($rc && $this->hasUserData($userprefKey, 'usr')) {
				$this->changeUserPassword($userprefKey.'.usr', md5($user.$oldCleartextPwd), md5($user.$newCleartextPwd));
			}
		}

		// Save the new cryptphrase, so the new hash is readable without logging out
		$this->onUserLogin($newCleartextPwd);
	}

	// Change/Rehash the password, given the old and the new key phrases
	// Return true on success; otherwise false, e.g. if no stored password is found, or a decryption failure
	function changeUserPassword($userprefKey, $oldPhraseMD5, $newPhraseMD5)
	{
		global $user;
		// Retrieve the old password
		$cryptOld = new CryptLib();
		$cryptOld->initSeed($oldPhraseMD5);
		if (!$cryptOld->hasCrypt()) {
			// Crypt is not available.
			// Only Base64 encoding. No conversion needed
			return false;
		}
		$cleartextPwd = $cryptOld->getUserData($userprefKey);
		$cryptOld->release();
		if ($cleartextPwd == false) {
			return false;
		}

		// Check if the cleartext contain any illigal password character.
		// 	If found, it indicates that the decryption has failed. The $oldPhraseMD5 may be incorrect?
		//  Then, do not proceed to rehash the password
		if (!ctype_print ($cleartextPwd)) {
			return false;
		}

		// Rehash and save
		$cryptNew = new CryptLib();
		$cryptNew->initSeed($newPhraseMD5);
		$cryptPwd = $cryptNew->setUserData($userprefKey, $cleartextPwd);
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

	// Use MCrypt if available. Otherwise Base64 encode only
	// Return base64 encoded string, containing either the crypttext or cleartext (if on base64 encoding is used)
	private function encrypt($cleartext, $iv)
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
	private function decrypt($crypttext, $iv)
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
