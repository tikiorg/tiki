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

	function AddGroup ($ObjectType, $ObjectNumber,$GroupName,$displayEachUser="y" ) {
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

}
global $tikilib,$dbTiki;
$groupalertlib = new groupAlertLib($dbTiki);
?>