<?php

include_once ('lib/notifications/notificationlib.php');

class TrackerLib extends TikiLib {
	function TrackerLib($db) {
		parent::TikiLib($db);
	}

	/* Tiki tracker construction options */
	// Return an array with items assigned to the user or a user group
	function list_tracker_items($trackerId, $offset, $maxRecords, $sort_mode, $fields, $status = '') {
		$filters = array();

		if ($fields) {
			for ($i = 0; $i < count($fields["data"]); $i++) {
				$fieldId = $fields["data"][$i]["fieldId"];

				$type = $fields["data"][$i]["type"];
				$value = $fields["data"][$i]["value"];
				$aux["value"] = $value;
				$aux["type"] = $type;
				$filters[$fieldId] = $aux;
			}
		}

		$mid = " where `trackerId`=? ";
		$bindvars=array((int) $trackerId);

		if ($status) {
			$mid .= " and `status`=? ";
			$bindvars[]=$status;
		}

		$query = "select * from `tiki_tracker_items` $mid order by ".$this->convert_sortmode($sort_mode);
		$query_cant = "select count(*) from `tiki_tracker_items` $mid";
		$result = $this->query($query,$bindvars,$maxRecords,$offset);
		$cant = $this->getOne($query_cant,$bindvars);
		$ret = array();

		while ($res = $result->fetchRow()) {
			$fields = array();

			$itid = $res["itemId"];
			$query2 = "select ttif.`fieldId`,`name`,`value`,`type`,`isTblVisible`,`isMain`,`position` 
				from `tiki_tracker_item_fields` ttif, `tiki_tracker_fields` ttf 
				where ttif.`fieldId`=ttf.`fieldId` and `itemId`=? order by `position` asc";
			$result2 = $this->query($query2,array((int) $res["itemId"]));
			$pass = true;

			while ($res2 = $result2->fetchRow()) {
				// Check if the field is visible!
				$fieldId = $res2["fieldId"];

				if (count($filters) > 0) {
					if ($filters["$fieldId"]["value"]) {
						if ($filters["$fieldId"]["type"] == 'a' || $filters["$fieldId"]["type"] == 't') {
							if (!strstr($res2["value"], $filters["$fieldId"]["value"]))
								$pass = false;
						} else {
							if ($res2["value"] != $filters["$fieldId"]["value"])
								$pass = false;
						}
					}
				}

				$fields[] = $res2;
			}

			$res["field_values"] = $fields;
			$res["comments"] = $this->getOne("select count(*) from `tiki_tracker_item_comments` where `itemId`=?",array((int) $itid));

			if ($pass)
				$ret[] = $res;
		}

		//$ret=$this->sort_items_by_condition($ret,$sort_mode);
		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}

	function add_item_attachment_hit($id) {
		global $count_admin_pvs;

		global $user;

		if ($count_admin_pvs == 'y' || $user != 'admin') {
			$query = "update `tiki_tracker_item_attachments` set `downloads`=`downloads`+1 where `attId`=?";

			$result = $this->query($query,array((int) $id));
		}

		return true;
	}

	function get_item_attachment_owner($attId) {
		return $this->getOne("select `user` from `tiki_tracker_item_attachments` where `attId`=?",array((int) $attId));
	}

