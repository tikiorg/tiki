<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

include_once ('lib/webmail/tikimaillib.php');

class NlLib extends TikiLib
{
	function replace_newsletter($nlId, $name, $description, $allowUserSub, $allowAnySub, $unsubMsg, $validateAddr,$allowTxt, $frequency , $author, $allowArticleClip = 'y', $autoArticleClip = 'n', $articleClipRange = null, $articleClipTypes = '') {
		if ($nlId) {
			$query = "update `tiki_newsletters` set `name`=?, 
								`description`=?, 
								`allowUserSub`=?,
								`allowTxt`=?, 
								`allowAnySub`=?, 
								`unsubMsg`=?, 
								`validateAddr`=?, 
								`frequency`=?, 
								`allowArticleClip`=?,
								`autoArticleClip`=?,
								`articleClipRange`=?,
								`articleClipTypes`=?																
								where `nlId`=?";
			$result = $this->query($query, array($name, $description, $allowUserSub, $allowTxt, $allowAnySub, $unsubMsg, $validateAddr, $frequency,
							$allowArticleClip,
							$autoArticleClip,
							$articleClipRange,
							$articleClipTypes,
							(int)$nlId));
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
								`author`,
								`allowArticleClip`,
								`autoArticleClip`,
								`articleClipRange`,
								`articleClipTypes`
								) ";
      $query.= " values(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
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
							$author,
							$allowArticleClip,
							$autoArticleClip,
							$articleClipRange,
							$articleClipTypes
							));
			$queryid = "select max(`nlId`) from `tiki_newsletters` where `created`=?";
			$nlId = $this->getOne($queryid, array((int)$this->now));
		}
		return $nlId;
	}

	function replace_edition($nlId, $subject, $data, $users, $editionId=0, $draft=false, $datatxt='', $files=array(), $wysiwyg=null) {
		if ($draft == false) {
			if( $editionId > 0 && $this->getOne('select `sent` from `tiki_sent_newsletters` where `editionId`=?', array( (int)$editionId )) == -1 ) {
				// save and send a draft
				$query = "update `tiki_sent_newsletters` set `subject`=?, `data`=?, `sent`=?, `users`=? , `datatxt`=?, `wysiwyg`=? ";
				$query.= "where editionId=? and nlId=?";
				$result = $this->query($query,array($subject,$data, (int)$this->now, $users, $datatxt, $wysiwyg, (int)$editionId,(int)$nlId));
				$query = "update `tiki_newsletters` set `editions`= `editions`+ 1 where `nlId`=? ";
				$result = $this->query($query,array((int)$nlId));
				$query = "delete from `tiki_sent_newsletters_files` where `editionId`=?";
				$result = $this->query($query,array((int)$editionId));
			} else {
				 // save and send an edition
				$query = "insert into `tiki_sent_newsletters`(`nlId`,`subject`,`data`,`sent`,`users` ,`datatxt`, `wysiwyg`) values(?,?,?,?,?,?,?)";
				$result = $this->query($query,array((int)$nlId,$subject,$data,(int)$this->now,$users,$datatxt, $wysiwyg));
				$query = "update `tiki_newsletters` set `editions`= `editions`+ 1 where `nlId`=?";
				$result = $this->query($query,array((int)$nlId));
				$editionId = $this->getOne('select max(`editionId`) from `tiki_sent_newsletters`');
			}
		} else {
			if( $editionId > 0 && $this->getOne('select `sent` from `tiki_sent_newsletters` where `editionId`=?', array( (int)$editionId )) == -1 ) {
				// save an existing draft
				$query = "update `tiki_sent_newsletters` set `subject`=?, `data`=?, `datatxt`=?, `wysiwyg`=? ";
				$query.= "where editionId=? and nlId=?";
				$result = $this->query($query,array($subject,$data,$datatxt,$wysiwyg, (int)$editionId,(int)$nlId));
				$query = "delete from `tiki_sent_newsletters_files` where `editionId`=?";
				$result = $this->query($query,array((int)$editionId));
			} else {
				// save a new draft
				$query = "insert into `tiki_sent_newsletters`(`nlId`,`subject`,`data`,`sent`,`users`,`datatxt`, `wysiwyg`) values(?,?,?,?,?,?,?)";
				$result = $this->query($query,array((int)$nlId,$subject,$data,-1,0,$datatxt,$wysiwyg));
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
	function get_subscribers($nlId, $isEmail='y') {
		$query = "select `email` from `tiki_newsletter_subscriptions` where `valid`=? and `nlId`=? and isUser !=?";
		$result = $this->query($query, array('y',(int)$nlId, $isEmail) );
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
		$page_included_emails = array();

		// Get list of the root groups (groups explicitely subscribed to this newsletter)
		//
		$groups = array();
		$query = "select `groupName`,`include_groups` from `tiki_newsletter_groups` where `nlId`=?";
		$result = $this->fetchAll($query, array((int)$nlId));
		foreach ($result as $res) {
			$groups[] = $res['groupName'];

			if ($res['include_groups'] == 'y') {
				$groups = array_merge($groups, $userlib->get_including_groups($res["groupName"], 'y'));
			}
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
				$res['email'] = strtolower($res['email']);
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
			$res['email'] = strtolower($res['email']);
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
					$res['email'] = strtolower($userlib->get_user_email($res['db_email']));
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
		
		$page_emails = $this->list_newsletter_pages( $nlId );
		if ($page_emails['cant'] > 0) {
			foreach ( $page_emails['data'] as $page) {
				$emails = $this->get_emails_from_page($page['wikiPageName']);
				if (!is_array($emails))
					continue;
				foreach ( $emails as $email ) {
					if (!empty($email)) {
						$res = array(
							'valid' => $page['validateAddrs'] == 'y' ? 'n' : 'y',
							'subscribed' => $this->now,
							'isUser' => 'n',
							'db_email' => $email,
							'email' => $email,
							'included' => 'n',
						);
						
						if ($page['addToList'] == 'y') {
							$res['code'] = $this->genRandomString($email);
							$all_users[$email] = $res;
						}
						$page_included_emails[$email] = $res;
					}
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
		
		$return = array_merge($return, $page_included_emails);

		return $return;
	}

	/**
	 * Removes newsletters subscriptions 
	 * 
	 * @param integer $nlId 
	 * @param string $email 
	 * @param boolean $isUser 
	 * @access public
	 * @return void
	 */
	function remove_newsletter_subscription($nlId, $email, $isUser)
	{
		$query = "delete from `tiki_newsletter_subscriptions` where `nlId`=? and `email`=? and `isUser`=?";
		$result = $this->query($query, array((int)$nlId, $email, $isUser), -1, -1, false);
	}

	/**
	 * Removes newsletters subscriptions with only the code as parameter
	 * 
	 * @param string $code 
	 * @access public
	 * @return void
	 */
	function remove_newsletter_subscription_code($code)
	{
		$query = 'delete from `tiki_newsletter_subscriptions` where `code`=?';
		$result = $this->query($query, array($code), -1, -1, false);
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
		$query = "select * from `tiki_newsletter_subscriptions` where `nlId`=? and `email`=? and `isUser`=?";
		$result = $this->query($query,array((int)$nlId,$add,$isUser));
		if ($res = $result->fetchRow()) {
			if ($res['valid'] == 'y') {
				return false; /* already subscribed and valid - keep the same valid status */
			}
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
//			$foopath = preg_replace('/tiki-admin_newsletter_subscriptions.php/', 'tiki-newsletters.php', $foo["path"]);
//			$url_subscribe = $tikilib->httpPrefix( true ). $foopath;
			$url_subscribe = $tikilib->httpPrefix( true ). '/tiki-newsletters.php';
			if (empty($res)) {
				$query = "insert into `tiki_newsletter_subscriptions`(`nlId`,`email`,`code`,`valid`,`subscribed`,`isUser`,`included`) values(?,?,?,?,?,?,?)";
				$bindvars = array((int)$nlId,$add,$code,'n',(int)$this->now,$isUser,'n');
			} else {
				// if already sub'ed but not validated then update code and timestamp (a.k.a. `subscribed`) and resend mail
				$query = "UPDATE `tiki_newsletter_subscriptions` SET `code`=?,`subscribed`=? WHERE `nlId`=? AND `email`=? AND `isUser`=? AND `valid`='n' AND `included`='n'";
				$bindvars = array($code,(int)$this->now,(int)$nlId,$add,$isUser);
			}
			$result = $this->query($query, $bindvars);
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
			include_once 'lib/mail/maillib.php';
			$zmail = tiki_get_admin_mail();
			$zmail->setSubject(tra('Newsletter subscription information at').' '. $_SERVER["SERVER_NAME"]);
			$zmail->setBodyText($mail_data);
			$zmail->addTo($email);
			try {
				$zmail->send();

				return true;
			} catch (Zend_Mail_Exception $e) {
				return false;
			}
		} else {
			if (!empty($res) && $res["valid"] == 'n') {
				$query = "update `tiki_newsletter_subscriptions` set `valid` = 'y' where `nlId` = ? and `email` = ? and `isUser` = ?";
				$this->query($query,array((int)$nlId,$add,$isUser));
				return true; 
			}
			$query = "insert into `tiki_newsletter_subscriptions`(`nlId`,`email`,`code`,`valid`,`subscribed`,`isUser`,`included`) values(?,?,?,?,?,?,?)";
			$result = $this->query($query,array((int)$nlId,$add,$code,'y',(int)$this->now,$isUser,'n'));
			return true;
		}
		/*$this->update_users($nlId);*/
		return false;
	}

	function confirm_subscription($code) {
		global $smarty, $tikilib, $prefs, $userlib;
		$foo = parse_url($_SERVER["REQUEST_URI"]);
		$url_subscribe = $tikilib->httpPrefix( true ). $foo["path"];
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
		include_once 'lib/mail/maillib.php';
		$zmail = tiki_get_admin_mail();
		$lg = ! $user ? $prefs['site_language']: $this->get_user_preference($user, "language", $prefs['site_language']);
		$mail_data = $smarty->fetchLang($lg, 'mail/newsletter_welcome_subject.tpl');
		$zmail->setSubject(sprintf($mail_data, $info["name"], $_SERVER["SERVER_NAME"]));
		$mail_data = $smarty->fetchLang($lg, 'mail/newsletter_welcome.tpl');
		$zmail->setBodyText($mail_data);
		$zmail->addTo($email);

		try {
			$zmail->send();

			return $this->get_newsletter($res["nlId"]);
		} catch (Zend_Mail_Exception $e) {
			return false;
		}
	}

	function unsubscribe($code,$mailit=false) {
		global $smarty, $prefs, $userlib, $tikilib;
		$foo = parse_url($_SERVER["REQUEST_URI"]);
		$url_subscribe = $tikilib->httpPrefix( true ). $foo["path"];
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
			include_once 'lib/mail/maillib.php';
			$zmail = tiki_get_admin_mail();
			$mail_data = $smarty->fetchLang($lg, 'mail/newsletter_byebye_subject.tpl');
			$zmail->setSubject(sprintf($mail_data, $info["name"], $_SERVER["SERVER_NAME"]));
			$mail_data = $smarty->fetchLang($lg, 'mail/newsletter_byebye.tpl');
			$zmail->setBodyText($mail_data);
			$zmail->addTo($email);

			try {
				$zmail->send();
			} catch (Zend_Mail_Exception $e) {
			}
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

	function add_group($nlId, $group, $include_groups = 'n') {
		$query = "delete from `tiki_newsletter_groups` where `nlId`=? and `groupName`=?";
		$result = $this->query($query,array((int)$nlId,$group), -1, -1, false);
		$code = $this->genRandomString($group);
		$query = "insert into `tiki_newsletter_groups`(`nlId`,`groupName`,`code`,`include_groups`) values(?,?,?,?)";
		$result = $this->query($query,array((int)$nlId,$group,$code,$include_groups));
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

		$query = "select tn.*, max(tsn.`sent`) as lastSent from `tiki_newsletters` tn left join `tiki_sent_newsletters` tsn on (tn.`nlId` = tsn.`nlId`) $mid group by tn.`nlId` order by ".$this->convertSortmode("$sort_mode");
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
		$query.= " where tn.`nlId`=tsn.`nlId` $mid order by ".$this->convertSortMode("$sort_mode");
		$result = $this->query($query,$bindvars,$maxRecords,$offset);
		$ret = array();
		$query_cant = "select count(*) from `tiki_newsletters` tn, `tiki_sent_newsletters` tsn where tn.`nlId`=tsn.`nlId` $mid";
		$cant = $this->getOne($query_cant,$bindvars);

		while ($res = $result->fetchRow()) {
			if ($nlId) {
				if ($tiki_p_admin_newsletters != 'y' && $perm && $$perm == 'n')
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
			$mid = " where `nlId`=? and (`valid` != 'y' or (`isUser` != 'g' and `included` != 'y')) and `email` like ?";
			$bindvars[] = $findesc;
		} else { // show all except valid by group or include newsletters
			$mid = " where `nlId`=?  and (`valid` != 'y' or (`isUser` != 'g' and `included` != 'y')) ";
		}

		$query = "select * from `tiki_newsletter_subscriptions` $mid order by ".$this->convertSortMode("$sort_mode").", email asc";
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

		$query = "select * from `tiki_newsletter_groups` $mid order by ".$this->convertSortMode("$sort_mode");
		$query_cant = "select count(*) from `tiki_newsletter_groups` $mid";
		$result = $this->query($query,$bindvars,$maxRecords,$offset);
		$cant = $this->getOne($query_cant,$bindvars);
		$ret = array();

		$userlib = TikiLib::lib('user');
		while ($res = $result->fetchRow()) {
			$res['additional_groups'] = array();
			if ($res['include_groups'] == 'y') {
				$res['additional_groups'] = $userlib->get_including_groups($res["groupName"], 'y');
			}
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
		$pth = $tikilib->httpPrefix( true ). substr($_SERVER["REQUEST_URI"],0,strpos($_SERVER["REQUEST_URI"],'tiki-'));
		$foo = parse_url($_SERVER["REQUEST_URI"]);
		 $smarty->assign('url',$pth);
		$foo = str_replace('send_newsletters', 'newsletters', $foo);
		$url_subscribe = $tikilib->httpPrefix( true ). $foo["path"];
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
				if (preg_match('/\.tpl$/', $file))
					$tpls[] = $file;
			}
		} elseif (is_dir("templates/newsletters/")) {
			$h = opendir("templates/newsletters/");
 			while ($file = readdir($h)) {
				if (preg_match('/\.tpl$/', $file))
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
		$query = 'delete from `tiki_sent_newsletters_errors` where `editionId`=? and `email`=?';
		$this->query($query, array((int)$editionId, $user['email']));
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
	
	function clip_articles($nlId) {
		global $artlib, $smarty;
		require_once 'lib/articles/artlib.php';
		$query = 'select `articleClipTypes`, `articleClipRange` from `tiki_newsletters` where nlId = ?';
		$result = $this->fetchAll($query, array($nlId));
		$articleClipTypes = unserialize($result[0]['articleClipTypes']);
		$date_min = $this->now - $result[0]['articleClipRange'];
		$date_max = $this->now;
		$articles = array();
		$articleClip = '';
		# Order array by publishDate
		if (!function_exists('cmp')) {
			function cmp($a,$b) {
				if ($a['publishDate'] == $b['publishDate']) return 0;
				return ($a['publishDate'] > $b['publishDate']) ? -1 : 1;
			}
		} 
		foreach ($articleClipTypes as $articleType) {
			$t_articles = $artlib->list_articles( 0, -1, 'publishDate_desc', '', $date_min, $date_max, false, $articleType);
			foreach ($t_articles["data"] as $t) {
				$articles[$t["articleId"]] = $t;	
			}
		}
		usort($articles,'cmp');
		foreach ($articles as $art) {
			$smarty->assign("nlArticleClipId", $art["articleId"]);
			$smarty->assign("nlArticleClipTitle", $art["title"]);
			$smarty->assign("nlArticleClipSubtitle", $art["subtitle"]);
			$smarty->assign("nlArticleClipParsedheading", $this->parse_data($art["heading"]));
			$smarty->assign("nlArticleClipPublishDate", $art["publishDate"]);
			$smarty->assign("nlArticleClipAuthorName", $art["authorName"]);
			$articleClip .= $smarty->fetch("mail/newsletter_articleclip.tpl");
		}
		return $articleClip;
	}

	// functions for getting email addresses from wiki pages
	
	function get_emails_from_page($wikiPageName) {
		global $prefs, $wikilib;
		
		include_once 'lib/wiki/wikilib.php';
		$emails = false;
		
		$canBeRefreshed = false;
		$o1 = $prefs['feature_wiki_protect_email'];
		$o2 = $prefs['feature_autolinks'];
		$prefs['feature_wiki_protect_email'] = 'n';
		$prefs['feature_autolinks'] = 'n';
		$pageContent = $wikilib->get_parse($wikiPageName, $canBeRefreshed);
		$prefs['feature_wiki_protect_email'] = $o1;
		$prefs['feature_autolinks'] = $o2;
		
		if (!empty($pageContent)) {
			$pageContent = strip_tags($pageContent, '<p><tr><br>');
			$pageContent = preg_replace(array('/<p.*?>/i','/<tr.*?>/i'), "", $pageContent);	// deal with stripped html from smarty
			$pageContent = str_replace(array('</p>','</tr>','<br />'), "\n", $pageContent);	// add linefeeds
			$pageContent = preg_replace('/[\\n\\r]/', "\n", $pageContent);	// in case there are MS lineends
			$pageContent = preg_replace('/\\n\\n/', "\n", $pageContent);	// remove blank lines
			$ary = explode("\n", $pageContent);
			$emails = array();
			foreach($ary as $a) {
				preg_match('/[a-z0-9\-_.]+?@[\w\-\.]+/i', $a, $m);
				if (count($m) > 0) {
					if (validate_email($m[0])) {
						$emails[] = strtolower($m[0]);
					}
				}
			}
		}
		
		return $emails;
	}
	
	function add_page($nlId, $wikiPageName, $validate = 'n', $addToList = 'n') {
		$query = "delete from `tiki_newsletter_pages` where `nlId`=? and `wikiPageName`=?";
		$this->query($query, array( (int)$nlId, $wikiPageName), -1, -1, false);
		$query = "insert into `tiki_newsletter_pages` (`nlId`,`wikiPageName`,`validateAddrs`,`addToList`) values(?,?,?,?)";
		$this->query($query, array( (int)$nlId, $wikiPageName, $validate, $addToList));
	}
	
	function remove_newsletter_page($nlId, $wikiPageName) {
		$query = "delete from `tiki_newsletter_pages` where `nlId`=? and `wikiPageName`=?";
		$this->query($query, array( (int)$nlId, $wikiPageName), -1, -1, false);
	}

	function list_newsletter_pages($nlId, $offset=-1, $maxRecords=-1, $sort_mode='wikiPageName_asc', $find='') {
		$bindvars = array((int)$nlId);
		if ($find) {
			$findesc = '%' . $find . '%';
			$mid = " where `nlId`=? and `wikiPageName` like ?";
			$bindvars[] = $findesc;
		} else {
			$mid = " where `nlId`=? ";
		}

		$query = "select * from `tiki_newsletter_pages` $mid order by ".$this->convertSortMode("$sort_mode");
		$query_cant = "select count(*) from `tiki_newsletter_pages` $mid";
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

	private function get_edition_mail($editionId, $target)
	{
		global $prefs;
		static $mailcache = array();

		if (! isset($mailcache[$editionId])) {
			$tikilib = TikiLib::lib('tiki');
			$headerlib = TikiLib::lib('header');

			$info = $this->get_edition($editionId);
			$nl_info = $this->get_newsletter($info['nlId']);


			// build the html
			$beginHtml = '<body><div id="tiki-center" class="clearfix content"><div class="wikitext">';
			$endHtml = '</div></div></body>';
			if (stristr($info['data'], '<body') === false) {
				$html = "<html>$beginHtml" . $tikilib->parse_data($info['data'], array('absolute_links' => true, 'suppress_icons' => true)) . "$endHtml</html>";
			} else {
				$html = str_ireplace('<body>', $beginHtml,$info['data']);
				$html = str_ireplace('</body>', $endHtml, $html);
			}

			if ($nl_info['allowArticleClip'] == 'y' && $nl_info['autoArticleClip'] == 'y') {
				$articleClip = $this->clip_articles($nl_info['nlId']);
				$txtArticleClip = generateTxtVersion($articleClip);
				$info['datatxt'] = str_replace('~~~articleclip~~~', $txtArticleClip, $info['datatxt']);
				$html = str_replace('~~~articleclip~~~', $articleClip, $html);
			}

			if (stristr($html, '<base') === false) {
				if (stristr($html, '<head') === false) {
					$news_cssfile = $tikilib->get_style_path($prefs['style'], '', 'newsletter.css');
					$news_cssfile_option = $tikilib->get_style_path($prefs['style'], $prefs['style_option'], 'newsletter.css');
					$news_css = '';
					if (!empty($news_cssfile)) {
						$news_css .= $headerlib->minify_css($news_cssfile);
					}
					if (!empty($news_cssfile_option) && $news_cssfile_option !== $news_cssfile) {
						$news_css .= $headerlib->minify_css($news_cssfile_option);
					}
					if (empty($news_css)) {
						$news_css = $headerlib->get_all_css_content();
					}
					$news_head = "<html><head><base href=\"$base_url\" /><style type=\"text/css\">$news_css</style></head>";
					$html = str_ireplace('<html>', $news_head, $html);
				} else {
					$html = str_ireplace('<head>', "<head><base href=\"$base_url\" />", $html);
				}
			}

			$info['files'] = $this->get_edition_files($editionId);

			include_once 'lib/mail/maillib.php';
			$zmail = tiki_get_admin_mail();

			if (!empty($info['replyto'])) {
				$zmail->setReplyTo($info['replyto']);
			}

			foreach($info['files'] as $f) {
				$fpath = isset($f['path']) ? $f['path'] : $prefs['tmpDir'] . '/newsletterfile-' . $f['filename'];
				$att = $zmail->createAttachment(file_get_contents($fpath));
				$att->filename = $f['name'];
				$att->mimeType = $f['type'];
			}

			$zmail->setSubject($info['subject']); // htmlMimeMail memorised the encoded subject

			$mailcache[$editionId] = array(
				'zmail' => $zmail,
				'text' => $info['datatxt'],
				'html' => $html,
				'unsubMsg' => $nl_info['unsubMsg'],
				'nlId' => $nl_info['nlId'],
			);
		}

		$cache = $mailcache[$editionId];

		$html = $cache['html'];
		$unsubmsg = '';
		if ($cache["unsubMsg"] == 'y' && !empty($target["code"])) {
			$unsubmsg = $this->get_unsub_msg($cache["nlId"], $target['email'], $target['language'], $target["code"], $target['user']);
			if (stristr($html, '</body>') === false) {
				$html .= $unsubmsg;
			} else {
				$html = str_replace("</body>", nl2br($unsubmsg) . "</body>", $html);
			}
		}

		$zmail = $cache['zmail'];
		$zmail->setBodyHtml($html);
		$zmail->setBodyText($cache['text'] . strip_tags($unsubmsg));
		$zmail->clearRecipients();
		$zmail->addTo($target['email']);

		return $zmail;
	}

	// info: subject, data, datatxt, dataparsed, wysiwyg, sendingUniqId, files, errorEditionId, editionId
	// browser: true if on the browser
	function send($nl_info, $info, $browser=true, &$sent, &$errors, &$logFileName) {
		global $prefs, $smarty;
		$headerlib = TikiLib::lib('header');
		$tikilib = TikiLib::lib('tiki');
		$userlib = TikiLib::lib('user');

		$users = $this->get_all_subscribers($nl_info['nlId'], $nl_info['unsubMsg'] == 'y');

		if (empty($info['editionId'])) {
			$info['editionId'] = $this->replace_edition($nl_info['nlId'], $info['subject'], $info['data'], 0, 0, true, $info['datatxt'], $info['files'], $info['wysiwyg']);
		} else {
			$this->replace_edition($nl_info['nlId'], $info['subject'], $info['data'], 0, $info['editionId'], true, $info['datatxt'], $info['files'], $info['wysiwyg']);
		}

		if (isset($info['begin'])) {
			$this->memo_subscribers_edition($info['editionId'], $users);
		}

		$remaining = $this->table('tiki_sent_newsletters_errors')->fetchColumn('email', array(
			'editionId' => $info['editionId'],
		));

		$sent = array();
		$errors = array();
		$toSend = array();
		foreach ($users as $uInfo) {
			$userEmail = $uInfo['login'];
			$email = trim($uInfo['email']);
			if ($userEmail == '') {
				$userEmail = $userlib->get_user_by_email($email);
			}
			$language = !$userEmail ? $prefs['site_language'] : $tikilib->get_user_preference($userEmail, "language", $prefs['site_language']);

			if (preg_match('/([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+/', $email)) {
				if (in_array($email, $remaining)) {
					$uInfo['user'] = $userEmail;
					$uInfo['email'] = $email;
					$uInfo['language'] = $language;

					$toSend[$email] = $uInfo;
				} else {
					$sent[] = $email;
				}
			} else {
				$errors[] = array("user" => $userEmail, "email" => $email, "msg" => tra("invalid email"));
			}
		}

		$users = array_values($toSend);

		$logFileName = $prefs['tmpDir'] . '/public/newsletter-log-' . $info['editionId'] . '.txt';
		if (($logFileHandle = fopen( $logFileName, 'a' )) == false) {
			$logFileName = '';
		}
	
		$smarty->assign('sectionClass', empty( $section ) ? '' : "tiki_$section " );
		if ($browser) {
			echo $smarty->fetch('send_newsletter_header.tpl');
		}

		if ($browser) {
			@ini_set('zlib.output_compression', 0);
		}

		$throttleLimit = (int) $prefs['newsletter_batch_size'];

		foreach ($users as $us) {
			$email = $us['email'];
			if ($browser) {
				if (@ob_get_level() == 0)
					@ob_start();
				// Browsers needs a certain amount of data, for each flush, to display something
				print str_repeat(' ', 4096) . "\n";
				print '<div class="confirmation">' . tra("Sending to") . "'<b>$email</b>': <font color=";
			}

			try {
				$zmail = $this->get_edition_mail($info['editionId'], $us);
				$zmail->send();
				$sent[] = $email;
				if ($browser) {
					print "'green'>" . tra('OK');
				}
				$this->delete_edition_subscriber($info['editionId'], $us);
				$logStatus = 'OK';
			} catch (Zend_Mail_Exception $e) {
				if ($browser) {
					print "'red'>" . tra('Error') . " - {$e->getMessage()}";
				}
				$errors[] = array("user" => $us['user'], "email" => $email, "msg" => $e->getMessage());
				$this->mark_edition_subscriber($info['editionId'], $us);
				$logStatus = 'Error';
			}

			if ( $logFileHandle ) {
				@fwrite( $logFileHandle, "$email : $logStatus\n" );
			}

			if ($browser) {
				print "</font></div>\n";

				// Flush output to force the browser to display email addresses as soon as emails are sent
				// This should avoid CGI and/or proxy and/or browser timeouts when sending to a lot of emails
				@ob_flush();
				@flush();
				@ob_end_flush();
			}

			if ($prefs['newsletter_throttle'] === 'y' && 0 >= --$throttleLimit) {
				$rate = (int) $prefs['newsletter_pause_length'];
				print '<div class="throttle" data-edition="' . $info['editionId'] . '" data-rate="' . $rate . '">' . tr('Limiting the email send rate. Resuming in %0 seconds.', $rate) . '</div>';
				exit;
			}
		}
		$info['editionId'] = $this->replace_edition($nl_info['nlId'], $info['subject'], $info['data'], count($sent), $info['editionId'], false, $info['datatxt'], $info['files'], $info['wysiwyg']);
		foreach($info['files'] as $k => $f) {
			if ($f['savestate'] == 'tikitemp') {
				$newpath = $prefs['tmpDir'] . '/newsletterfile-' . $f['filename'];
				rename($f['path'], $newpath);
				unlink($f['path'] . '.infos');
				$info['files'][$k]['savestate'] = 'tiki';
				$info['files'][$k]['path'] = $newpath;
			}
		}
		if ($logFileHandle) {
			@fclose( $logFileHandle );
		}
	}


}

$nllib = new NlLib;
