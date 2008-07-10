<?php
//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

include_once ('lib/webmail/tikimaillib.php');

class EvLib extends TikiLib {
	function EvLib($db) {
		parent::TikiLib($db);
	}

	function replace_event($evId, $name, $description, $allowUserSub, $allowAnySub, $unsubMsg, $validateAddr) {
		if ($evId) {
			$query = "update `tiki_events` set `name`=?, `description`=?, `allowUserSub`=?, `allowAnySub`=?, `unsubMsg`=?, `validateAddr`=? where `evId`=?";
			$result = $this->query($query, array($name,$description,$allowUserSub,$allowAnySub,$unsubMsg,$validateAddr,(int)$evId));
		} else {
			$query = "insert into `tiki_events`(`name`,`description`,`allowUserSub`,`allowAnySub`,`unsubMsg`,`validateAddr`,`lastSent`,`editions`,`users`,`created`) ";
      $query.= " values(?,?,?,?,?,?,?,?,?,?)";
			$result = $this->query($query, array($name,$description,$allowUserSub,$allowAnySub,$unsubMsg,$validateAddr,(int)$this->now,0,0,(int)$this->now));
			$queryid = "select max(`evId`) from `tiki_events` where `created`=?";
			$evId = $this->getOne($queryid, array((int)$this->now));
		}
		return $evId;
	}

	function replace_edition($evId, $subject, $data, $users) {
		$query = "insert into `tiki_sent_events`(`evId`,`subject`,`data`,`sent`,`users`) values(?,?,?,?,?)";
		$result = $this->query($query,array((int)$evId,$subject,$data,(int)$this->now,$users));
	}

	function get_subscribers($evId) {
		$query = "select email from `tiki_event_subscriptions` where `valid`=? and `evId`=?";
		$result = $this->query($query, array('y',(int)$evId));
		$ret = array();
		while ($res = $result->fetchRow()) {
			$ret[] = $res["email"];
		}
		return $ret;
	}

	function remove_event_subscription($evId, $email) {
		$valid = $this->getOne("select `valid` from `tiki_event_subscriptions` where `evId`=? and `email`=?", array((int)$evId,$email));
		$query = "delete from `tiki_event_subscriptions` where `evId`=? and `email`=?";
		$result = $this->query($query, array((int)$evId,$email));
		$this->update_users($evId);
	}

	function event_subscribe($evId, $email, $fname, $lname, $company, $charset="utf-8") {
		global $smarty, $tikilib, $user, $prefs;
		$info = $this->get_event($evId);
		$smarty->assign('info', $info);
		$code = $this->genRandomString($prefs['sender_email']);
		if ($info["validateAddr"] == 'y') {
			// Generate a code and store it and send an email  with the
			// URL to confirm the subscription put valid as 'n'
			$foo = parse_url($_SERVER["REQUEST_URI"]);
			$foopath = preg_replace('/tiki-admin_event_subscriptions.php/', 'tiki-events.php', $foo["path"]);
			$url_subscribe = $tikilib->httpPrefix(). $foopath;
			$query = "delete from `tiki_event_subscriptions` where `evId`=? and `email`=?";
			$result = $this->query($query,array((int)$evId,$email));
			$query = "insert into `tiki_event_subscriptions`(`evId`,`email`,`code`,`valid`,`subscribed`,`fname`,`lname`,`company`) values(?,?,?,?,?,?,?,?)";
			$result = $this->query($query,array((int)$evId,$email,$code,'n',(int)$this->now,$fname,$lname,$company));
			// Now send an email to the address with the confirmation instructions
			$smarty->assign('mail_date', $this->now);
			$smarty->assign('mail_user', $user);
			$smarty->assign('code', $code);
			$smarty->assign('url_subscribe', $url_subscribe);
			$smarty->assign('server_name', $_SERVER["SERVER_NAME"]);
			$mail_data = $smarty->fetch('mail/confirm_event_subscription.tpl');
			if (!isset($_SERVER["SERVER_NAME"])) {
				$_SERVER["SERVER_NAME"] = $_SERVER["HTTP_HOST"];
			}
			$mail = new TikiMail($user);
			$mail->setSubject(tra('Newsletter subscription information at '). $_SERVER["SERVER_NAME"]);
			$mail->setText($mail_data);
			if (!$mail->send(array($email)))
				return false;
		} else {
			$query = "delete from `tiki_event_subscriptions` where `evId`=? and `email`=?";
			$result = $this->query($query,array((int)$evId,$email));
			$query = "insert into `tiki_event_subscriptions`(`evId`,`email`,`code`,`valid`,`subscribed`,`fname`,`lname`,`company`) values(?,?,?,?,?,?,?,?)";
			$result = $this->query($query,array((int)$evId,$email,$code,'y',(int)$this->now,$fname,$lname,$company));
		}
		$this->update_users($evId);
		return true;
	}

