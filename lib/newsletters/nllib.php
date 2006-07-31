<?php
// $Header: /cvsroot/tikiwiki/tiki/lib/newsletters/nllib.php,v 1.49 2006-07-31 13:05:16 hangerman Exp $
//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

include_once ('lib/webmail/tikimaillib.php');

class NlLib extends TikiLib {
	function NlLib($db) {
		parent::TikiLib($db);
	}

	function replace_newsletter($nlId, $name, $description, $allowUserSub, $allowAnySub, $unsubMsg, $validateAddr,$allowTxt) {
	
		$query="select count(`allowTxt`) from `tiki_newsletters`";
	        $result=$this->query($query,"",-1,-1,false);
		if (!$result) {
		     $query="ALTER TABLE `tiki_newsletters` ADD `allowTxt` varchar(1) ";
	             $result=$this->query($query,"");
		}
		if ($nlId) {
			$query = "update `tiki_newsletters` set `name`=?, `description`=?, `allowUserSub`=?, `allowAnySub`=?, `unsubMsg`=?, `validateAddr`=? , `allowTxt`=? where `nlId`=?";
			$result = $this->query($query, array($name,$description,$allowUserSub,$allowAnySub,$unsubMsg,$validateAddr,$allowTxt,(int)$nlId));
		} else {
			$now = date("U");
			$query = "insert into `tiki_newsletters`(`name`,`description`,`allowUserSub`,`allowAnySub`,`unsubMsg`,`validateAddr`,`lastSent`,`editions`,`users`,`created`,`allowTxt`) ";
      			$query.= " values(?,?,?,?,?,?,?,?,?,?,?)";
			$result = $this->query($query, array($name,$description,$allowUserSub,$allowAnySub,$unsubMsg,$validateAddr,(int)$now,0,0,(int)$now,$allowTxt));
			$queryid = "select max(`nlId`) from `tiki_newsletters` where `created`=?";
			$nlId = $this->getOne($queryid, array((int)$now));
		}
		return $nlId;
	}
	function replace_edition($nlId, $subject, $data, $users, $editionId=0, $draft=false,$datatxt='') {
		//Check the extra field for text msg
		//Has to be removed in prod
		$query="select count(`datatxt`) from `tiki_sent_newsletters`";

	        $result=$this->query($query,"",-1,-1,false);
		if (!$result) {
		     $query="ALTER TABLE `tiki_sent_newsletters` ADD `datatxt` longblob AFTER data";
	             $result=$this->query($query,"");
		}
		
		
		if( $draft == false ) {
			$now = date("U");
			
			if( $editionId > 0) {
				// save and send a draft
				$query = "update `tiki_sent_newsletters` set `subject`=?, `data`=?, `sent`=?, `users`=? , `datatxt`=? ";
				$query.= "where editionId=? and nlId=?";
				$result = $this->query($query,array($subject,$data, (int)$now, $users,$datatxt, (int)$editionId,(int)$nlId));
				$query = "update `tiki_newsletters` set `editions`= `editions`+ 1 where `nlId`=? ";
				$result = $this->query($query,array((int)$nlId));				
			}
			else {
				// save and send an edition				
				$query = "insert into `tiki_sent_newsletters`(`nlId`,`subject`,`data`,`sent`,`users` ,`datatxt`) values(?,?,?,?,?,?)";
				$result = $this->query($query,array((int)$nlId,$subject,$data,(int)$now,$users,$datatxt));
				$query = "update `tiki_newsletters` set `editions`= `editions`+ 1 where `nlId`=?";
				$result = $this->query($query,array((int)$nlId));
				$editionId = $this->getOne('select max(`editionId`) from `tiki_sent_newsletters`');				
			}
		} else {
			if( $editionId > 0 && $this->getOne('select `sent` from `tiki_sent_newsletters` where `editionId`=?', array( (int)$editionId )) >=0 ) {
				// save an existing draft
				$query = "update `tiki_sent_newsletters` set `subject`=?, `data`=?";
				$query.= "where editionId=? and nlId=?";			
				$result = $this->query($query,array($subject,$data,(int)$editionId,(int)$nlId));
				
			} else {
				// save a new draft
				$query = "insert into `tiki_sent_newsletters`(`nlId`,`subject`,`data`,`sent`,`users`,`datatxt`) values(?,?,?,?,?,?)";
				$result = $this->query($query,array((int)$nlId,$subject,$data,-1,0,$datatxt));
				$editionId = $this->getOne('select max(`editionId`) from `tiki_sent_newsletters`');				
			}
		}
		return $editionId;
	}

