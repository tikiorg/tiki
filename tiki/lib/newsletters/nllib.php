<?php

class NlLib extends TikiLib {
	function NlLib($db) {
		parent::TikiLib($db);
	}

	function replace_newsletter($nlId, $name, $description, $allowUserSub, $allowAnySub, $unsubMsg, $validateAddr) {
		if ($nlId) {
			$query = "update `tiki_newsletters` set `name`=?, `description`=?, `allowUserSub`=?, `allowAnySub`=?, `unsubMsg`=?, `validateAddr`=?  where `nlId`=?";
			$result = $this->query($query, array($name,$description,$allowUserSub,$allowAnySub,$unsubMsg,$validateAddr,(int)$nlId));
		} else {
			$now = date("U");
			$query = "insert into `tiki_newsletters`(`name`,`description`,`allowUserSub`,`allowAnySub`,`unsubMsg`,`validateAddr`,`lastSent`,`editions`,`users`,`created`) ";
      $query.= " values(?,?,?,?,?,?,?,?,?,?)";
			$result = $this->query($query, array($name,$description,$allowUserSub,$allowAnySub,$unsubMsg,$validateAddr,(int)$now,0,0,(int)$now));
			$queryid = "select max(`nlId`) from `tiki_newsletters` where `created`=?";
			$nlId = $this->getOne($queryid, array((int)$now));
		}
		return $nlId;
	}

	function replace_edition($nlId, $subject, $data, $users) {
		$now = date("U");
		$query = "insert into `tiki_sent_newsletters`(`nlId`,`subject`,`data`,`sent`,`users`) values(?,?,?,?,?)";
		$result = $this->query($query,array((int)$nlId,$subject,$data,(int)$now,$users));
	}

	function get_subscribers($nlId) {
		$query = "select email from `tiki_newsletter_subscriptions` where `valid`=? and `nlId`=?";
		$result = $this->query($query, array('y',(int)$nlId));
		$ret = array();
		while ($res = $result->fetchRow()) {
			$ret[] = $res["email"];
		}
		return $ret;
	}

	function remove_newsletter_subscription($nlId, $email) {
		$valid = $this->getOne("select `valid` from `tiki_newsletter_subscriptions` where `nlId`=? and `email`=?", array((int)$nlId,$email));
		$query = "delete from `tiki_newsletter_subscriptions` where `nlId`=? and `email`=?";
		$result = $this->query($query, array((int)$nlId,$email));
		$this->update_users($nlId);
	}

	function newsletter_subscribe($nlId, $email) {
		global $smarty;
		global $user;
		global $sender_email;
		$info = $this->get_newsletter($nlId);
		$smarty->assign('info', $info);
		$code = md5($this->genPass());
		$now = date("U");
		if ($info["validateAddr"] == 'y') {
			// Generate a code and store it and send an email  with the
			// URL to confirm the subscription put valid as 'n'
			$foo = parse_url($_SERVER["REQUEST_URI"]);
			$foopath = preg_replace('/tiki-admin_newsletter_subscriptions.php/', 'tiki-newsletters.php', $foo["path"]);
			$url_subscribe = httpPrefix(). $foopath;
			$query = "delete from `tiki_newsletter_subscriptions` where `nlId`=? and `email`=?";
			$result = $this->query($query,array((int)$nlId,$email));
			$query = "insert into `tiki_newsletter_subscriptions`(`nlId`,`email`,`code`,`valid`,`subscribed`) values(?,?,?,?,?)";
			$result = $this->query($query,array((int)$nlId,$email,$code,'n',(int)$now));
			// Now send an email to the address with the confirmation instructions
			$smarty->assign('mail_date', date("U"));
			$smarty->assign('mail_user', $user);
			$smarty->assign('code', $code);
			$smarty->assign('url_subscribe', $url_subscribe);
			$smarty->assign('server_name', $_SERVER["SERVER_NAME"]);
			$mail_data = $smarty->fetch('mail/confirm_newsletter_subscription.tpl');
			@mail($email, tra('Newsletter subscription information at '). $_SERVER["SERVER_NAME"], $mail_data,
				"From: $sender_email\r\nContent-type: text/plain;charset=utf-8\r\n");
		} else {
			$query = "delete from `tiki_newsletter_subscriptions` where `nlId`=? and `email`=?";
			$result = $this->query($query,array((int)$nlId,$email));
			$query = "insert into `tiki_newsletter_subscriptions`(`nlId`,`email`,`code`,`valid`,`subscribed`) values(?,?,?,?,?)";
			$result = $this->query($query,array((int)$nlId,$email,$code,'y',(int)$now));
		}
		$this->update_users($nlId);
	}