	function confirm_subscription($code) {
		global $smarty, $prefs, $userlib, $tikilib;
		$foo = parse_url($_SERVER["REQUEST_URI"]);
		$url_subscribe = $tikilib->httpPrefix(). $foo["path"];
		$query = "select * from `tiki_event_subscriptions` where `code`=?";
		$result = $this->query($query,array($code));

		if (!$result->numRows()) return false;

		$res = $result->fetchRow();
		$info = $this->get_event($res["evId"]);
		$smarty->assign('info', $info);
		$query = "update `tiki_event_subscriptions` set `valid`=? where `code`=?";
		$result = $this->query($query,array('y',$code));
		// Now send a welcome email
		$smarty->assign('mail_date', $this->now);
		$user = $userlib->get_user_by_email($res["email"]); //global $user is not necessary defined as the user is not necessary logged in
		$smarty->assign('mail_user', $user);
		$smarty->assign('code', $res["code"]);
		$smarty->assign('url_subscribe', $url_subscribe);
		if (!isset($_SERVER["SERVER_NAME"])) {
			$_SERVER["SERVER_NAME"] = $_SERVER["HTTP_HOST"];
		}
		$mail = new TikiMail($user);
		$lg = ! $user ? $prefs['site_language'] : $this->get_user_preference($user, "language", $prefs['site_language']);
		$mail_data = $smarty->fetchLang($lg, 'mail/event_welcome_subject.tpl');
		$mail->setSubject(sprintf($mail_data, $info["name"], $_SERVER["SERVER_NAME"]));
		$mail_data = $smarty->fetchLang($lg, 'mail/event_welcome.tpl');
		$mail->setText($mail_data);
		if (!$mail->send(array($res["email"])))
				return false;
		return $this->get_event($res["evId"]);
	}

	function unsubscribe($code) {
		global $smarty, $prefs, $userlib, $tikilib;
		$foo = parse_url($_SERVER["REQUEST_URI"]);
		$url_subscribe = $tikilib->httpPrefix(). $foo["path"];
		$query = "select * from `tiki_event_subscriptions` where `code`=?";
		$result = $this->query($query,array($code));

		if (!$result->numRows()) return false;

		$res = $result->fetchRow();
		$info = $this->get_event($res["evId"]);
		$smarty->assign('info', $info);
		$smarty->assign('code', $res["code"]);
		$query = "delete from `tiki_event_subscriptions` where `code`=?";
		$result = $this->query($query,array($code));
		// Now send a bye bye email
		$smarty->assign('mail_date', $this->now);
		$user = $userlib->get_user_by_email($res["email"]); //global $user is not necessary defined as the user is not necessary logged in
		$smarty->assign('mail_user', $user);
		$smarty->assign('url_subscribe', $url_subscribe);
		$lg = ! $user ? $prefs['site_language']: $this->get_user_preference($user, "language", $prefs['site_language']);
		if (!isset($_SERVER["SERVER_NAME"])) {
			$_SERVER["SERVER_NAME"] = $_SERVER["HTTP_HOST"];
		}
		$mail = new TikiMail();
		$mail_data = $smarty->fetchLang($lg, 'mail/event_byebye_subject.tpl');
		$mail->setSubject(sprintf($mail_data, $info["name"], $_SERVER["SERVER_NAME"]));
		$mail_data = $smarty->fetchLang($lg, 'mail/event_byebye.tpl');
		$mail->setText($mail_data);
		$mail->send(array($res["email"]));

		$this->update_users($res["evId"]);
		return $this->get_event($res["evId"]);
	}

	function add_all_users($evId) {
		$query = "select `email` from `users_users`";
		$result = $this->query($query,array());
		while ($res = $result->fetchRow()) {
			$email = $res["email"];
			if (!empty($email)) {
				$this->event_subscribe($evId, $email, " ", " ", " ");
			}
		}
	}

	function add_all_group_emails($evId,$group) {
		$groups =  array_merge(array($group),$this->get_groups_all($group));
		$mid = implode(" or ",array_fill(0,count($groups),"`groupName`=?"));
		$query = "select `login`,`email`  from `users_users` uu, `users_usergroups` ug where uu.`userId`=ug.`userId` and ($mid)";
		$result = $this->query($query,$groups);
		$ret = array();
		while ($res = $result->fetchRow()) {
			if (!empty($res['email'])) {
				// $this->event_subscribe($evId, $res['email']);
				$ret[] = $res['email'];
			}
		}
		$ret = array_unique($ret);
		foreach ($ret as $o) {
			$this->event_subscribe($evId, $o, " ", " ", " ");
		}
		return $ret;
	}