	/* get only the email subscribers */
	function get_subscribers($nlId) {
		$query = "select `email` from `tiki_newsletter_subscriptions` where `valid`=? and `nlId`=? and isUser !='y'";
		$result = $this->query($query, array('y',(int)$nlId));
		$ret = array();
		while ($res = $result->fetchRow()) {
			$ret[] = $res["email"];
		}
		return $ret;
	}

	function get_all_subscribers($nlId, $genUnsub) {
		global $userlib;
			/* cleaning */
		if ($genUnsub == "y") {	
			$query = "delete from `tiki_newsletter_subscriptions` where `nlId`=? and `isUser`=? and `valid` != ?";
			$result = $this->query($query, array((int)$nlId, 'g','x'));
		}
			/* get the root groups */
		$query = "select `groupName` from `tiki_newsletter_groups` where `nlId`=?";
		$result = $this->query($query, array((int)$nlId));
		$ret = array();
		$groups =array();
		while ($res = $result->fetchRow()) {
			$groups = array_merge($groups, array($res["groupName"]), $this->get_groups_all($res["groupName"]));
		}
		 	/* users already in or unsubscribed */
		$query = "select * from `tiki_newsletter_subscriptions` where `nlId`=? and `isUser`!='g'";
		$result = $this->query($query, array((int)$nlId));
		$out = array();
		$in = array();
		$inEmail = array();
		$potential = array();
		while ($res = $result->fetchRow()) {
			if ($res["valid"] == "x" || $res["valid"] == "n")
				$out[] = $res["email"];
			elseif ($res["isUser"] == "n" && $res["valid"] == "y") {
				$inEmail[] = $res["email"]; 
				$ret[] = array("login"=>"", "email"=>$res["email"], "code"=>$res["code"]);
			} elseif ($res["isUser"] == "y" && $res["valid"] == "y") {
				$potential[] = $res;
			}
		}
		foreach ($potential as $res) {
			$in[] = $res["email"];
			$email = $userlib->get_user_email($res["email"]);
			if (!in_array($email, $inEmail) && !in_array($email, $out))
				$ret[] = array("login"=>$res["email"], "email"=>$email, "code"=>$res["code"]);
		}

                $query = "select * from tiki_newsletter_subscriptions where isUser=? and valid=?";
		$result = $this->query($query, array('g', 'x'));
		$unsub_groupusers = array();
		while($res = $result->fetchRow()) {
			$unsub_groupusers[] = $res['email'];
		}
		
			/* potential users */
		if (count($groups) > 0) {
			$mid = " and (".implode(" or ",array_fill(0,count($groups),"`groupName`=?")).")";
			$query = "select  distinct uu.`login`, uu.`email` from `users_users` uu, `users_usergroups` ug where uu.`userId`=ug.`userId` ".$mid; 
			$result = $this->query($query, $groups);
			while ($res = $result->fetchRow()) {
				if (!in_array($res["login"], $in) && !in_array($res["login"], $out) && !in_array($res["email"], $inEmail) && !in_array($res['login'], $unsub_groupusers)) {
					if ($genUnsub == "y") {
						$query = "insert into `tiki_newsletter_subscriptions`(`nlId`,`email`,`code`,`valid`,`subscribed`,`isUser`) values(?,?,?,?,?,?)";
						$code = $this->genRandomString($res["login"]);
						$this->query($query,array((int)$nlId,$res["login"],$code,'y',(int)date('U'),'g'));
						$res["code"] = $code;
					}
					$ret[] = $res;
				}
			}
		}
//echo "<pre>Subscribers:";print_r($ret); echo "</pre>";
		return $ret;
	}