	function list_item_attachments($itemId, $offset, $maxRecords, $sort_mode, $find) {

		if ($find) {
			$findesc = '%' . $find . '%';

			$mid = " where `itemId`=? and (`filename` like ?)";
			$bindvars=array((int) $itemId,$findesc);
		} else {
			$mid = " where `itemId`=? ";
			$bindvars=array((int) $itemId);
		}

		$query = "select `user`,`attId`,`itemId`,`filename`,`filesize`,`filetype`,`downloads`,`created`,`comment`,`longdesc`,`version` from `tiki_tracker_item_attachments` $mid order by ".$this->convert_sortmode($sort_mode);
		$query_cant = "select count(*) from `tiki_tracker_item_attachments` $mid";
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

	function item_attach_file($itemId, $name, $type, $size, $data, $comment, $user, $fhash, $version, $longdesc) {
		$comment = strip_tags($comment);
		$now = date("U");
		$query = "insert into `tiki_tracker_item_attachments`(`itemId`,`filename`,`filesize`,`filetype`,`data`,`created`,`downloads`,`user`,`comment`,`path`,`version`,`longdesc`)
    values(?,?,?,?,?,?,?,?,?,?,?,?)";
		$result = $this->query($query,array((int) $itemId,$name,$size,$type,$data,(int) $now,0,$user,$comment,$fhash,$version,$longdesc));
	}

	function get_item_attachment($attId) {
		$query = "select * from `tiki_tracker_item_attachments` where `attId`=?";

		$result = $this->query($query,array((int) $attId));

		if (!$result->numRows())
			return false;

		$res = $result->fetchRow();
		return $res;
	}

	function remove_item_attachment($attId) {
		global $t_use_dir;

		$path = $this->getOne("select `path` from `tiki_tracker_item_attachments` where `attId`=?",array((int) $attId));

		if ($path) {
			@unlink ($t_use_dir . $path);
		}

		$query = "delete from `tiki_tracker_item_attachments` where `attId`=?";
		$result = $this->query($query,array((int) $attId));
	}

	function replace_item_comment($commentId, $itemId, $title, $data, $user) {
		global $smarty;

		global $notificationlib;
		global $sender_email;
		$title = strip_tags($title);
		$data = strip_tags($data, "<a>");

		if ($commentId) {
			$query = "update `tiki_tracker_item_comments` set `title`=?, `data`=? , `user`=? where `commentId`=?";

			$result = $this->query($query,array($title,$data,$user,(int) $commentId));
		} else {
			$now = date("U");

			$query = "insert into `tiki_tracker_item_comments`(`itemId`,`title`,`data`,`user`,`posted`) values (?,?,?,?,?)";
			$result = $this->query($query,array((int) $itemId,$title,$data,$user,(int) $now));
			$commentId
				= $this->getOne("select max(`commentId`) from `tiki_tracker_item_comments` where `posted`=? and `title`=? and `itemId`=?",array((int) $now,$title,(int)$itemId));
		}

		$trackerId = $this->getOne("select `trackerId` from `tiki_tracker_items` where `itemId`=?",array((int) $itemId));
		$trackerName = $this->getOne("select `name` from `tiki_trackers` where `trackerId`=?",array((int) $trackerId));
		$emails = $notificationlib->get_mail_events('tracker_modified', $trackerId);
		$emails2 = $notificationlib->get_mail_events('tracker_item_modified', $itemId);
		$emails = array_merge($emails, $emails2);
		$smarty->assign('mail_date', date("U"));
		$smarty->assign('mail_user', $user);
		$smarty->assign('mail_action', 'New comment added for item:' . $itemId . ' at tracker ' . $trackerName);
		$smarty->assign('mail_data', $title . "\n\n" . $data);

		foreach ($emails as $email) {
			$mail_data = $smarty->fetch('mail/tracker_changed_notification.tpl');

			@mail($email, tra('Tracker was modified at '). $_SERVER["SERVER_NAME"], $mail_data,
				"From: $sender_email\r\nContent-type: text/plain;charset=utf-8\r\n");
		}

		return $commentId;
	}

	function remove_item_comment($commentId) {
		$query = "delete from `tiki_tracker_item_comments` where `commentId`=?";

		$result = $this->query($query,array((int) $commentId));
	}

	function list_item_comments($itemId, $offset, $maxRecords, $sort_mode, $find) {

		if ($find) {
			$findesc = '%' . $find . '%';

			$mid = " and (`title` like ? or `data` like ?)";
			$bindvars = array((int) $itemId,$findesc,$findesc);
		} else {
			$mid = "";
			$bindvars = array((int) $itemId);
		}

		$query = "select * from `tiki_tracker_item_comments` where `itemId`=? $mid order by ".$this->convert_sortmode($sort_mode);
		$query_cant = "select count(*) from `tiki_tracker_item_comments` where `itemId`=? $mid";
		$result = $this->query($query,$bindvars,$maxRecords,$offset);
		$cant = $this->getOne($query_cant,$bindvars);
		$ret = array();

		while ($res = $result->fetchRow()) {
			$res["parsed"] = nl2br($res["data"]);

			$ret[] = $res;
		}

		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}

	function get_item_comment($commentId) {
		$query = "select * from `tiki_tracker_item_comments` where `commentId`=?";

		$result = $this->query($query,array((int) $commentId));

		if (!$result->numRows())
			return false;

		$res = $result->fetchRow();
		return $res;
	}

	function get_last_position($id) {
		return $this->getOne("select max(`position`) from `tiki_tracker_fields` where `trackerId` = ?",array((int)$id));
	}

	function list_all_tracker_items($offset, $maxRecords, $sort_mode, $fields) {
		$filters = array();

		for ($i = 0; $i < count($fields["data"]); $i++) {
			$fieldId = $fields["data"][$i]["fieldId"];

			$type = $fields["data"][$i]["type"];
			$value = $fields["data"][$i]["value"];
			$aux["value"] = $value;
			$aux["type"] = $type;
			$filters[$fieldId] = $aux;
		}

		$mid = '';
		$bindvars=array();
		$query = "select * from `tiki_tracker_items` $mid order by ".$this->convert_sortmode($sort_mode);
		$query_cant = "select count(*) from `tiki_tracker_items` $mid";
		$result = $this->query($query,$bindvars,$maxRecords,$offset);
		$cant = $this->getOne($query_cant,$bindvars);
		$ret = array();

		while ($res = $result->fetchRow()) {
			$fields = array();

			$itid = $res["itemId"];
			$query2 = "select ttif.`fieldId`,`value`,`isTblVisible`,`isMain`,`position` from `tiki_tracker_item_fields` ttif,
			`tiki_tracker_fields` ttf where ttif.`fieldId`=ttf.`fieldId` and `itemId`=? order by `position` asc";
			$result2 = $this->query($query2,array((int) $res["itemId"]));
			$pass = true;

			while ($res2 = $result2->fetchRow()) {
				// Check if the field is visible!
				$fieldId = $res2["fieldId"];

				if ($filters["$fieldId"]["value"]) {
					if ($filters["$fieldId"]["type"] == 'a' || $filters["$fieldId"]["type"] == 't') {
						if (!strstr($res2["value"], $filters["$fieldId"]["value"]))
							$pass = false;
					} else {
						if ($res2["value"] != $filters["$fieldId"]["value"])
							$pass = false;
					}
				}

				$fields[] = $res2;
			}

			$res["field_values"] = $fields;
			$res["comments"] = $this->getOne("select count(*) from `tiki_tracker_item_comments` where `itemId`=?",array((int) $itid));

			if ($pass)
				$ret[] = $res;
		}

		//$ret=$this->sort_items_by_condition($ret,$sort_mode);
		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}

	function get_tracker_item($itemid) {
		$query = "select * from `tiki_tracker_items` where `itemid`=?";

		$result = $this->query($query,array((int) $itemid));

		if (!$result->numrows())
			return false;

		$res = $result->fetchrow();
		$query = "select * from `tiki_tracker_item_fields` ttif, `tiki_tracker_fields` ttf where ttif.`fieldid`=ttf.`fieldid` and `itemid`=?";
		$result = $this->query($query,array((int) $itemid));
		$fields = array();

		while ($res2 = $result->fetchrow()) {
			$name = ereg_replace("[^a-zA-Z0-9]","",$res2["name"]);
			$res["$name"] = $res2["value"];
		}

		return $res;
	}

	function get_item($trackerId,$field,$value) {
		$query = "select distinct ttif.`itemid` from `tiki_tracker_items` tti, `tiki_tracker_fields` ttf, `tiki_tracker_item_fields` ttif ";
		$query.= " where tti.`trackerId`=ttf.`trackerId` and ttif.`fieldId`=ttf.`fieldId` and ttf.`trackerId`=? and ttf.`name`=? and ttif.`value`=?";
		$itemId = $this->getOne($query,array((int) $trackerId,$field,$value));
		return $this->get_tracker_item($itemId);
	}

	function replace_item($trackerId, $itemId, $ins_fields, $status = 'o') {
		global $user;

		global $smarty;
		global $notificationlib;
		global $sender_email;
		$now = date("U");
		if ($itemId) {
			$query = "update `tiki_tracker_items` set `status`=?,`lastModif`=? where `itemId`=?";
			$result = $this->query($query,array($status,(int) $now,(int) $itemId));
		} else {
			$query = "insert into `tiki_tracker_items`(`trackerId`,`created`,`lastModif`,`status`) values(?,?,?,?)";
			$result = $this->query($query,array((int) $trackerId,(int) $now,(int) $now,$status));
			$new_itemId = $this->getOne("select max(`itemId`) from `tiki_tracker_items` where `created`=? and `trackerId`=?",array((int) $now,(int) $trackerId));
		}
		$the_data = '';

		for ($i = 0; $i < count($ins_fields["data"]); $i++) {
			$fieldId = $ins_fields["data"][$i]["fieldId"];
			$value = $ins_fields["data"][$i]["value"];
			if (isset($ins_fields["data"][$i]["name"])) {
				$name = $ins_fields["data"][$i]["name"];
			} else {
				$name = $this->getOne("select `name` from `tiki_tracker_fields` where `fieldId`=?",array((int)$fieldId));
			}
			$the_data .= "$name = $value\n";

			if ($itemId) {
				$query = "update `tiki_tracker_item_fields` set `value`=? where `itemId`=? and `fieldId`=?";
				$this->query($query,array($value,(int) $itemId,(int) $fieldId));
			} else {
				$query = "insert into `tiki_tracker_item_fields`(`itemId`,`fieldId`,`value`) values(?,?,?)";
				$this->query($query,array((int) $new_itemId,(int) $fieldId,$value));
			}
		}
		include_once('lib/notifications/notificationlib.php');	
		$emails = $notificationlib->get_mail_events('tracker_modified', $trackerId);
		$emails2 = $notificationlib->get_mail_events('tracker_item_modified', $itemId);
		$emails = array_merge($emails, $emails2);
		if (count($emails) > 0) {
			$trackerName = $this->getOne("select `name` from `tiki_trackers` where `trackerId`=?",array((int) $trackerId));
			$smarty->assign('mail_date', $now);
			$smarty->assign('mail_user', $user);
			if ($itemId) {
				$smarty->assign('mail_action', tra('Modification of item $itemId in tracker $trackerName'));
			} else {
				$smarty->assign('mail_action', tra('New item $itemId in tracker $trackerName'));
			}
			$smarty->assign('mail_data', $the_data);
			foreach ($emails as $email) {
				$mail_data = $smarty->fetch('mail/tracker_changed_notification.tpl');
				@mail($email, tra('Tracker was modified at '). $_SERVER["SERVER_NAME"], $mail_data,
					"From: $sender_email\r\nContent-type: text/plain;charset=utf-8\r\n");
			}
		}
		$cant_items = $this->getOne("select count(*) from `tiki_tracker_items` where `trackerId`=?",array((int) $trackerId));
		$query = "update `tiki_trackers` set `items`=?,`lastModif`=?  where `trackerId`=?";
		$result = $this->query($query,array((int)$cant_items,(int) $now,(int) $trackerId));

		if (!$itemId) $itemId = $new_itemId;
		return $itemId;
	}

	function remove_tracker_item($itemId) {
		$now = date("U");

		$trackerId = $this->getOne("select `trackerId` from `tiki_tracker_items` where `itemId`=?",array((int) $itemId));
		$query = "update `tiki_trackers` set `lastModif`=? where `trackerId`=?";
		$result = $this->query($query,array((int) $now,(int) $trackerId));
		$query = "update `tiki_trackers` set `items`=`items`-1 where `trackerId`=?";
		$result = $this->query($query,array((int) $trackerId));
		$query = "delete from `tiki_tracker_item_fields` where `itemId`=?";
		$result = $this->query($query,array((int) $itemId));
		$query = "delete from `tiki_tracker_items` where `itemId`=?";
		$result = $this->query($query,array((int) $itemId));
		$query = "delete from `tiki_tracker_item_comments` where `itemId`=?";
		$result = $this->query($query,array((int) $itemId));
	}

	// List the available trackers
	
	// Lists all the fields for an existing tracker
	function list_tracker_fields($trackerId, $offset, $maxRecords, $sort_mode, $find) {

		if ($find) {
			$findesc = '%' . $find . '%';

			$mid = " where `trackerId`=? and (`name` like ?)";
			$bindvars=array((int) $trackerId,$findesc);
		} else {
			$mid = " where `trackerId`=? ";
			$bindvars=array((int) $trackerId);
		}

		$query = "select * from `tiki_tracker_fields` $mid order by ".$this->convert_sortmode($sort_mode);
		$query_cant = "select count(*) from `tiki_tracker_fields` $mid";
		$result = $this->query($query,$bindvars,$maxRecords,$offset);
		$cant = $this->getOne($query_cant,$bindvars);
		$ret = array();

		while ($res = $result->fetchRow()) {
			$res["options_array"] = split(',', $res["options"]);
			$res["label"] = $res["name"];
			$res["name"] = ereg_replace("[^a-zA-Z0-9]","",$res["name"]);
			$ret[] = $res;
		}

		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}

				// Inserts or updates a tracker  
	function replace_tracker($trackerId, $name, $description, $showCreated, $showLastModif, $useComments, $useAttachments, $showStatus, $showComments, $showAttachments, $orderattachments) {

		if ($trackerId) {
			$query = "update `tiki_trackers` set `name`=?,`description`=?, `useAttachments`=?,`showAttachments`=?,
				`useComments`=?, `showComments`=?,`showCreated`=?,`showLastModif`=?,`showStatus`=?, `orderAttachments`=? 
				where `trackerId`=?";

			$bindvars=array($name,$description,$useAttachments,$showAttachments,$useComments,$showComments,$showCreated,
				$showLastModif,$showStatus,$orderattachments,(int) $trackerId);

			$result = $this->query($query,$bindvars);
		} else {
			$now = date("U");
			
			$this->getOne("delete from `tiki_trackers` where `name`=?",array($name),false);
			$query = "insert into `tiki_trackers`(`name`,`description`,`created`,`lastModif`,
				`items`,`showCreated`,`showLastModif`,`useComments`,`showComments`,`useAttachments`,`showAttachments`,`showStatus`,`orderAttachments`)
                		values(?,?,?,?,?,?,?,?,?,?,?,?,?)";
			$bindvars=array($name,$description,(int) $now,(int) $now,0,$showCreated,$showLastModif,
				$useComments,$showComments,$useAttachments,$showAttachments,$showStatus,$orderattachments);
			$result = $this->query($query,$bindvars);
			$trackerId = $this->getOne("select max(`trackerId`) from `tiki_trackers` where `name`=? and `created`=?",array($name,(int) $now));
		}

		return $trackerId;
	}