	function get_event($evId) {
		$query = "select * from `tiki_events` where `evId`=?";
		$result = $this->query($query,array((int)$evId));
		if (!$result->numRows()) return false;
		$res = $result->fetchRow();
		return $res;
	}

	function get_edition($editionId) {
		$query = "select * from `tiki_sent_events` where `editionId`=?";
		$result = $this->query($query,array((int)$editionId));
		if (!$result->numRows()) return false;
		$res = $result->fetchRow();
		return $res;
	}

	function update_users($evId) {
		$users = $this->getOne("select count(*) from `tiki_event_subscriptions` where `evId`=?",array((int)$evId));
		$query = "update `tiki_events` set `users`=? where `evId`=?";
		$result = $this->query($query,array($users,(int)$evId));
	}

	function list_events($offset, $maxRecords, $sort_mode, $find) {
		$bindvars = array();
		if ($find) {
			$findesc = '%' . $find . '%';
			$mid = " where (`name` like ? or `description` like ?)";
			$bindvars[] = $findesc;
			$bindvars[] = $findesc;
		} else {
			$mid = " ";
		}

		$query = "select * from `tiki_events` $mid order by ".$this->convert_sortmode("$sort_mode");
		$query_cant = "select count(*) from `tiki_events` $mid";
		$result = $this->query($query,$bindvars,$maxRecords,$offset);
		$cant = $this->getOne($query_cant,$bindvars);
		$ret = array();

		while ($res = $result->fetchRow()) {
			$res["confirmed"] = $this->getOne("select count(*) from `tiki_event_subscriptions` where `valid`=? and `evId`=?",array('y',(int)$res["evId"]));
			$ret[] = $res;
		}
		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}

	function list_editions($evId, $offset, $maxRecords, $sort_mode, $find) {
		$bindvars = array();
		$mid = "";
		
		if ($evId) {
			$mid.= " and tn.`evId`=". intval($evId);
		}
		
		if ($find) {
			$findesc = '%' . $find . '%';
			$mid.= " and (`subject` like ? or `data` like ?)";
			$bindvars[] = $findesc;
			$bindvars[] = $findesc;
		}

		$query = "select tsn.`editionId`,tn.`evId`,`subject`,`data`,tsn.`users`,`sent`,`name` from `tiki_events` tn, `tiki_sent_events` tsn ";
		$query.= " where tn.`evId`=tsn.`evId` $mid order by ".$this->convert_sortmode("$sort_mode");
		$query_cant = "select count(*) from `tiki_events` tn, `tiki_sent_events` tsn where tn.`evId`=tsn.`evId` $mid";
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

	function list_event_subscriptions($evId, $offset, $maxRecords, $sort_mode, $find) {
		$bindvars = array((int)$evId);
		if ($find) {
			$findesc = '%' . $find . '%';
			$mid = " where `evId`=? and (`name` like ? or `description` like ?)";
			$bindvars[] = $findesc;
			$bindvars[] = $findesc;
		} else {
			$mid = " where `evId`=? ";
		}

		$query = "select * from `tiki_event_subscriptions` $mid order by ".$this->convert_sortmode("$sort_mode");
		$query_cant = "select count(*) from tiki_event_subscriptions $mid";
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

	function get_unsub_msg($evId, $email, $lang) {
		global $smarty, $prefs, $userlib, $tikilib;
		$foo = parse_url($_SERVER["REQUEST_URI"]);

		$foo = str_replace('send_events', 'events', $foo);
		$url_subscribe = $tikilib->httpPrefix(). $foo["path"];
		$code = $this->getOne("select `code` from `tiki_event_subscriptions` where `evId`=? and `email`=?",array((int)$evId,$email));
		$url_unsub = $url_subscribe . '?unsubscribe=' . $code;
		$user = $userlib->get_user_by_email($email);
		$lg = ! $user ? $prefs['site_language'] : $this->get_user_preference($user, "language", $prefs['site_language']);
		$msg = $smarty->fetchLang($lg, 'mail/event_unsubscribe.tpl');
		$msg = '<br /><br />' . $msg . ": <a href='$url_unsub'>$url_unsub</a>";
		return $msg;
	}

	function remove_event($evId) {
		$query = "delete from `tiki_events` where `evId`=?";
		$result = $this->query($query,array((int)$evId));
		$query = "delete from `tiki_event_subscriptions` where `evId`=?";
		$result = $this->query($query,array((int)$evId));
		$this->remove_object('event', $evId);
		return true;
	}

	function remove_edition($editionId) {
		$query = "delete from `tiki_sent_events` where `editionId`=?";
		$result = $this->query($query,array((int)$editionId));
	}

}
global $dbTiki;
$evlib = new EvLib($dbTiki);

?>