	function remove_newsletter_subscription($nlId, $email, $isUser) {
		$query = "delete from `tiki_newsletter_subscriptions` where `nlId`=? and `email`=? and `isUser`=?";
		$result = $this->query($query, array((int)$nlId,$email, $isUser),-1, -1, false);
		/*$this->update_users($nlId);*/
	}

	function remove_newsletter_group($nlId, $group) {
		$query = "delete from `tiki_newsletter_groups` where `nlId`=? and `groupName`=?";
		$result = $this->query($query, array((int)$nlId,$group), -1, -1, false);
	}

	function newsletter_subscribe($nlId, $add, $isUser='n', $validateAddr='', $addEmail='') {
		global $smarty, $tikilib, $user, $sender_email,  $userlib;
		if (empty($add))
			return false;
		if ($isUser == "y" && $addEmail == "y") {
			$add = $userlib->get_user_email($add);
			$isUser="n";
		}
		$query = "select * from `tiki_newsletter_subscriptions` where `nlId`=? and `email`=? and `isUser`=? and `valid`=?";
		$result = $this->query($query,array((int)$nlId,$add,$isUser, 'y'));
		if ($res = $result->fetchRow()) {
			return false; /* already subscribed and valid - keep the same valid status */
			}
		$code = $this->genRandomString($add);
		$now = date("U");
		$info = $this->get_newsletter($nlId);
		if ($info["validateAddr"] == 'y' && $validateAddr != 'n') {
			if ($isUser == "y")
				$email = $userlib->get_user_email($add);
			else
				$email = $add;
			/* if already has validated don't ask again */
			// Generate a code and store it and send an email  with the
			// URL to confirm the subscription put valid as 'n'
			$foo = parse_url($_SERVER["REQUEST_URI"]);
			$foopath = preg_replace('/tiki-admin_newsletter_subscriptions.php/', 'tiki-newsletters.php', $foo["path"]);
			$url_subscribe = $tikilib->httpPrefix(). $foopath;
			$query = "insert into `tiki_newsletter_subscriptions`(`nlId`,`email`,`code`,`valid`,`subscribed`,`isUser`) values(?,?,?,?,?,?)";
			$result = $this->query($query,array((int)$nlId,$add,$code,'n',(int)$now,$isUser));
			// Now send an email to the address with the confirmation instructions
			$smarty->assign('info', $info);
			$smarty->assign('mail_date', date("U"));
			$smarty->assign('mail_user', $user);
			$smarty->assign('code', $code);
			$smarty->assign('url_subscribe', $url_subscribe);
			$smarty->assign('server_name', $_SERVER["SERVER_NAME"]);
			$mail_data = $smarty->fetch('mail/confirm_newsletter_subscription.tpl');
			if (!isset($_SERVER["SERVER_NAME"])) {
				$_SERVER["SERVER_NAME"] = $_SERVER["HTTP_HOST"];
			}
			$mail = new TikiMail($user);
			$mail->setSubject(tra('Newsletter subscription information at '). $_SERVER["SERVER_NAME"]);
			$mail->setText($mail_data);
			if (!$mail->send(array($email)))
				return false;
			return true;
		} else {
			$query = "insert into `tiki_newsletter_subscriptions`(`nlId`,`email`,`code`,`valid`,`subscribed`,`isUser`) values(?,?,?,?,?,?)";
			$result = $this->query($query,array((int)$nlId,$add,$code,'y',(int)$now,$isUser));
		}
		/*$this->update_users($nlId);*/
		return false;
	}

