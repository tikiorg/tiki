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
			$query = "select `groupName` from `tiki_webmail_contacts_groups` where `contactId`=?";
			$res2 = $this->query($query,array((int)$res['contactId']));
			$ret2 = array();
			if ($res2) {
				while ($r2 = $res2->fetchRow()) {
					$res['groups'][] = $r2['groupName'];
				}
			} else {
				$res['groups'] = array();
			}
			$ret[] = $res;
		}

		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}


	function list_group_contacts($user, $offset, $maxRecords, $sort_mode, $find, $initial=false) {
		if ($find) {
			$findesc = '%' . $find . '%';
			$mid = " and (c.`nickname` like ? or c.`firstName` like ? or c.`lastName` like ? or c.`email` like ?)";
			$bindvars=array($findesc, $findesc, $findesc, $findesc);
		} else {
			$mid = "";
			$bindvars=array();
		}
		$groups = $this->get_user_groups($user);
		$count = 0;
		$back = array();
		foreach ($groups as $group) {
			$query = "select c.* from `tiki_webmail_contacts_groups` as a left join `tiki_webmail_contacts` as c on a.`contactId`=c.`contactId` where a.`groupName`=? $mid order by c.".$this->convert_sortmode($sort_mode);
			$query_cant = "select count(*) from `tiki_webmail_contacts_groups` as a left join `tiki_webmail_contacts` as c on a.`contactId`=c.`contactId` where a.`groupName`=? $mid";
			$bindv = $bindvars;
			array_unshift($bindv,$group);
			$result = $this->query($query, $bindv, $maxRecords, $offset);
			$cant = $this->getOne($query_cant, $bindv);
			$ret = array();
			while ($res = $result->fetchRow()) {
				$back[$group][] = $res;
			}
			$count = $count + $cant;
		}

		return array('data'=>$back,'cant'=>$count);
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

		$query = "select * from `tiki_webmail_contacts` $mid order by ".$this->convert_sortmode($sort_mode);
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
	
	function replace_contact($contactId, $firstName, $lastName, $email, $nickname, $user, $groups=array(), $exts=array()) {
		$firstName = trim($firstName);
		$lastName = trim($lastName);
		$email = trim($email);
		$nickname = trim($nickname);
		if ($contactId) {
			$query = "update `tiki_webmail_contacts` set `firstName`=?, `lastName`=?, `email`=?, `nickname`=? where `contactId`=? and `user`=?";
			$bindvars = array($firstName,$lastName,$email,$nickname,(int)$contactId,$user);
			$result = $this->query($query, $bindvars);
			$this->query('delete from `tiki_webmail_contacts_groups` where `contactId`=?',array((int)$contactId));
		} else {
			$query = "delete from `tiki_webmail_contacts` where `contactId`=? and `user`=?"; 
			$result = $this->query($query,array((int)$contactId, $user),-1,-1,false); //the false allows ignoring errors 
			$contactId = $this->getOne('select max(`contactId`) from `tiki_webmail_contacts`') + 1;
			$query = "insert into `tiki_webmail_contacts`(`contactId`,`firstName`,`lastName`,`email`,`nickname`,`user`) values(?,?,?,?,?,?)"; 
			$result = $this->query($query,array((int)$contactId,$firstName,$lastName,$email,$nickname,$user)); 
		}
		foreach ($groups as $group) {
			$this->query('insert into `tiki_webmail_contacts_groups` (`contactId`,`groupName`) values (?,?)',array((int)$contactId,$group));
		}
		foreach($exts as $ext => $value) {
			$this->query('delete from `tiki_webmail_contacts_ext` where `contactId`=? and `name`=?',
				     array((int)$contactId, $ext));
			if (strlen($value))
				$this->query('insert into `tiki_webmail_contacts_ext` (`contactId`,`name`,`value`) values (?,?,?)',
					     array((int)$contactId, $ext, $value));
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
		$query = "select `groupName` from `tiki_webmail_contacts_groups` where `contactId`=?";
		$res2 = $this->query($query,array((int)$res['contactId']));
		$ret2 = array();
		if ($res2) {
			while ($r2 = $res2->fetchRow()) {
				$res['groups'][] = $r2['groupName'];
			}
		}
		$res2=$this->query("select `name`,`value` from `tiki_webmail_contacts_ext` where `contactId`=?", array($contactId));
		while($r2 = $res2->fetchRow()) {
			$res['ext'][$r2['name']]=$r2['value'];
		}
		return $res;
	}
	
	function get_ext_list() {
		global $user;
		$exts=$this->get_user_preference($user, "contacts_exts", serialize(array('Phone','Mobile')));
		$exts=unserialize($exts);
 		return $exts;
	}
	
	function add_ext($name) {
		global $user;
		$exts=$this->get_ext_list();
		$exts[]=$name;
		$this->set_user_preference($user, "contacts_exts", serialize($exts));
	}
	
	function remove_ext($name) {
		global $user;
		$exts=$this->get_ext_list();
		foreach($exts as $k => $v)
			if ($v == $name) unset($exts[$k]);
		$this->set_user_preference($user, "contacts_exts", serialize($exts));
	}
	
	function rename_ext($oldname, $newname) {
		global $user;
		$exts=$this->get_ext_list();
		foreach($exts as $k => $v)
			if ($v == $oldname) $exts[$k]=$newname;
		$this->set_user_preference($user, "contacts_exts", serialize($exts));
	}
}
$contactlib = new ContactLib($dbTiki);
?>