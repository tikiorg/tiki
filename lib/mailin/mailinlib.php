<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
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
class MailinLib extends TikiDb_Bridge
{
	function list_available_types()
	{
		$container = TikiInit::getContainer();
		$list = $container->get('tiki.mailin.providerlist');

		$out = [];
		foreach ($list->getList() as $provider) {
			$out[$provider->getType()] = [
				'name' => $provider->getLabel(),
				'enabled' => $provider->isEnabled(),
			];
		}

		return $out;
	}

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
	 * @param $protocol
     * @param $host
     * @param $port
     * @param $username
     * @param $pass
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
	function replace_mailin_account($accountId, $account, $protocol, $host, $port, $username, $clearpass, $type, $active, $anonymous, $admin, $attachments, $routing, $article_topicId = NULL, $article_type = NULL, $discard_after=NULL, $show_inlineImages='n', $save_html='y', $categoryId = 0, $namespace='', $respond_email = 'y', $leave_email = 'n')
	{
		// Fix values
		if ($attachments == null) {
			$attachments = 'n';
		}
		
		// Encrypt password
		$pass = $this->encryptPassword($clearpass);
		
		$data = array(
			'account' => $account,
			'protocol' => $protocol,
			'host' => $host,
			'port' => (int) $port,
			'username' => $username,
			'pass' => $pass,
			'type' => $type,
			'active' => $active,
			'anonymous' => $anonymous,
			'admin' => $admin,
			'attachments' => $attachments,
			'routing' => $routing,
			'article_topicId' => (int) $article_topicId,
			'article_type' => $article_type,
			'discard_after' => $discard_after,
			'show_inlineImages' => $show_inlineImages,
			'save_html' => $save_html,
			'categoryId' => (int) $categoryId,
			'namespace' => $namespace,
			'respond_email' => $respond_email,
			'leave_email' => $leave_email,
		);
		$table = $this->table('tiki_mailin_accounts');
		if ($accountId) {
			$table->update($data, [
				'accountId' => $accountId,
			]);

			return $accountId;
		} else {
			return $table->insert($data);
		}
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