	function confirm_subscription($code) {
		global $smarty;
		global $tikilib;
		global $sender_email;
		global $userlib;
		global $language;
		$foo = parse_url($_SERVER["REQUEST_URI"]);
		$url_subscribe = $tikilib->httpPrefix(). $foo["path"];
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
		if ($res["isUser"] == "y") {
			$user = $res["email"];
			$email = $userlib->get_user_email($user);
		} else {
			$email = $res["email"];
			$user = $userlib->get_user_by_email($email); //global $user is not necessary defined as the user is not necessary logged in
		}
		$smarty->assign('mail_user', $user);
		$smarty->assign('code', $res["code"]);
		$smarty->assign('url_subscribe', $url_subscribe);
		if (!isset($_SERVER["SERVER_NAME"])) {
			$_SERVER["SERVER_NAME"] = $_SERVER["HTTP_HOST"];
		}
		$mail = new TikiMail($user);
		$lg = !$user? $language: $this->get_user_preference($user, "language", $language);
		$mail_data = $smarty->fetchLang($lg, 'mail/newsletter_welcome_subject.tpl');
		$mail->setSubject(sprintf($mail_data, $info["name"], $_SERVER["SERVER_NAME"]));
		$mail_data = $smarty->fetchLang($lg, 'mail/newsletter_welcome.tpl');
		$mail->setText($mail_data);
		if (!$mail->send(array($email)))
				return false;
		return $this->get_newsletter($res["nlId"]);
	}

	function unsubscribe($code) {
		global $smarty;
		global $sender_email;
		global $userlib;
		global $tikilib;
		global $language;
		$foo = parse_url($_SERVER["REQUEST_URI"]);
		$url_subscribe = $tikilib->httpPrefix(). $foo["path"];
		$query = "select * from `tiki_newsletter_subscriptions` where `code`=?";
		$result = $this->query($query,array($code));

		if (!$result->numRows()) return false;

		$res = $result->fetchRow();
		$info = $this->get_newsletter($res["nlId"]);
		$smarty->assign('info', $info);
		$smarty->assign('code', $res["code"]);
		if ($res["isUser"] == 'g')
			$query = "update `tiki_newsletter_subscriptions` set `valid`='x' where `code`=?";
		else
			$query = "delete from `tiki_newsletter_subscriptions` where `code`=?";
		$result = $this->query($query,array($code), -1, -1, false);
		// Now send a bye bye email
		$smarty->assign('mail_date', date("U"));
		if ($res["isUser"] == "y") {
			$user = $res["email"];
			$email = $userlib->get_user_email($user);
		} else {
			$email = $res["email"];
			$user = $userlib->get_user_by_email($email); //global $user is not necessary defined as the user is not necessary logged in
		}
		$smarty->assign('mail_user', $user);
		$smarty->assign('url_subscribe', $url_subscribe);
		$lg = !$user? $language: $this->get_user_preference($user, "language", $language);
		if (!isset($_SERVER["SERVER_NAME"])) {
			$_SERVER["SERVER_NAME"] = $_SERVER["HTTP_HOST"];
		}
		$mail = new TikiMail();
		$mail_data = $smarty->fetchLang($lg, 'mail/newsletter_byebye_subject.tpl');
		$mail->setSubject(sprintf($mail_data, $info["name"], $_SERVER["SERVER_NAME"]));
		$mail_data = $smarty->fetchLang($lg, 'mail/newsletter_byebye.tpl');
		$mail->setText($mail_data);
		$mail->send(array($email));

		/*$this->update_users($res["nlId"]);*/
		return $this->get_newsletter($res["nlId"]);
	}

	function add_all_users($nlId, $validateAddr='', $addEmail='') {
		$query = "select `email`, `login`from `users_users`";
		$result = $this->query($query,array());
		while ($res = $result->fetchRow()) {
			if ($addEmail == "y") {
				$add = $res["email"];
				$isUser = "n";
			} else {
				$add = $res["login"];
				$isUser = "y";
			}
			if (!empty($add)) {
				$this->newsletter_subscribe($nlId, $add, $isUser, $validateAddr, $addEmail);
			}
		}
	}

	function add_group($nlId, $group) {
		$query = "delete from `tiki_newsletter_groups` where `nlId`=? and `groupName`=?";
		$result = $this->query($query,array((int)$nlId,$group), -1, -1, false);
		$code = $this->genRandomString($group);
		$query = "insert into `tiki_newsletter_groups`(`nlId`,`groupName`,`code`) values(?,?,?)";
		$result = $this->query($query,array((int)$nlId,$group,$code));
	}

