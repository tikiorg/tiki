<?php
// Tikiwiki authentication backend for phpBB3 with mysql4
// By Jacob Moen 10 Dec 2009
// Based on:
// Mediawiki authentication plugin for phpBB3 with mysql4
// By Steve Streeting 26 Dec 2008

require_once ("lib/auth/PasswordHash.php");

// some definitions for helping with authentication
// Er, what about definition clashes ?
define("PHPBB_INVALID_CREDENTIALS", -21);
define("PHPBB_INVALID_SYNTAX", -23);
define("PHPBB_NO_SUCH_USER", -25);
define("PHPBB_SUCCESS", -29);
define("SERVER_ERROR", -1);


//TODO: support other database types

class TikiPhpBBLib {

	function check($user, $pass) {

	// no need to progress further if the user doesn't even exist
		if(!$this->userExists($user)) {
			return PHPBB_NO_SUCH_USER;
		}

		// if the user does exist, authenticate
		if($this->authenticate($user, $pass)) {
			return PHPBB_SUCCESS;
		} else {
			return PHPBB_INVALID_CREDENTIALS;
		}

		// shouldn't happen..
		return PHPBB_INVALID_SYNTAX;
	}

	function connectdb() {
		global $prefs;
		$dbhost = $prefs['auth_phpbb_dbhost'];
		$dbuser = $prefs['auth_phpbb_dbuser'];
		$dbpasswd = $prefs['auth_phpbb_dbpasswd'];
		$dbname = $prefs['auth_phpbb_dbname'];
		// not really used - will it be used?
		//$dbport = $prefs['auth_phpbb_dbport'];

		//$dbconnection = mysql_connect($dbhost.':'.$dbport, $dbuser, $dbpasswd)
		$dbconnection = mysql_connect($dbhost, $dbuser, $dbpasswd)
			or die('AuthPhpBB : Could not connect: ' . mysql_error());

		if($dbconnection) {
			mysql_select_db($dbname, $dbconnection);
			return $dbconnection;
		}
		return false;
	}

	/**
	* Check whether there exists a user account with the given name.
	*
	* @param string $username
	* @return bool
	* @access public
	*/
	function userExists( $username ) {
		global $prefs;

		$dbconnection = $this->connectdb();

		// MySQL queries are case insensitive anyway
		$query = "select username from ".$prefs['auth_phpbb_table_prefix']."users where lcase(username) = lcase('". $username ."')";
		$result = mysql_query($query, $dbconnection)
			or die('AuthPhpBB : Query failed: ' . mysql_error());

		$numrows = mysql_num_rows($result);

		// Free resultset
		mysql_free_result($result);

		return $numrows > 0;

	}

	/**
	* Check if a username+password pair is a valid login.
	*
	* @param string $username
	* @param string $password
	* @return bool
	* @access public
	*/
	function authenticate( $username, $password ) {
		global $prefs;

		$dbconnection = $this->connectdb();

		$query = "select user_password from ".$prefs['auth_phpbb_table_prefix']."users where lcase(username) = lcase('". $username ."')";
		$result = mysql_query($query, $dbconnection)
			or die('AuthPhpBB : Query failed: ' . mysql_error());

		if (mysql_num_rows($result) == 0) {
			return false;
		}
		else {
		// TODO: check for phpBB version here, and select a different hasher, if needed.
		// This one is hardcoded for phpbb3
			$PasswordHasher = new PasswordHash(8, TRUE);

			$row = mysql_fetch_row($result);
			if ($PasswordHasher->CheckPassword($password, $row[0])) {
				return true;
			}
			else {
				return false;
			}

		}
	}

	/**
	* Returns a users email from the phpbb3 user table.
	* @param Username $username
	* @access public
	* @return email or 0
	*/
	function grabEmail( &$username ) {
		global $prefs;
		$dbconnection = $this->connectdb();

		// Just add email
		$query = "select user_email from ".$prefs['auth_phpbb_table_prefix'] . "users where lcase(username) = lcase('". $username ."')";
		$result = mysql_query($query, $dbconnection)
			or die('AuthPhpBB : Query failed: ' . mysql_error());

		if (mysql_num_rows($result) > 0) {
			$row = mysql_fetch_row($result);
			return $row[0];
		}

		return 0;
	}

}