	function confirm_subscription($code) {
		global $smarty;
		global $user;
		global $sender_email;
		$foo = parse_url($_SERVER["REQUEST_URI"]);
		$url_subscribe = httpPrefix(). $foo["path"];
		$query = "select * from `tiki_newsletter_subscriptions` where `code`=?";
		$result = $this->query($query,array($code));

		if (!$result->numRows()) return false;

		$res = $result->fetchRow();
		$info = $this->get_newsletter($res["nlId"]);
		$smarty->assign('info', $info);
		$query = "update `tiki_newsletter_subscriptions` set `valid`=? where `code`=?";
		$result = $this->query($query,array('y',$code));
		// Now send a welcome email
		$smarty->assign('mail_date', date("U"));
		$smarty->assign('mail_user', $user);
		$smarty->assign('code', $res["code"]);
		$smarty->assign('url_subscribe', $url_subscribe);
		$mail_data = $smarty->fetch('mail/newsletter_welcome.tpl');
		@mail($res["email"], tra('Welcome to '). $info["name"] . tra(' at '). $_SERVER["SERVER_NAME"], $mail_data,
			"From: $sender_email\r\nContent-type: text/plain;charset=utf-8\r\n");
		return $this->get_newsletter($res["nlId"]);
	}

	function unsubscribe($code) {
		global $smarty;
		global $user;
		global $sender_email;
		$foo = parse_url($_SERVER["REQUEST_URI"]);
		$url_subscribe = httpPrefix(). $foo["path"];
		$query = "select * from `tiki_newsletter_subscriptions` where `code`=?";
		$result = $this->query($query,array($code));

		if (!$result->numRows()) return false;

		$res = $result->fetchRow();
		$info = $this->get_newsletter($res["nlId"]);
		$smarty->assign('info', $info);
		$smarty->assign('code', $res["code"]);
		$query = "delete from `tiki_newsletter_subscriptions` where `code`=?";
		$result = $this->query($query,array($code));
		// Now send a bye bye email
		$smarty->assign('mail_date', date("U"));
		$smarty->assign('mail_user', $user);
		$smarty->assign('url_subscribe', $url_subscribe);
		$mail_data = $smarty->fetch('mail/newsletter_byebye.tpl');
		@mail($res["email"], tra('Bye bye from '). $info["name"] . tra(' at '). $_SERVER["SERVER_NAME"], $mail_data,
			"From: $sender_email\r\nContent-type: text/plain;charset=utf-8\r\n");
		$this->update_users($res["nlId"]);
		return $this->get_newsletter($res["nlId"]);
	}

	function add_all_users($nlId) {
		$query = "select `email` from `users_users`";
		$result = $this->query($query,array());
		while ($res = $result->fetchRow()) {
			$email = $res["email"];
			if (!empty($email)) {
				$this->newsletter_subscribe($nlId, $email);
			}
		}
	}

	function get_newsletter($nlId) {
		$query = "select * from `tiki_newsletters` where `nlId`=?";
		$result = $this->query($query,array((int)$nlId));
		if (!$result->numRows()) return false;
		$res = $result->fetchRow();
		return $res;
	}

	function get_edition($editionId) {
		$query = "select * from `tiki_sent_newsletters` where `editionId`=?";
		$result = $this->query($query,array((int)$editionId));
		if (!$result->numRows()) return false;
		$res = $result->fetchRow();
		return $res;
	}

	function update_users($nlId) {
		$users = $this->getOne("select count(*) from `tiki_newsletter_subscriptions` where `nlId`=?",array((int)$nlId));
		$query = "update `tiki_newsletters` set `users`=? where `nlId`=?";
		$result = $this->query($query,array($users,(int)$nlId));
	}

