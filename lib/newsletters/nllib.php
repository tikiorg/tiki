<?php
// $Id: /cvsroot/tikiwiki/tiki/lib/newsletters/nllib.php,v 1.63.2.2 2008-01-17 15:47:10 sylvieg Exp $
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

	function replace_newsletter($nlId, $name, $description, $allowUserSub, $allowAnySub, $unsubMsg, $validateAddr,$allowTxt, $frequency , $author) {
		if ($nlId) {
			$query = "update `tiki_newsletters` set `name`=?, 
								`description`=?, 
								`allowUserSub`=?,
								`allowTxt`=?, 
								`allowAnySub`=?, 
								`unsubMsg`=?, 
								`validateAddr`=?, 
								`frequency`=? where `nlId`=?";
			$result = $this->query($query, array($name, $description, $allowUserSub, $allowTxt, $allowAnySub, $unsubMsg, $validateAddr, $frequency, (int)$nlId));
		} else {
			$query = "insert into `tiki_newsletters`(
								`name`,
								`description`,
								`created`,
								`lastSent`,
								`editions`,
								`users`,
								`allowUserSub`,
								`allowTxt`,
								`allowAnySub`,
								`unsubMsg`,
								`validateAddr`,
								`frequency`,
								`author`
								) ";
      $query.= " values(?,?,?,?,?,?,?,?,?,?,?,?,?)";
			$result = $this->query($query, array($name,
							$description,
							(int)$this->now,
							0,
							0,
							0,
							$allowUserSub,
							$allowTxt,
							$allowAnySub,
							$unsubMsg,
							$validateAddr,
							NULL,
							$author));
			$queryid = "select max(`nlId`) from `tiki_newsletters` where `created`=?";
			$nlId = $this->getOne($queryid, array((int)$this->now));
		}
		return $nlId;
	}

	function replace_edition($nlId, $subject, $data, $users, $editionId=0, $draft=false, $datatxt='', $files=array()) {
		if ($draft == false) {
			if( $editionId > 0 && $this->getOne('select `sent` from `tiki_sent_newsletters` where `editionId`=?', array( (int)$editionId )) == -1 ) {
				// save and send a draft
				$query = "update `tiki_sent_newsletters` set `subject`=?, `data`=?, `sent`=?, `users`=? , `datatxt`=? ";
				$query.= "where editionId=? and nlId=?";
				$result = $this->query($query,array($subject,$data, (int)$this->now, $users, $datatxt, (int)$editionId,(int)$nlId));
				$query = "update `tiki_newsletters` set `editions`= `editions`+ 1 where `nlId`=? ";
				$result = $this->query($query,array((int)$nlId));
				$query = "delete from `tiki_sent_newsletters_files` where `editionId`=?";
				$result = $this->query($query,array((int)$editionId));
			} else {
				 // save and send an edition
				$query = "insert into `tiki_sent_newsletters`(`nlId`,`subject`,`data`,`sent`,`users` ,`datatxt`) values(?,?,?,?,?,?)";
				$result = $this->query($query,array((int)$nlId,$subject,$data,(int)$this->now,$users,$datatxt));
				$query = "update `tiki_newsletters` set `editions`= `editions`+ 1 where `nlId`=?";
				$result = $this->query($query,array((int)$nlId));
				$editionId = $this->getOne('select max(`editionId`) from `tiki_sent_newsletters`');
			}
		} else {
			if( $editionId > 0 && $this->getOne('select `sent` from `tiki_sent_newsletters` where `editionId`=?', array( (int)$editionId )) == -1 ) {
				// save an existing draft
				$query = "update `tiki_sent_newsletters` set `subject`=?, `data`=?, `datatxt`=?";
				$query.= "where editionId=? and nlId=?";
				$result = $this->query($query,array($subject,$data,$datatxt,(int)$editionId,(int)$nlId));
				$query = "delete from `tiki_sent_newsletters_files` where `editionId`=?";
				$result = $this->query($query,array((int)$editionId));
			} else {
				// save a new draft
				$query = "insert into `tiki_sent_newsletters`(`nlId`,`subject`,`data`,`sent`,`users`,`datatxt`) values(?,?,?,?,?,?)";
				$result = $this->query($query,array((int)$nlId,$subject,$data,-1,0,$datatxt));
				$editionId = $this->getOne('select max(`editionId`) from `tiki_sent_newsletters`');
			}
		}
		foreach($files as $file) {
			$query = "insert into `tiki_sent_newsletters_files` (`editionId`,`name`,`type`,`size`,`filename`) values (?,?,?,?,?)";
			$result = $this->query($query, array((int)$editionId, $file['name'], $file['type'], (int)$file['size'], $file['filename']));
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
		global $userlib, $prefs, $user;
		$return = array();
		$all_users = array();
		$group_users = array();
		$included_users = array();

		// Get list of the root groups (groups explicitely subscribed to this newsletter)
		//
		$groups = array();
		$query = "select `groupName` from `tiki_newsletter_groups` where `nlId`=?";
		$result = $this->query($query, array((int)$nlId));
		while ( $res = $result->fetchRow() ) {
			$groups = array_merge($groups, array($res["groupName"]), $this->get_included_groups($res["groupName"]));
		}

		// If some groups are subscribed to this newsletter, get the list of users from those groups to be able to add them as subscribers
		// + Generate a random code (to allow users to unsubscribe) for users who don't already have one
		//
		if ( count($groups) > 0 ) {
			$mid = " and (".implode(" or ",array_fill(0,count($groups),"`groupName`=?")).")";
			$query = "select distinct uu.`login`, uu.`email` from `users_users` uu, `users_usergroups` ug where uu.`userId`=ug.`userId` ".$mid; 
			$result = $this->query($query, $groups);
			while ( $res = $result->fetchRow() ) {
				if ( empty($res['email']) ) {
					if ( $prefs['login_is_email'] == 'y' && $user != 'admin' ) {
						$res['email'] = $res['login'];
					} else continue;
				}
				$all_users[$res['email']] = array(
					'nlId' => (int)$nlId,
					'email' => $res['email'],
					'code' => $this->genRandomString($res['login']),
					'valid' => 'y',
					'subscribed' => $this->now,
					'isUser' => 'g',
					'db_email' => $res['login'],
					'included' => 'n'
				);
				$group_users[] = $res['login'];
			}
		}
		unset($groups);

		// Add subscribers that comes from included newsletters (only if their email is not already in the current list)
		//   Those users need to be saved in database for the current newsletter, in order to allow them to unsubscribe to this newsletter only
		//   (This implies to generate a new unsubscription code for the current newsletter)
		//
		$incnl = $this->list_newsletter_included($nlId);
		foreach ( $incnl as $incid => $incname ) {
			$incall = $this->get_all_subscribers($incid, $genUnsub);
			foreach ( $incall as $res ) {
				if ( empty($all_users[$res['email']]) ) {
					$res['code'] = $this->genRandomString($res['db_email']);
					$res['included'] = 'y';
					$all_users[$res['email']] = $res;
					$included_users[] = $res['db_email'];
				}
			}
		}

		// Retrieve current subscribers of the list (into $all_users array)
		// Do not keep subscribers that are:
		//   - not valid (valid = n)
		//   - or that comes from a tiki group (isUser = g)
		//     except those who explicitely unsubscribed themselves (valid = x), in order to keep this information and not add this user again later
		//     except those who are still in a subscribed group ($group_users)
		//   - or an included newsletter (included = y)
		//     except those who explicitely unsubscribed themselves (valid = x), in order to keep this information and not add this user again later
		//     except those who are still in an included newsletter ($included_users)
		//
		//   Note: users from included newsletters or groups (see above) are replaced by current subscribers to keep their code of unsubscription
		//
		$query = "select * from `tiki_newsletter_subscriptions` where `nlId`=?";
		$result = $this->query($query, array((int)$nlId));
		while ( $res = $result->fetchRow() ) {
			if ( ( $res['included'] != 'y' || $res['valid'] == 'x' ) && ((
					$res['valid'] != 'n' && ( $res['isUser'] != 'g' || $res['valid'] == 'x' ) )
					|| ( $res['isUser'] == 'g' && in_array($res['email'], $group_users) )
				)
				|| ( $res['included'] == 'y' && in_array($res['email'], $included_users) )
			) {
				$res['db_email'] = $res['email'];

				// Update e-mails of tiki users (directly included or included via a group)
				// When the e-mail already exists for another subscriber, keep the other subscriber
				//   (e.g. to keep information of users that subscribed themselves)
				//
				if ( $res['isUser'] == 'y' || $res['isUser'] == 'g' ) {
					$res['email'] = $userlib->get_user_email($res['db_email']);
				}

				// Add new subscribers to $all_users, or replace the information that was already there from group users
				//   In case of valid users from included newsletters, update everything except the unsubscribe code
				if ( $res['included'] == 'y' && $res['valid'] == 'y' ) {
					$all_users[$res['email']]['code'] = $res['code'];
				} else {
					$all_users[$res['email']] = $res;
				}
			}
		}

		// Update database if requested
		//
		if ( $genUnsub ) {
			$this->query('DELETE FROM `tiki_newsletter_subscriptions` WHERE `nlId`=?', array((int)$nlId));
			$query = "INSERT INTO `tiki_newsletter_subscriptions` (`nlId`,`email`,`code`,`valid`,`subscribed`,`isUser`,`included`) VALUES (?,?,?,?,?,?,?)";
			foreach ( $all_users as $res ) {
				$this->query($query, array(
					(int)$nlId,
					$res['db_email'],
					$res['code'],
					$res['valid'],
					$res['subscribed'],
					$res['isUser'],
					$res['included']
				));
			}
		}

		// Only send the newsletter to valid and confirmed emails (valid=y)
		foreach ( $all_users as $r ) {
			if ( $r['valid'] == 'y' ) $return[] = $r;
		}

		return $return;
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

	function remove_newsletter_included($nlId, $includedId) {
		$query = "delete from `tiki_newsletter_included` where `nlId`=? and `includedId`=?";
		$this->query($query, array((int)$nlId,$includedId), -1, -1, false);
	}

	function newsletter_subscribe($nlId, $add, $isUser='n', $validateAddr='', $addEmail='') {
		global $smarty, $tikilib, $user, $prefs, $userlib;
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
			$query = "insert into `tiki_newsletter_subscriptions`(`nlId`,`email`,`code`,`valid`,`subscribed`,`isUser`,`included`) values(?,?,?,?,?,?,?)";
			$result = $this->query($query,array((int)$nlId,$add,$code,'n',(int)$this->now,$isUser,'n'));
			// Now send an email to the address with the confirmation instructions
			$smarty->assign('info', $info);
			$smarty->assign('mail_date', $this->now);
			$smarty->assign('mail_user', $user);
			$smarty->assign('code', $code);
			$smarty->assign('url_subscribe', $url_subscribe);
			$smarty->assign('server_name', $_SERVER["SERVER_NAME"]);
			$mail_data = $smarty->fetch('mail/confirm_newsletter_subscription.tpl');
			if (!isset($_SERVER["SERVER_NAME"])) {
				$_SERVER["SERVER_NAME"] = $_SERVER["HTTP_HOST"];
			}
			$mail = new TikiMail($user);
			$mail->setSubject(tra('Newsletter subscription information at').' '. $_SERVER["SERVER_NAME"]);
			$mail->setText($mail_data);
			if (!$mail->send(array($email)))
				return false;
			return true;
		} else {
			$query = "insert into `tiki_newsletter_subscriptions`(`nlId`,`email`,`code`,`valid`,`subscribed`,`isUser`,`included`) values(?,?,?,?,?,?,?)";
			$result = $this->query($query,array((int)$nlId,$add,$code,'y',(int)$this->now,$isUser,'n'));
		}
		/*$this->update_users($nlId);*/
		return false;
	}

	function confirm_subscription($code) {
		global $smarty, $tikilib, $prefs, $userlib;
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
		$smarty->assign('mail_date', $this->now);
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
		$lg = ! $user ? $prefs['site_language']: $this->get_user_preference($user, "language", $prefs['site_language']);
		$mail_data = $smarty->fetchLang($lg, 'mail/newsletter_welcome_subject.tpl');
		$mail->setSubject(sprintf($mail_data, $info["name"], $_SERVER["SERVER_NAME"]));
		$mail_data = $smarty->fetchLang($lg, 'mail/newsletter_welcome.tpl');
		$mail->setText($mail_data);
		if (!$mail->send(array($email)))
				return false;
		return $this->get_newsletter($res["nlId"]);
	}

	function unsubscribe($code,$mailit=false) {
		global $smarty, $prefs, $userlib, $tikilib;
		$foo = parse_url($_SERVER["REQUEST_URI"]);
		$url_subscribe = $tikilib->httpPrefix(). $foo["path"];
		$query = "select * from `tiki_newsletter_subscriptions` where `code`=?";
		$result = $this->query($query,array($code));

		if (!$result->numRows()) return false;

		$res = $result->fetchRow();
		$info = $this->get_newsletter($res["nlId"]);
		$smarty->assign('info', $info);
		$smarty->assign('code', $res["code"]);
		if ( $res["isUser"] == 'g' || $res["included"] == 'y' ) {
			$query = "update `tiki_newsletter_subscriptions` set `valid`='x' where `code`=?";
		} else {
			$query = "delete from `tiki_newsletter_subscriptions` where `code`=?";
		}
		$result = $this->query($query,array($code), -1, -1, false);
		// Now send a bye bye email
		$smarty->assign('mail_date', $this->now);
		if ($res["isUser"] == "y") {
			$user = $res["email"];
			$email = $userlib->get_user_email($user);
		} else {
			$email = $res["email"];
			$user = $userlib->get_user_by_email($email); //global $user is not necessary defined as the user is not necessary logged in
		}
		$smarty->assign('mail_user', $user);
		$smarty->assign('url_subscribe', $url_subscribe);
		$lg = ! $user ? $prefs['site_language'] : $this->get_user_preference($user, "language", $prefs['site_language']);
		if (!isset($_SERVER["SERVER_NAME"])) {
			$_SERVER["SERVER_NAME"] = $_SERVER["HTTP_HOST"];
		}
		if ($mailit) {
			$mail = new TikiMail();
			$mail_data = $smarty->fetchLang($lg, 'mail/newsletter_byebye_subject.tpl');
			$mail->setSubject(sprintf($mail_data, $info["name"], $_SERVER["SERVER_NAME"]));
			$mail_data = $smarty->fetchLang($lg, 'mail/newsletter_byebye.tpl');
			$mail->setText($mail_data);
			$mail->send(array($email));
		}
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

	function add_included($nlId, $includedId) {
		$query = "delete from `tiki_newsletter_included` where `nlId`=? and `includedId`=?";
		$this->query($query,array((int)$nlId,(int)$includedId), -1, -1, false);
		$query = "insert into `tiki_newsletter_included` (`nlId`,`includedId`) values(?,?)";
		$this->query($query,array((int)$nlId,(int)$includedId));
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
		$res['files']=$this->get_edition_files($editionId);
		return $res;
	}

	function get_edition_files($editionId) {
		global $prefs;
		$res=array();
		$query = "select * from `tiki_sent_newsletters_files` where `editionId`=?";
		$result = $this->query($query,array((int)$editionId));
		$res=array();
		while ($f = $result->fetchRow()) {
			$f['error']=0;
			$res[]=$f;
		}
		return $res;
	}

	function update_users($nlId) {
		$users = $this->getOne("select count(*) from `tiki_newsletter_subscriptions` where `nlId`=? and `valid`!=?",array((int)$nlId, 'x'));
		$query = "update `tiki_newsletters` set `users`=? where `nlId`=?";
		$result = $this->query($query,array($users,(int)$nlId));
	}
/* perms = a or between perms */
	function list_newsletters($offset, $maxRecords, $sort_mode, $find, $update='', $perms='', $full='y') {
		global $user, $tikilib;
		$bindvars = array();
		if ($find) {
			$findesc = '%' . $find . '%';
			$mid = " where (tn.`name` like ? or tn.`description` like ?)";
			$bindvars[] = $findesc;
			$bindvars[] = $findesc;
		} else {
			$mid = '';
		}

		$query = "select tn.*, max(tsn.`sent`) as lastSent from `tiki_newsletters` tn left join `tiki_sent_newsletters` tsn on (tn.`nlId` = tsn.`nlId`) $mid group by tn.`nlId` order by ".$this->convert_sortmode("$sort_mode");
		$result = $this->query($query,$bindvars,$maxRecords,$offset);
		$query_cant = "select count(*) from  `tiki_newsletters` as tn $mid";
		$cant = $this->getOne($query_cant,$bindvars);
		$ret = array();

		while ($res = $result->fetchRow()) {
			$objperm = $this->get_perm_object($res['nlId'], 'newsletter', '', false);
			$res['tiki_p_admin_newsletters'] = $objperm['tiki_p_admin_newsletters'];
			$res['tiki_p_send_newsletters'] = $objperm['tiki_p_send_newsletters'];
			$res['tiki_p_subscribe_newsletters'] = $objperm['tiki_p_subscribe_newsletters'];

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
			if ($full != 'n') {
				$ok = count($this->get_all_subscribers($res['nlId'], ""));
				$notok = $this->getOne("select count(*) from `tiki_newsletter_subscriptions` where `valid`=? and `nlId`=?",array('n',(int)$res['nlId']));
				$res["users"] = $ok + $notok;
				$res["confirmed"] = $ok;
				$res['drafts'] = $this->getOne("select count(*) from `tiki_sent_newsletters` where `nlId`=? and `sent`=-1", array((int)$res['nlId']));
			}
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
		global $tikilib, $user;
		$bindvars = array();
		$mid = "";
		
		if ($nlId) {
			$mid.= " and tn.`nlId`=". intval($nlId);
			$tiki_p_admin_newsletters = $tikilib->user_has_perm_on_object($user, $nlId, 'newsletter', 'tiki_p_admin_newsletters')? 'y': 'n';
			$tiki_p_send_newsletters = $tikilib->user_has_perm_on_object($user, $nlId, 'newsletter', 'tiki_p_send_newsletters')? 'y': 'n';
			$tiki_p_subscribe_newsletters = $tikilib->user_has_perm_on_object($user, $nlId, 'newsletter', 'tiki_p_subscribe_newsletters')? 'y': 'n';
		}
		
		if ($find) {
			$findesc = '%' . $find . '%';
			$mid.= " and (`subject` like ? or `data` like ?)";
			$bindvars[] = $findesc;
			$bindvars[] = $findesc;
		}

		$mid.=($drafts ? ' and tsn.`sent`=-1' : ' and tsn.`sent`<>-1');

		$query = "select tsn.`editionId`,tn.`nlId`,`subject`,`data`,tsn.`users`,`sent`,`name` from `tiki_newsletters` tn, `tiki_sent_newsletters` tsn ";
		$query.= " where tn.`nlId`=tsn.`nlId` $mid order by ".$this->convert_sortmode("$sort_mode");
		$result = $this->query($query,$bindvars,$maxRecords,$offset);
		$ret = array();
		$query_cant = "select count(*) from `tiki_newsletters` tn, `tiki_sent_newsletters` tsn where tn.`nlId`=tsn.`nlId` $mid";
		$cant = $this->getOne($query_cant,$bindvars);

		while ($res = $result->fetchRow()) {
			if ($nlId) {
				if ($perm && $$perm == 'n')
					continue;
				$res['tiki_p_admin_newsletters'] = $tiki_p_admin_newsletters;
				$res['tiki_p_send_newsletters'] = $tiki_p_send_newsletters;
				$res['tiki_p_subscribe_newsletters'] = $tiki_p_subscribe_newsletters;
			} else {
				$res['tiki_p_admin_newsletters'] = $tikilib->user_has_perm_on_object($user, $res['nlId'], 'newsletter', 'tiki_p_admin_newsletters')? 'y': 'n';
				$res['tiki_p_send_newsletters'] = $tikilib->user_has_perm_on_object($user, $res['nlId'], 'newsletter', 'tiki_p_send_newsletters')? 'y': 'n';
				$res['tiki_p_subscribe_newsletters'] = $tikilib->user_has_perm_on_object($user, $res['nlId'], 'newsletter', 'tiki_p_subscribe_newsletters')? 'y': 'n';
				if ($perm && $res[$perm] == 'n')
					continue;
			}
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
		$query_cant = "select count(*) from `tiki_newsletter_groups` $mid";
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

	function list_newsletter_included($nlId) {
		$query = "select a.`includedId`,b.`name` from `tiki_newsletter_included` a left join `tiki_newsletters` b on a.`includedId`=b.`nlId` where a.`nlId`=? ";
		$result = $this->query($query,array((int)$nlId));
		$ret = array();
		while ($res = $result->fetchRow()) {
			$ret[$res['includedId']] = $res['name'];
		}
		return $ret;
	}

	function list_newsletter_all_included($nlId,$check=array()) {
		$query = "select a.`includedId`,b.`name` from `tiki_newsletter_included` a left join `tiki_newsletters` b on a.`includedId`=b.`nlId` where a.`nlId`=? ";
		$result = $this->query($query,array((int)$nlId));
		$ret = array();
		while ($res = $result->fetchRow()) {
			if (!in_array($res['includedId'],$check)) {
				$check[] = $res['includedId'];
				$ret[$res['includedId']] = $res['name'];
				$back = $this->list_newsletter_all_included($res['includedId'],$check);
				$ret = $back + $check;
			}
		}
		return array_unique($ret);
	}

	function get_unsub_msg($nlId, $email, $lang, $code='', $user='') {
		global $smarty, $userlib, $tikilib;
		$pth = $tikilib->httpPrefix(). substr($_SERVER["REQUEST_URI"],0,strpos($_SERVER["REQUEST_URI"],'tiki-'));
		$foo = parse_url($_SERVER["REQUEST_URI"]);
		 $smarty->assign('url',$pth);
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
			$lang = ! $user ? $prefs['site_language'] : $this->get_user_preference($user, "language", $prefs['site_language']);

		$smarty->assign('thisuser',$user);
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
		$this->remove_object('newsletter', $nlId);
		return true;
	}

	function remove_edition($nlId, $editionId) {
		$query = "delete from `tiki_sent_newsletters` where `editionId`=?";
		$result = $this->query($query,array((int)$editionId),-1, -1, false);
		$query = "update `tiki_newsletters` set `editions`= `editions`- 1 where `nlId`=?";
		$result = $this->query($query,array((int)$nlId));
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