	// Adds a new field to a tracker or modifies an existing field for a tracker
	function replace_tracker_field($trackerId, $fieldId, $name, $type, $isMain, $isSearchable, $isTblVisible, $position, $options) {
		// Check the name
		if ($fieldId) {
			$query = "update `tiki_tracker_fields` set `name`=? ,`type`=?,`isMain`=?,`isSearchable`=?,
				`isTblVisible`=?,`position`=?,`options`=? where `fieldId`=?";
			$bindvars=array($name,$type,$isMain,$isSearchable,$isTblVisible,(int)$position,$options,(int) $fieldId);

			$result = $this->query($query, $bindvars);
		} else {
			$this->getOne("delete from `tiki_tracker_fields` where `trackerId`=? and `name`=?",
				array((int) $trackerId,$name),false);
			$query = "insert into `tiki_tracker_fields`(`trackerId`,`name`,`type`,`isMain`,`isSearchable`,`isTblVisible`,`position`,`options`)
                values(?,?,?,?,?,?,?,?)";

			$result = $this->query($query,array((int) $trackerId,$name,$type,$isMain,$isSearchable,$isTblVisible,$position,$options));
			$fieldId = $this->getOne("select max(`fieldId`) from `tiki_tracker_fields` where `trackerId`=? and `name`=?",array((int) $trackerId,$name));
			// Now add the field to all the existing items
			$query = "select `itemId` from `tiki_tracker_items` where `trackerId`=?";
			$result = $this->query($query,array((int) $trackerId));

			while ($res = $result->fetchRow()) {
				$itemId = $res['itemId'];
				$this->getOne("delete from `tiki_tracker_item_fields` where `itemId`=? and `fieldId`=?",
					array((int) $itemId,(int) $fieldId),false);

				$query2 = "insert into `tiki_tracker_item_fields`(`itemId`,`fieldId`,`value`) values(?,?,?)";
				$this->query($query2,array((int) $itemId,(int) $fieldId,''));
			}
		}

		return $fieldId;
	}

