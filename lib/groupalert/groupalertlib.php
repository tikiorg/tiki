<?php
/*
 groupalert is used to select user of groups to send alert email (groupware notification)
*/

if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

class groupAlertLib extends TikiLib {

	function GroupAlertLib($db) {
		parent::TikiLib($db);
	}

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

	function Notify ( $Object ,$ObjectId ,$ListOfChecked ,$URL,$Message ) {
		foreach ( $ListOfChecked as $user ){
		$email=$userlib->get_user_email($user);
		if ( ! empty($email) ){
		include_once ('lib/webmail/tikimaillib.php');
		$mail = new TikiMail();
		$mail->setText($Message + "\n" + tra ("You can check the modifications at :")+$URL);
		$mail->setSubject("You are alerted of a changement" );
		$mail->send(array($email));
		}
	}
	}

}


global $tikilib,$dbTiki;
$groupalertlib = new groupAlertLib($dbTiki);
?>