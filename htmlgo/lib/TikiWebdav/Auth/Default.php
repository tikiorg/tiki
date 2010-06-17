<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class TikiWebdav_Auth_Default extends ezcWebdavBasicAuth implements ezcWebdavAuthorizer, ezcWebdavLockAuthorizer, ezcWebdavBasicAuthenticator
{
	protected $tokens;
	protected $storageFile;

	protected $credentials = array(
			'guest' => 'guest'
			);

	public function __construct( $storageFile = null )
	{
		print_debug("Auth_Default: $storageFile\n");
		$this->storageFile = $storageFile;

		$this->tokens = array();
		if ( file_exists( $storageFile ) )
		{
			$this->tokens = unserialize(file_get_contents($storageFile));
		}
	}

	public function __destruct()
	{
		if (empty($this->storageFile) ) return true;
		print_debug("Auth_Default __destruct: ".$this->storageFile."\n");
		if ( $this->tokens !== array() )
		{
			file_put_contents( $this->storageFile, serialize( $this->tokens ));
		} else {
			print_debug('Auth_Default __destruct: '.serialize($this->tokens)."\n", FILE_APPEND);
		}
	} 

	public function authenticateAnonymous( ezcWebdavAnonymousAuth $data )
	{
		return false;
		$user = $_SESSION['webdav_user'] = 'Anonymous';
		return true;
	}

	public function authenticateBasic( ezcWebdavBasicAuth $data )
	{
		global $user;
		if (!isset($_SESSION['webdav_user']) ) {
			if ( $data->username === '' or $data->username === 'Anonymous') {
				$user = $_SESSION['webdav_user'] = 'Anonymous';
				print_debug("Login Anonymous User=".$data->username." Already logged\n");
				return true;
			}
			global $userlib; include_once('lib/userslib.php');
			list($isvalid, $user, $error) = $userlib->validate_user($data->username, $data->password);
			if ($isvalid) {
				$userlib->update_expired_groups();
				$user = $_SESSION['webdav_user'] = $data->username;
				return $isvalid; ///FIXME
			} 
			return false;
		} else {
			print_debug("Login Basic User=".$data->username." Already logged\n");
			$user = $_SESSION['webdav_user'];
			return true;
		}
		return false;
	}

	public function authenticateDigest( ezcWebdavDigestAuth $data )
	{
		return null;
		global $user;
		if (!isset($_SESSION['webdav_user']) ) {
			if ( $data->username === '' or $data->username === 'Anonymous') {
				$user = $_SESSION['webdav_user'] = 'Anonymous';
				return true;
			}
			global $userlib; include_once('lib/userslib.php');
			list($isvalid, $user, $error) = $userlib->validate_user($data->username, 'tototi');
			print_debug("Login Digest User=".$data->username." ".($isvalid ? 'OK' : 'FAILED')." ".print_r($data,true)."\n");
			if ($isvalid) {
				$userlib->update_expired_groups();
				$_SESSION['webdav_user'] = $data->username;
				return $isvalid;
			} else {
				return false;
			}
		} else {
			$user = $_SESSION['webdav_user'];
			return true;
		}
		return false;
	}

	public function authorize( $user, $path, $access = self::ACCESS_READ )
	{
		global $tikilib;
		global $filegallib; include_once('lib/filegals/filegallib.php');
		print_debug("Authorize...PATH=$path ACCESS=".($access == self::ACCESS_READ?'READ':'WRITE')."\n");
		$path = dirname(urldecode($path));
		if ( $path === '/' && $access === self::ACCESS_READ ) return true;
		$fgal = $filegallib->get_objectid_from_virtual_path($path);
		$id = $fgal['id'];
		if (!$id) {
			print_debug("Authorize...PATH=$path does not exist\n");
			return false;
		}

		$groups = $tikilib->get_user_groups( $user );
		$perms = Perms::getInstance();
		$perms->setGroups( $groups );
		$perms = Perms::get(array('type'=>'file gallery', 'object'=>$id));
		print_debug("Authorize...PERMS:".print_r($perms,true)."\n");
		$ret = false;
		if ( $access === self::ACCESS_READ ) {
			print_debug("Authorize...READ ".($perms->view_file_gallery?'OK':'PAS')." ".($perms->list_file_gallery?'OK':'PAS')."\n");
			if ($perms->view_file_gallery || $perms->list_file_gallery) {
				$ret = true;
			}
		} elseif ( $access === self::ACCESS_WRITE ) {
			print_debug("Authorize...WRITE ".($perms->upload_files?'OK':'PAS')." ".($perms->admin_file_galleries?'OK':'PAS')."\n");
			if ( $perms->upload_files || $perms->admin_file_galleries ) {
				$ret = true;
			}
		}

		print_debug("Authorize...USER=$user PATH=$path ".($ret?'OK':'PAS OK')."\n");
		return $ret;
	}

	public function assignLock( $user, $lockToken )
	{
		if ( $user == '' ) $user = 'Anonymous';
		print_debug("Assigning Lock($user, $lockToken)...\n");
		if ( !isset( $this->tokens[$user] ) )
		{
			$this->tokens[$user] = array();
		}
		$this->tokens[$user][$lockToken] = true;
	}

	public function ownsLock( $user, $lockToken )
	{
		if ( $user == '' ) $user = 'Anonymous';
		print_debug("Checking Lock($user, $lockToken): ".( isset( $this->tokens[$user][$lockToken] ) ? 'OK' : 'NOT OK' )."\n");
		return isset( $this->tokens[$user][$lockToken] );
		return true; ///FIXME
	}

	public function releaseLock( $user, $lockToken )
	{
		print_debug("Releasing Lock($user, $lockToken)...\n");
		if ( $user == '' ) $user = 'Anonymous';
		unset( $this->tokens[$user][$lockToken] );
	} 
}