	function remove_tracker($trackerId) {
		// Remove the tracker
		$bindvars=array((int) $trackerId);
		$query = "delete from `tiki_trackers` where `trackerId`=?";

		$result = $this->query($query,$bindvars);
		// Remove the fields
		$query = "delete from `tiki_tracker_fields` where `trackerId`=?";
		$result = $this->query($query,$bindvars);
		// Remove the items (Remove fields for each item for this tracker)
		$query = "select `itemId` from `tiki_tracker_items` where `trackerId`=?";
		$result = $this->query($query,$bindvars);

		while ($res = $result->fetchRow()) {
			$query2 = "delete from `tiki_tracker_item_fields` where `itemId`=?";

			$result2 = $this->query($query2,array((int) $res["itemId"]));
			$query2 = "delete from `tiki_tracker_item_comments` where `itemId`=?";
			$result2 = $this->query($query2,array((int) $res["itemId"]));
		}

		$query = "delete from `tiki_tracker_items` where `trackerId`=?";
		$result = $this->query($query,$bindvars);
		$this->remove_object('tracker', $trackerId);
		return true;
	}

	function remove_tracker_field($fieldId) {
		$query = "delete from `tiki_tracker_fields` where `fieldId`=?";
		$bindvars=array((int) $fieldId);
		$result = $this->query($query,$bindvars);
		$query = "delete from `tiki_tracker_item_fields` where `fieldId`=?";
		$result = $this->query($query,$bindvars);
		return true;
	}

	