	function add_group_users($nlId, $group, $validateAddr='', $addEmail='') {
		$groups =  array_merge(array($group),$this->get_groups_all($group));
		$mid = implode(" or ",array_fill(0,count($groups),"`groupName`=?"));
		$query = "select `login`,`email`  from `users_users` uu, `users_usergroups` ug where uu.`userId`=ug.`userId` and ($mid)";
		$result = $this->query($query,$groups);
		$ret = array();
		while ($res = $result->fetchRow()) {
			if ($addEmail == "y")
				$ret[] = $res['email'];
			else
				$ret[] = $res['login'];
		}
		$ret = array_unique($ret);
print_r($ret);
		$isUser = $addEmail == "y"?"n": "y";
		foreach ($ret as $o) {
			$this->newsletter_subscribe($nlId, $o, $isUser, $validateAddr, $addEmail);
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
		$users = $this->getOne("select count(*) from `tiki_newsletter_subscriptions` where `nlId`=? and `valid`!=?",array((int)$nlId, 'x'));
		$query = "update `tiki_newsletters` set `users`=? where `nlId`=?";
		$result = $this->query($query,array($users,(int)$nlId));
	}
/* perms = a or between perms */
	function list_newsletters($offset, $maxRecords, $sort_mode, $find, $update='', $perms='') {
		global $user;
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
		$result = $this->query($query,$bindvars,$maxRecords,$offset);
		$cant = 0;
		$ret = array();

		while ($res = $result->fetchRow()) {
			$res['tiki_p_admin_newsletters'] = $this->user_has_perm_on_object($user, $res['nlId'], 'newsletter', 'tiki_p_admin_newsletters')? 'y': 'n';
			$res['tiki_p_send_newsletters'] = $this->user_has_perm_on_object($user, $res['nlId'], 'newsletter', 'tiki_p_send_newsletters')? 'y': 'n';
			$res['tiki_p_subscribe_newsletters'] = $this->user_has_perm_on_object($user, $res['nlId'], 'newsletter', 'tiki_p_subscribe_newsletters')? 'y': 'n';
			if (!empty($perms)) {
				$hasPerm = false;
				if (is_array($perms)) {
					foreach ($perms as $perm) {
			 			if ($res[$perm] == 'y') {
							$hasPerm = true;
							break;
						}
					}
				} else {
					$hasPerm = $res[$perm];
				}
				if (!$hasPerm)
					continue;
			}
			++$cant;
			$ok = count($this->get_all_subscribers($res["nlId"], ""));
			$notok = $this->getOne("select count(*) from `tiki_newsletter_subscriptions` where `valid`=? and `nlId`=?",array('n',(int)$res["nlId"]));			
			$res["users"] = $ok + $notok;
			$res["confirmed"] = $ok;
			$nb_drafts = $this->getOne("select count(*) from `tiki_sent_newsletters` where `nlId`=? and `sent`=-1", array((int)$res['nlId']));
			$res['drafts'] = $nb_drafts;
			$ret[] = $res;
		}
		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}

	function list_avail_newsletters() {
		$res = array();
		$query = "select `nlId`, `name` from `tiki_newsletters` where `allowUserSub`='y'";
		$bindvars = array();
		$result = $this->query($query, $bindvars);
		while ($rez = $result->fetchRow()){
			$res[] = $rez;
		}
		return $res;
	}

	function list_editions($nlId, $offset, $maxRecords, $sort_mode, $find, $drafts=false, $perm='') {
		global $user;
		$bindvars = array();
		$mid = "";
		
		if ($nlId) {
			$mid.= " and tn.`nlId`=". intval($nlId);
			$tiki_p_admin_newsletters = $this->user_has_perm_on_object($user, $nlId, 'newsletter', 'tiki_p_admin_newsletters')? 'y': 'n';
			$tiki_p_send_newsletters = $this->user_has_perm_on_object($user, $nlId, 'newsletter', 'tiki_p_send_newsletters')? 'y': 'n';
			$tiki_p_subscribe_newsletters = $this->user_has_perm_on_object($user, $nlId, 'newsletter', 'tiki_p_subscribe_newsletters')? 'y': 'n';
		}
		
		if ($find) {
			$findesc = '%' . $find . '%';
			$mid.= " and (`subject` like ? or `data` like ?)";
			$bindvars[] = $findesc;
			$bindvars[] = $findesc;
		}
		
		if($drafts) {
			$mid.= ' and tsn.`sent`=-1';
		} else {
			$mid.= ' and tsn.`sent`<>-1';
			
		}

		$query = "select tsn.`editionId`,tn.`nlId`,`subject`,`data`,tsn.`users`,`sent`,`name` from `tiki_newsletters` tn, `tiki_sent_newsletters` tsn";
		$query.= " where tn.`nlId`=tsn.`nlId` $mid order by ".$this->convert_sortmode("$sort_mode");
		$result = $this->query($query,$bindvars,$maxRecords,$offset);
		$ret = array();
		$cant = 0;

		while ($res = $result->fetchRow()) {
			if ($nlId) {
				if ($perm && $$perm == 'n')
					continue;
				$res['tiki_p_admin_newsletters'] = $tiki_p_admin_newsletters;
				$res['tiki_p_send_newsletters'] = $tiki_p_send_newsletters;
				$res['tiki_p_subscribe_newsletters'] = $tiki_p_subscribe_newsletters;
			} else {
				$res['tiki_p_admin_newsletters'] = $this->user_has_perm_on_object($user, $res['nlId'], 'newsletter', 'tiki_p_admin_newsletters')? 'y': 'n';
				$res['tiki_p_send_newsletters'] = $this->user_has_perm_on_object($user, $res['nlId'], 'newsletter', 'tiki_p_send_newsletters')? 'y': 'n';
				$res['tiki_p_subscribe_newsletters'] = $this->user_has_perm_on_object($user, $res['nlId'], 'newsletter', 'tiki_p_subscribe_newsletters')? 'y': 'n';
				if ($perm && $res[$perm] == 'n')
					continue;
			}
			$res['nbErrors'] = $this->get_edition_nb_errors($res['editionId']);
			$ret[] = $res;
			++$cant;
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
			$mid = " where `nlId`=? and `isUser`!='g' and `email` like ?";
			$bindvars[] = $findesc;
		} else {
			$mid = " where `nlId`=? and `isUser`!='g' ";
		}

		$query = "select * from `tiki_newsletter_subscriptions` $mid order by ".$this->convert_sortmode("$sort_mode").", email asc";
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

	function list_newsletter_groups($nlId, $offset=-1, $maxRecords=-1, $sort_mode='groupName_asc', $find='') {
		$bindvars = array((int)$nlId);
		if ($find) {
			$findesc = '%' . $find . '%';
			$mid = " where `nlId`=? and `groupName` like ?";
			$bindvars[] = $findesc;
		} else {
			$mid = " where `nlId`=? ";
		}

		$query = "select * from `tiki_newsletter_groups` $mid order by ".$this->convert_sortmode("$sort_mode");
		$query_cant = "select count(*) from tiki_newsletter_groups $mid";
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

	function get_unsub_msg($nlId, $email, $lang, $code='', $user='') {
		global $smarty, $language,$userlib,$tikilib;
		$foo = parse_url($_SERVER["REQUEST_URI"]);

		$foo = str_replace('send_newsletters', 'newsletters', $foo);
		$url_subscribe = $tikilib->httpPrefix(). $foo["path"];
		if ($code == '') {
			$isUser = $user? "y": "n";
			$code = $this->getOne("select `code` from `tiki_newsletter_subscriptions` where `nlId`=? and `email`=? and `isUser`=?",array((int)$nlId, $email, $isUser));
		}
		$url_unsub = $url_subscribe . '?unsubscribe=' . $code;
		$smarty->assign('url_unsub', $url_unsub);
		if ($user == '')
			$user = $userlib->get_user_by_email($email);
		if ($lang == '')
			$lang = !$user? $language: $this->get_user_preference($user, "language", $language);
		$msg = $smarty->fetchLang($lang, 'mail/newsletter_unsubscribe.tpl');
		return $msg;
	}

	function remove_newsletter($nlId) {
		$query = "delete from `tiki_newsletters` where `nlId`=?";
		$result = $this->query($query,array((int)$nlId), -1, -1, false);
		$query = "delete from `tiki_newsletter_subscriptions` where `nlId`=?";
		$result = $this->query($query,array((int)$nlId), -1, -1, false);
		$query = "delete from `tiki_newsletter_groups` where `nlId`=?";
		$result = $this->query($query,array((int)$nlId), -1, -1, false);
		$query = "delete from `tiki_sent_newsletters` where `nlId`=? and `sent`=-1";
		$result = $this->query($query,array((int)$nlId), -1, -1, false);
		$this->remove_object('newsletter', $nlId);
		return true;
	}

	function remove_edition($nlId, $editionId) {
		$query = "delete from `tiki_sent_newsletters` where `editionId`=?";
		$result = $this->query($query,array((int)$editionId),-1, -1, false);
		$query = "update `tiki_newsletters` set `editions`= `editions`- 1 where `nlId`=?";
		$result = $this->query($query,array((int)$nlId));
		$this->remove_edition_errors($editionId);
	}

	function valid_subscription($nlId, $email, $isUser) {
		$query = "update `tiki_newsletter_subscriptions` set `valid`= ? where `nlId`=? and `email`=? and `isUser`=?";
		$result = $this->query($query, array('y', (int)$nlId, $email, $isUser));
	}

	function list_tpls() {
		global $tikidomain;
		$tpls = array();
		if (is_dir("templates/$tikidomain/newsletters/")) {
			$h = opendir("templates/$tikidomain/newsletters/");
 			while ($file = readdir($h)) {
				if (ereg("\.tpl$", $file))
					$tpls[] = $file;
			}
		} elseif (is_dir("templates/newsletters/")) {
			$h = opendir("templates/newsletters/");
 			while ($file = readdir($h)) {
				if (ereg("\.tpl$", $file))
					$tpls[] = $file;
			}
		}
		return $tpls;
	}
	function memo_subscribers_edition($editionId, $users) {
		$query = 'insert into `tiki_sent_newsletters_errors` (`editionId`, `email`, `login`) values(?,?,?)';
		foreach ($users as $user) {
			$result = $this->query($query, array((int)$editionId, $user['email'], $user['login']));
		}
	}
	function delete_edition_subscriber($editionId, $user) {
		$query = 'delete from `tiki_sent_newsletters_errors` where `editionId`=? and `email`=? and `login`=?';
		$this->query($query, array((int)$editionId, $user['email'], $user['login']));
	}
	function mark_edition_subscriber($editionId, $user) {
		$query = 'update `tiki_sent_newsletters_errors` set `error`= ? where `editionId`=? and `email`=? and `login`=?';
		$this->query($query, array('y', (int)$editionId, $user['email'], $user['login']));
	}
	function get_edition_errors($editionId) {
		$query = 'select * from `tiki_sent_newsletters_errors` where `editionId`=?';
		$result = $this->query($query, array((int)$editionId));
		$ret = array();
		while ($res = $result->fetchRow()) {
			$ret[] = $res;
		}
		return $ret;
	}
	function get_edition_nb_errors($editionId) {
		$query = 'select count(*) from `tiki_sent_newsletters_errors` where `editionId`=?';
		return $this->getOne($query, array((int)$editionId));
	}
	function remove_edition_errors($editionId) {
		$query = 'delete from `tiki_sent_newsletters_errors` where `editionId`=?';
		$this->query($query, array((int)$editionId));
	}
}
global $dbTiki;
$nllib = new NlLib($dbTiki);

?>
