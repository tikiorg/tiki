<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/*
 groupalert is used to select user of groups to send alert email (groupware notification)
*/

if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

class groupAlertLib extends TikiLib
{

	function AddGroup ($ObjectType, $ObjectNumber,$GroupName,$displayEachUser) {
		if ( $displayEachUser == "on" ) $displayEachUser='y';
		if ( $displayEachUser == "" ) $displayEachUser='n';

		$query ="delete from `tiki_groupalert` where ( `objectType`= ? and `objectId` = ?) ";
		$this->query($query,array($ObjectType,$ObjectNumber));
		if ( $GroupName != '' ) {
			$query = "insert into `tiki_groupalert` ( `groupName`,`objectType`,`objectId`,`displayEachuser` )  values (?,?,?,?)";
			$this->query($query,array($GroupName,$ObjectType,$ObjectNumber,$displayEachUser));
		}
	 return true;
	}

	function GetGroup ($ObjectType,$ObjectNumber) {
		$res= $this->getOne( "select `groupName` from `tiki_groupalert` where ( `objectType` = ? and `objectId` = ? )", array($ObjectType,$ObjectNumber));
		return $res ;
	}

	function GetShowEachUser($ObjectType,$ObjectNumber,$GroupName ) {
		return $this->getOne( "select `displayEachuser` from `tiki_groupalert` where ( `objectType` = ? and `objectId` = ? and `groupName` =? )", array($ObjectType,$ObjectNumber,$GroupName));
	}

	function Notify ( $ListUserToAlert,$URI ) {
		global $tikilib,$userlib ;
		if (!is_array($ListUserToAlert)) {
			return;
		}
		$project=$tikilib->get_preference ("browsertitle");
		$foo = parse_url($_SERVER["REQUEST_URI"]);
		$machine = $tikilib->httpPrefix( true ) . dirname( $foo["path"] );
		$URL=$machine."/".$URI;
		foreach ( $ListUserToAlert as $user ){
		$email=$userlib->get_user_email($user);
		if ( ! empty($email) ){
		include_once ('lib/webmail/tikimaillib.php'); 
		$mail = new TikiMail();
		$mail->setText(tra("You are alerted by the server "). $project . "\n" . tra ("You can check the modifications at : ") .$URL );
		$mail->setSubject(tra("You are alerted of a change on ").$project );
		$mail->send(array($email));
		}
	}
	}
}
global $groupalertlib;
$groupalertlib = new groupAlertLib;