	function get_tracker_field($fieldId) {
		$query = "select * from `tiki_tracker_fields` where `fieldId`=?";

		$result = $this->query($query,array((int) $fieldId));

		if (!$result->numRows())
			return false;

		$res = $result->fetchRow();
		return $res;
	}

/*
** function only used for the popup for more infos on attachements
*  returns an array with field=>value
*/
	function get_moreinfo($attId) {
		$query = "select `orderAttachments`, t.`trackerId` from `tiki_trackers` t ";
		$query.= " left join `tiki_tracker_items` i on t.`trackerId`=i.`trackerId` ";
		$query.= " left join `tiki_tracker_item_attachments` a on i.`itemId`=a.`itemId` ";
		$query.= " where a.`attId`=? ";
		$result = $this->query($query,array((int)$attId));
		$resu = $result->fetchRow();
		if (strstr($resu['orderAttachments'],'|')) {
			$fields = split(',',substr($resu['orderAttachments'],strpos($resu['orderAttachments'],'|')+1));
			$query = "select `".implode("`,`",$fields)."` from `tiki_tracker_item_attachments` where `attId`=?";
			$result = $this->query($query,array((int)$attId));
			$res = $result->fetchRow();
			$res["trackerId"] = $resu['trackerId'];
			$res["longdesc"] = $this->parse_data($res['longdesc']);
		} else {
			$res = array(tra("message") => tra("No extra information for that attached file. "));
			$res['trackerId'] = 0;
		}
		return $res;
	}

