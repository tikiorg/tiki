<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}

/**
 *
 */
class MailinLib extends TikiLib
{

    /**
     * @param $offset
     * @param $maxRecords
     * @param $sort_mode
     * @param $find
     * @return array
     */
    function list_mailin_accounts($offset, $maxRecords, $sort_mode, $find)
	{

		if ($find) {
			$findesc = '%'.$find.'%';
			$mid = " where `account` like ?";
			$bindvars = array($findesc);
		} else {
			$mid = "	";
			$bindvars = array();
		}

		$query = "select * from `tiki_mailin_accounts` $mid order by ".$this->convertSortMode($sort_mode);
		$query_cant = "select count(*) from `tiki_mailin_accounts` $mid";
		$result = $this->query($query, $bindvars, $maxRecords, $offset);
		$cant = $this->getOne($query_cant, $bindvars);
		$ret = array();

		while ($res = $result->fetchRow('DB_FETCHMODE_ASSOC')) {
			// Decrypt the password
			$pwd = $this->decryptPassword($res['pass']);
			$res['pass'] = $pwd;

			$ret[] = $res;
		}

		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}

    /**
     * @param $offset
     * @param $maxRecords
     * @param $sort_mode
     * @param $find
     * @return array
     */
    function list_active_mailin_accounts($offset, $maxRecords, $sort_mode, $find)
	{

		if ($find) {
			$findesc = '%'.$find.'%';
			$mid = " where `active`=? and `account` like ?";
			$bindvars = array("y",$findesc);
		} else {
			$mid = " where `active`=?";
			$bindvars = array("y");
		}

		$query = "select * from `tiki_mailin_accounts` $mid order by ".$this->convertSortMode($sort_mode);
		$query_cant = "select count(*) from `tiki_mailin_accounts` $mid";
		$result = $this->query($query, $bindvars, $maxRecords, $offset);
		$cant = $this->getOne($query_cant, $bindvars);
		$ret = array();

		while ($res = $result->fetchRow('DB_FETCHMODE_ASSOC')) {
			// Decrypt the password
			$pwd = $this->decryptPassword($res['pass']);
			$res['pass'] = $pwd;

			$ret[] = $res;
		}

		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}

    /**
     * @param $accountId
     * @param $account
     * @param $pop
     * @param $port
     * @param $username
     * @param $pass
     * @param $smtp
     * @param $useAuth
     * @param $smtpPort
     * @param $type
     * @param $active
     * @param $anonymous
	 * @param $admin
	 * @param $attachments
     * @param null $article_topicId
     * @param null $article_type
     * @param null $discard_after
	 * @param null $show_inlineImages
	*  @param 0 $categoryId
	* @return bool
	 */
	function replace_mailin_account($accountId, $account, $pop, $port, $username, $clearpass, $smtp, $useAuth, $smtpPort, $type, $active, $anonymous, $admin, $attachments, $article_topicId = NULL, $article_type = NULL, $discard_after=NULL, $show_inlineImages='n', $save_html='y', $categoryId = 0, $namespace='', $respond_email = 'y')
	{
		// Encrypt password
		$pass = $this->encryptPassword($clearpass);
		
		if ($accountId) {
			$bindvars = array($account,$pop,(int)$port,(int)$smtpPort,$username,$pass,$smtp,$useAuth,$type,$active,$anonymous,$admin,$attachments,(int)$article_topicId,$article_type,$discard_after,$show_inlineImages,$save_html,$categoryId, $namespace, $respond_email, (int)$accountId);
			$query = "update `tiki_mailin_accounts` set `account`=?, `pop`=?, `port`=?, `smtpPort`=?, `username`=?, `pass`=?, `smtp`=?, `useAuth`=?, `type`=?, `active`=?, `anonymous`=?, `admin`=?, `attachments`=?, `article_topicId`=?, `article_type`=? , `discard_after`=?, `show_inlineImages`=?, `save_html`=?, `categoryId`=?, `namespace`=?, `respond_email`=? where `accountId`=?";
			$result = $this->query($query, $bindvars);
		} else {
			$bindvars = array($account,$pop,(int)$port,(int)$smtpPort,$username,$pass,$smtp,$useAuth,$type,$active,$anonymous,$admin,$attachments,(int)$article_topicId,$article_type, $show_inlineImages, $save_html, $categoryId, $namespace, $respond_email);
			$query = "delete from `tiki_mailin_accounts` where `account`=? and `pop`=? and `port`=? and `smtpPort`=? and `username`=? and `pass`=? and `smtp`=? and `useAuth`=? and `type`=? and `active`=? and `anonymous`=? and `admin`=? and `attachments`=? and `article_topicId`=?, `article_type`=?, `show_inlineImages`=?, `save_html`=?, `categoryId`=?, `namespace`=?, `respond_email`=?";
			$result = $this->query($query, $bindvars, -1, -1, false);
			$query = "insert into `tiki_mailin_accounts`(`account`,`pop`,`port`,`smtpPort`,`username`,`pass`,`smtp`,`useAuth`,`type`,`active`,`anonymous`,`admin`,`attachments`,`article_topicId`,`article_type`,`show_inlineImages`, `save_html`, `categoryId`, `namespace`, `respond_email`) values(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
			$result = $this->query($query, $bindvars);
		}
		return true;
	}

    /**
     * @param $accountId
     * @return bool
     */
    function remove_mailin_account($accountId)
	{
		$query = "delete from `tiki_mailin_accounts` where `accountId`=?";
		$result = $this->query($query, array((int)$accountId));
		return true;
	}

    /**
     * @param $accountId
     * @return bool
     */
    function get_mailin_account($accountId)
	{
		$query = "select * from `tiki_mailin_accounts` where `accountId`=?";
		$result = $this->query($query, array((int)$accountId));
		if (!$result->numRows()) {
			return false;
		}
		$res = $result->fetchRow('DB_FETCHMODE_ASSOC');
		
		// Decrypt the password
		$pwd = $this->decryptPassword($res['pass']);
		$res['pass'] = $pwd;

		return $res;
	}
	
	/**
	 * encryptPassword the email account password
	 *
	 * @param string $pwd Password in clear-text
	 * @return crypt Encoded password
	 *
	 */	
	function encryptPassword($pwd) {
		$encoded =  base64_encode($pwd);
		return $encoded;
	}

	/**
	 * decryptPassword the email account password
	 *
	 * @param crypt $$encrypted Encoded password
	 * @return string Return clear text password
	 *
	 */	
	function decryptPassword($encoded) {
		$plaintext =  base64_decode($encoded);
		return $plaintext;
	}
}
$mailinlib = new MailinLib;
