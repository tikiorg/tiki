<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

class ContactLib extends TikiLib {
	function ContactLib($db) {
		parent::TikiLib($db);
	}

	// Contacts
	function list_contacts($user, $offset, $maxRecords, $sort_mode, $find) {
		if ($find) {
			$findesc = '%' . $find . '%';
			$mid = " where `user`=? and (`nickname` like ? or `firstName` like ? or `lastName` like ? or `email` like ?)";
			$bindvars=array($user, $findesc, $findesc, $findesc, $findesc);
		} else {
			$mid = " where `user`=? ";
			$bindvars=array($user);
		}

		$query = "select * from `tiki_webmail_contacts` $mid order by ".$this->convert_sortmode($sort_mode);
		$query_cant = "select count(*) from `tiki_webmail_contacts` $mid";

		$result = $this->query($query, $bindvars, $maxRecords, $offset);
		$cant = $this->getOne($query_cant, $bindvars);
		$ret = array();

		while ($res = $result->fetchRow()) {
			$ret[] = $res;
		}
		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}

	function are_contacts($contacts, $user) {
		$ret = array();

		foreach ($contacts as $con) {
			$con = trim($con);

			$query = "select count(*) from `tiki_webmail_contacts` where `email`=?";
			$cant = $this->getOne($query, array($con));

			if (!$cant)
				$ret[] = $con;
		}
		return $ret;
	}

	function list_contacts_by_letter($user, $offset, $maxRecords, $sort_mode, $letter) {
		$letter .= '%';
		$mid = " where `user`=? and (`email` like ?)";
		$bindvars=array($user, $letter);

		$query = "select * from `tiki_webmail_contacts` $mid order by ".$this-convert_sortmode($sort_mode);
		$query_cant = "select count(*) from `tiki_webmail_contacts` $mid";
			
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

	function parse_nicknames($dirs) {
		for ($i = 0; $i < count($dirs); $i++) {
			if (!strstr($dirs[$i], '@') && !empty($dirs[$i])) {
				$query = "select `email` from `tiki_webmail_contacts` where `nickname`=?";
				$result = $this->query($query, array($dirs[$i]));
				if ($result->numRows()) {
					$res = $result->fetchRow();
					$dirs[$i] = $res["email"];
				}
			}
		}
		return $dirs;
	}

	function replace_contact($contactId, $firstName, $lastName, $email, $nickname, $user) {
		$firstName = trim($firstName);
		$lastName = trim($lastName);
		$email = trim($email);
		$nickname = trim($nickname);
		if ($contactId) {
			$query = "update `tiki_webmail_contacts` set `firstName`=?, `lastName`=?, `email`=?, `nickname`=? where `contactId`=? and `user`=?";
			$bindvars = array($firstName,$lastName,$email,$nickname,(int)$contactId,$user);
			$result = $this->query($query, $bindvars);
		} else {
		  $query = "delete from `tiki_webmail_contacts` where `contactId`=? and `user`=?"; 
		  $result = $this->query($query,array((int)$contactId, $user),-1,-1,false); //the false allows ignoring errors 
      $query = "insert into `tiki_webmail_contacts`(`firstName`,`lastName`,`email`,`nickname`,`user`) values(?,?,?,?,?)"; 
      $result = $this->query($query,array($firstName,$lastName,$email,$nickname,$user)); 
		}
		return true;
	}

	function remove_contact($contactId, $user) {
		$query = "delete from `tiki_webmail_contacts` where `contactId`=? and `user`=?";
		$result = $this->query($query, array((int)$contactId,$user));
		return true;
	}

	function get_contact($contactId, $user) {
		$query = "select * from `tiki_webmail_contacts` where `contactId`=? and `user`=?";
		$result = $this->query($query, array((int)$contactId,$user));
		if (!$result->numRows()) {
			return false;
		}
		$res = $result->fetchRow();
		return $res;
	}

}
$contactlib = new ContactLib($dbTiki);
?>