	function field_types() {
		$type['c'] = array('label'=>tra('checkbox'),      'opt'=>false);
		$type['t'] = array('label'=>tra('text field'),    'opt'=>false);
		$type['a'] = array('label'=>tra('textarea'),      'opt'=>false);
		$type['d'] = array('label'=>tra('drop down'),     'opt'=>true,  'help'=>tra('Dropdown options : list of items separated with commas') );
		$type['u'] = array('label'=>tra('user selector'), 'opt'=>false);
		$type['g'] = array('label'=>tra('group selector'),'opt'=>false);
		$type['f'] = array('label'=>tra('date and time'), 'opt'=>false);
		$type['j'] = array('label'=>tra('jscalendar'),    'opt'=>false);
		$type['i'] = array('label'=>tra('image'),         'opt'=>true, 'help'=>tra('Image options : xSize,ySize indicated in pixels')  );
		$type['x'] = array('label'=>tra('action'),        'opt'=>true, 'help'=>tra('Action options : Label,post,tiki-index.php,page:fieldname,highlight=test') );
		$type['h'] = array('label'=>tra('header'),        'opt'=>false);
		$type['e'] = array('label'=>tra('category'),      'opt'=>true, 'help'=>tra('Category options : parentId') );
		$type['r'] = array('label'=>tra('tracker item'),  'opt'=>true, 'help'=>tra('Tracker options : trackerId,fieldname') );
		return $type;
	}
	
}

$trklib = new TrackerLib($dbTiki);

?>
