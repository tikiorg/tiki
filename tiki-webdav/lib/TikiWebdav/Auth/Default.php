<?php

///class TikiWebdav_Auth_Default implements ezcWebdavLockAuthorizer, ezcWebdavAnonymousAuthenticator, ezcWebdavAuthorizer
class TikiWebdav_Auth_Default extends ezcWebdavBasicAuth implements ezcWebdavAuthorizer, ezcWebdavLockAuthorizer, ezcWebdavBasicAuthenticator
{
	protected $tokens;
	protected $storageFile;

	protected $credentials = array(
		'guest' => 'guest'
	);

	public function __construct( $storageFile )
	{
/** FIXME
		$this->storageFile = $storageFile;

		$this->tokens = array();
		if ( file_exists( $storageFile ) )
		{
			$this->tokens = include $storageFile;
		}
**/
	}

	public function __destruct()
	{
/*		if ( $this->tokens !== array() )
		{
			file_put_contents(
				$this->storageFile,
				"<?php\n\nreturn " . var_export( $this->tokens, true ) . ";\n\n?>"
			);
		} else {
			file_put_contents('/tmp/tiki4log', 'TEST:'.serialize($this->tokens)."\n", FILE_APPEND);
		}
*/
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
				@file_put_contents('/tmp/tiki4log', "Login Anonymous User=".$data->username." Already logged\n", FILE_APPEND);
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
			@file_put_contents('/tmp/tiki4log', "Login Basic User=".$data->username." Already logged\n", FILE_APPEND);
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
			//file_put_contents('/tmp/tiki4log', "Login Digest User=".$data->username." ".($isvalid ? 'OK' : 'FAILED')." ".print_r($data,true)."\n", FILE_APPEND);
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
		@file_put_contents('/tmp/tiki4log', "Authorize...PATH=$path ACCESS=".($access == self::ACCESS_READ?'READ':'WRITE')."\n", FILE_APPEND);
		$path = dirname(urldecode($path));
		if ( $path === '/' ) && $access === self::ACCESS_READ ) return true;
		$fgal = $filegallib->get_objectid_from_virtual_path($path);
		$id = $fgal['id'];
		if (!$id) {
			@file_put_contents('/tmp/tiki4log', "Authorize...PATH=$path does not exist\n", FILE_APPEND);
			return false;
		}

		$groups = $tikilib->get_user_groups( $user );
    $perms = Perms::getInstance();
    $perms->setGroups( $groups );
		$perms = Perms::get(array('type'=>'file gallery', 'object'=>$id));
		//@file_put_contents('/tmp/tiki4log', "Authorize...PERMS:".print_r($perms,true)."\n", FILE_APPEND);
		$ret = false;
		if ( $access === self::ACCESS_READ ) {
			@file_put_contents('/tmp/tiki4log', "Authorize...READ ".($perms->view_file_gallery?'OK':'PAS')." ".($perms->list_file_gallery?'OK':'PAS')."\n", FILE_APPEND);
			if ($perms->view_file_gallery || $perms->list_file_gallery) {
				$ret = true;
			}
		} elseif ( $access === self::ACCESS_WRITE ) {
			@file_put_contents('/tmp/tiki4log', "Authorize...WRITE ".($perms->upload_files?'OK':'PAS')." ".($perms->admin_file_galleries?'OK':'PAS')."\n", FILE_APPEND);
			if ( $perms->upload_files || $perms->admin_file_galleries ) {
				$ret = true;
			}
		}
		
		@file_put_contents('/tmp/tiki4log', "Authorize...USER=$user PATH=$path ".($ret?'OK':'PAS OK')."\n", FILE_APPEND);
		return $ret;
	}

	public function assignLock( $user, $lockToken )
	{
		if ( $user == '' ) $user = 'Anonymous';
		file_put_contents('/tmp/tiki4log', "Assigning Lock($user, $lockToken)...\n", FILE_APPEND);
/*
		if ( !isset( $this->tokens[$user] ) )
		{
			$this->tokens[$user] = array();
		}
		$this->tokens[$user][$lockToken] = true;
*/
	}

	public function ownsLock( $user, $lockToken )
	{
/*		if ( $user == '' ) $user = 'Anonymous';
		file_put_contents('/tmp/tiki4log', "Checking Lock($user, $lockToken): ".( isset( $this->tokens[$user][$lockToken] ) ? 'OK' : 'NOT OK' )."\n", FILE_APPEND);*/
//		return isset( $this->tokens[$user][$lockToken] );
		return true; ///FIXME
	}

	public function releaseLock( $user, $lockToken )
	{
/*		file_put_contents('/tmp/tiki4log', "Releasing Lock($user, $lockToken)...\n", FILE_APPEND);
		if ( $user == '' ) $user = 'Anonymous';
		unset( $this->tokens[$user][$lockToken] );
*/
	} 
}