	function list_newsletters($offset, $maxRecords, $sort_mode, $find) {
		$bindvars = array();
		if ($find) {
			$findesc = '%' . $find . '%';
			$mid = " where (`name` like ? or `description` like ?)";
			$bindvars[] = $findesc;
			$bindvars[] = $findesc;
		} else {
			$mid = " ";
		}

		$query = "select * from `tiki_newsletters` $mid order by ".$this->convert_sortmode("$sort_mode");
		$query_cant = "select count(*) from `tiki_newsletters` $mid";
		$result = $this->query($query,$bindvars,$maxRecords,$offset);
		$cant = $this->getOne($query_cant,$bindvars);
		$ret = array();

		while ($res = $result->fetchRow()) {
			$res["confirmed"] = $this->getOne("select count(*) from `tiki_newsletter_subscriptions` where `valid`=? and `nlId`=?",array('y',(int)$res["nlId"]));
			$ret[] = $res;
		}
		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}

	function list_editions($offset, $maxRecords, $sort_mode, $find) {
		$bindvars = array();
		if ($find) {
			$findesc = '%' . $find . '%';
			$mid = " and (`subject` like ? or `data` like ?)";
			$bindvars[] = $findesc;
			$bindvars[] = $findesc;
		} else {
			$mid = " ";
		}

		$query = "select tsn.`editionId`,tn.`nlId`,`subject`,`data`,tsn.`users`,`sent`,`name` from `tiki_newsletters` tn, `tiki_sent_newsletters` tsn ";
		$query.= " where tn.`nlId`=tsn.`nlId` $mid order by ".$this->convert_sortmode("$sort_mode");
		$query_cant = "select count(*) from `tiki_newsletters` tn, `tiki_sent_newsletters` tsn where tn.`nlId`=tsn.`nlId` $mid";
		$result = $this->query($query,$bindvars,$maxRecords,$offset);
		$cant = $this->getOne($query_cant,$bindvars);
		$ret = array();

		while ($res = $result->fetchRow()) {
			$ret[] = $res;
		}

		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}

	function list_newsletter_subscriptions($nlId, $offset, $maxRecords, $sort_mode, $find) {
		$bindvars = array((int)$nlId);
		if ($find) {
			$findesc = '%' . $find . '%';
			$mid = " where `nlId`=? and (`name` like ? or `description` like ?)";
			$bindvars[] = $findesc;
			$bindvars[] = $findesc;
		} else {
			$mid = " where `nlId`=? ";
		}

		$query = "select * from `tiki_newsletter_subscriptions` $mid order by ".$this->convert_sortmode("$sort_mode");
		$query_cant = "select count(*) from tiki_newsletter_subscriptions $mid";
		$result = $this->query($query,$bindvars,$maxRecords,$offset);
		$cant = $this->getOne($query_cant,$bindvars);
		$ret = array();

		while ($res = $result->fetchRow()) {
			$ret[] = $res;
		}
		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}

	function get_unsub_msg($nlId, $email) {
		$foo = parse_url($_SERVER["REQUEST_URI"]);

		$foo = str_replace('send_newsletters', 'newsletters', $foo);
		$url_subscribe = httpPrefix(). $foo["path"];
		$code = $this->getOne("select `code` from `tiki_newsletter_subscriptions` where `nlId`=? and `email`=?",array((int)$nlId,$email));
		$url_unsub = $url_subscribe . '?unsubscribe=' . $code;
		$msg = '<br/><br/>' . tra( 'You can unsubscribe from this newsletter following this link'). ": <a href='$url_unsub'>$url_unsub</a>";
		return $msg;
	}

	function remove_newsletter($nlId) {
		$query = "delete from `tiki_newsletters` where `nlId`=?";
		$result = $this->query($query,array((int)$nlId));
		$query = "delete from `tiki_newsletter_subscriptions` where `nlId`=?";
		$result = $this->query($query,array((int)$nlId));
		$this->remove_object('newsletter', $nlId);
		return true;
	}

	function remove_edition($editionId) {
		$query = "delete from `tiki_sent_newsletters` where `editionId`=$editionId";
		$result = $this->query($query,array((int)$editionId));
	}

}

$nllib = new NlLib($dbTiki);

?>
