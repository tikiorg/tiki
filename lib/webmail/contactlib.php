<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

class ContactLib extends TikiLib {

	function ContactLib($db) {
		$this->TikiLib($db);
	}

	// Contacts
	function list_contacts($user, $offset=-1, $maxRecords=-1,
			       $sort_mode='firstName_asc,lastName_asc,email_asc',
			       $find=NULL, $include_group_contacts = false, $letter = '', $letter_field = 'email') {

		if ( $include_group_contacts ) {
			$user_groups = "'".join("','", $this->get_user_groups($user))."'";
			$mid = "where (`user`=? or `groupName` IN ($user_groups)) and `$letter_field` like ?";
		} else $mid = "where `user`=? and `$letter_field` like ?";
		$bindvars=array($user, $letter.'%');
		
		if ($find !== NULL) {
			$findesc = '%' . $find . '%';
			$mid .= " and (`nickname` like ? or `firstName` like ? or `lastName` like ? or `email` like ?)";
			array_push($bindvars, $findesc, $findesc, $findesc, $findesc);
		}

		$query = "select c.* from `tiki_webmail_contacts` as c left join `tiki_webmail_contacts_groups` as a on a.`contactId`=c.`contactId` $mid group by contactId order by c.".$this->convert_sortmode($sort_mode);
		//$query = "select * from `tiki_webmail_contacts` $mid order by ".$this->convert_sortmode($sort_mode);

		$result = $this->query($query, $bindvars, $maxRecords, $offset);
		$ret = array();

		while ($res = $result->fetchRow()) {
			$query = "select `groupName` from `tiki_webmail_contacts_groups` where `contactId`=?";
			$res2 = $this->query($query,array((int)$res['contactId']));
			if ($res2) {
				while ($r2 = $res2->fetchRow()) $res['groups'][] = $r2['groupName'];
			} else {
				$res['groups'] = array();
			}
			$res2 = $this->query("select `fieldId`,`value` from `tiki_webmail_contacts_ext` where `contactId`=?", array((int)$res['contactId']));
			if ($res2) {
				while ($r2 = $res2->fetchRow()) $res['ext'][$r2['fieldId']]=$r2['value'];
			}
			$ret[] = $res;
		}

		return $ret;
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
		return $this->list_contacts($user, $offset, $maxRecords, $sort_mode, '', false, $letter);
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
			if ( $this->is_a_user_contact($contactId, $user, true) ) {
				$query = "update `tiki_webmail_contacts` set `firstName`=?, `lastName`=?, `email`=?, `nickname`=? where `contactId`=?";
				$bindvars = array($firstName,$lastName,$email,$nickname,(int)$contactId);
				$result = $this->query($query, $bindvars);
				$this->query('delete from `tiki_webmail_contacts_groups` where `contactId`=?',array((int)$contactId));
			} else return false;
		} else {
			$contactId = $this->getOne('select max(`contactId`) from `tiki_webmail_contacts`') + 1;
			$query = "insert into `tiki_webmail_contacts`(`contactId`,`firstName`,`lastName`,`email`,`nickname`,`user`) values(?,?,?,?,?,?)";
			$result = $this->query($query,array((int)$contactId,$firstName,$lastName,$email,$nickname,$user));
		}
		if (is_array($groups)) {
			foreach ($groups as $group) {
				$this->query('insert into `tiki_webmail_contacts_groups` (`contactId`,`groupName`) values (?,?)',array((int)$contactId,$group));
			}
		}
		
		$this->query('delete from `tiki_webmail_contacts_ext` where `contactId`=?', array((int)$contactId));
		foreach($exts as $fieldId => $ext) if ($fieldId > 0 && $ext != '') {
			$this->query('insert into `tiki_webmail_contacts_ext` (`contactId`,`fieldId`,`value`) values (?,?,?)',
				array((int)$contactId, $fieldId, $ext));
		}
		return true;
	}

	function is_a_user_contact($contactId, $user, $include_group_contacts = true) {
		if ( $contactId > 0 ) {
			$user_groups = "'".join("','", $this->get_user_groups($user))."'";
			$query = "select count(*) as res from `tiki_webmail_contacts` as c left join `tiki_webmail_contacts_groups` as a on a.`contactId`=c.`contactId` where c.`contactId`=? and (`user`=? or `groupName` IN ($user_groups))";
			$result = $this->query($query, array((int)$contactId,$user));
			if ( $result ) $r = $result->fetchRow();
			return ( $r['res'] > 0 );
		}
		return false;
	}

	function remove_contact($contactId, $user) {
		if ( $this->is_a_user_contact($contactId, $user, true) ) {
			$this->query('delete from `tiki_webmail_contacts` where `contactId`=?', array((int)$contactId));
			$this->query('delete from `tiki_webmail_contacts_groups` where `contactId`=?',array((int)$contactId));
			$this->query('delete from `tiki_webmail_contacts_ext` where `contactId`=?',array((int)$contactId));
			return true;
		}
		return false;
	}
	function get_contact_email($email, $user) {
		$result=$this->query("Select `contactId` from tiki_webmail_contacts where `email`=?",array($email));
		while ($res = $result->fetchRow()){
			if ($this->is_a_user_contact($res, $user, false)) {
				$contactId=$res;
			}
		}
		$info=$this->get_contact($contatId, $user);
		foreach($info['ext'] as $k => $v) {
	    		if (!in_array($k, array_keys($exts))) {
				$exts[$k]=$v;
				$traducted_exts[$k]['tra']=tra($info['fieldname']);
				$traducted_exts[$k]['art']=$info['fieldname'];
				$traducted_exts[$k]['id']=$k;
	    		}
		}
		return $info['ext'];
	}
	function get_contact($contactId, $user) {
		if ( $this->is_a_user_contact($contactId, $user, true) ) {
			$query = "select * from `tiki_webmail_contacts` where `contactId`=?";
			$result = $this->query($query, array((int)$contactId));
			if (!$result->numRows()) return false;
			$res = $result->fetchRow();
			$query = "select `groupName` from `tiki_webmail_contacts_groups` where `contactId`=?";
			$res2 = $this->query($query,array((int)$res['contactId']));
			$ret2 = array();
			if ($res2) while ($r2 = $res2->fetchRow()) $res['groups'][] = $r2['groupName'];
			$res2=$this->query("select `fieldId`,`value` from `tiki_webmail_contacts_ext` where `contactId`=?", array($contactId));
			if ($res2) while ($r2 = $res2->fetchRow()) $res['ext'][$r2['fieldId']]=$r2['value'];
			return $res;
		}
		return false;
	}
	
	// this function is never called, it is just for making get_strings.php happy, so that default fields in the next function will be in translation files
	function make_get_strings_happy() {
		tra('Personal Phone'); tra('Personal Mobile'); tra('Personal Fax'); tra('Work Phone'); tra('Work Mobile');
		tra('Work Fax'); tra('Company'); tra('Organization'); tra('Department'); tra('Division'); tra('Job Title');
		tra('Street Address'); tra('City'); tra('State'); tra('Zip Code'); tra('Country');
	}
	function get_ext_list($user) {
		global $user;
		$query = 'select * from `tiki_webmail_contacts_fields` where `user`=? order by `order`, `fieldname`';
		$bindvars = array($user);
		
		$res = $this->query($query, $bindvars);
		// default values if no user is specified or if user has no ext list
		if (!$res->numRows()) {
			$exts=array('Personal Phone', 'Personal Mobile', 'Personal Fax', 'Work Phone', 'Work Mobile',
				   'Work Fax', 'Company', 'Organization', 'Department', 'Division', 'Job Title',
				   'Street Address', 'City', 'State', 'Zip Code', 'Country');
			if (($user == NULL) || (empty($user))) return $exts;
			foreach($exts as $ext) $this->add_ext($user, $ext);
			$res = $this->query($query, $bindvars);
		}
		while ($row = $res->fetchRow()) $ret[] = $row;

 		return $ret;
	}
    
	function get_ext($id) {
		$this->query('select * from `tiki_webmail_contacts_fields` where `fieldId`=?', array((int)$id));
		if (!$res->numRows()) return NULL;
		return $res->fetchRow();
	}
	
	function add_ext($user, $name) {
		$this->query("insert into `tiki_webmail_contacts_fields` (`user`, `fieldname`) values (?,?)",
			     array($user, $name));
	}
	
	function remove_ext($user, $fieldId) {
		$this->query('delete from `tiki_webmail_contacts_fields` where `user`=? and `fieldId`=?',
			     array($user, $fieldId));
	}
	
	function rename_ext($user, $fieldId, $newname) {
		$this->query('update `tiki_webmail_contacts_fields` set `fieldname`=? where `fieldId`=? and `user`=?',
			     array($newname, $fieldId, $user));
	}

	function modify_ext($user, $fieldId, $new_values) {
		if ( is_array($new_values) ) {
			foreach ( $new_values as $f => $v ) {
				if ( $query != '' ) $query .= ', ';
				$query .= "`$f`=?";
				$bindvars[] = $v;
			}
			$query = "update `tiki_webmail_contacts_fields` set $query where `fieldId`=? and `user`=?";
			$bindvars[] = $fieldId;
			$bindvars[] = $user;
			$this->query($query, $bindvars);
		}
	}
}
$contactlib = new ContactLib($dbTiki);
?>
