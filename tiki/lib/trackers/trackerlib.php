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
			$query2 = "select ttif.`fieldId`,`name`,`value`,`type`,`isTblVisible`,`isMain` 
				from `tiki_tracker_item_fields` ttif, `tiki_tracker_fields` ttf 
				where ttif.`fieldId`=ttf.`fieldId` and `itemId`=? order by `fieldId` asc";
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

		$query = "select `user`,`attId`,`itemId`,`filename`,`filesize`,`filetype`,`downloads`,`created`,`comment` from `tiki_tracker_item_attachments` $mid order by ".$this->convert_sortmode($sort_mode);
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

	function item_attach_file($itemId, $name, $type, $size, $data, $comment, $user, $fhash) {
		$comment = strip_tags($comment);
		$now = date("U");
		$query = "insert into `tiki_tracker_item_attachments`(`itemId`,`filename`,`filesize`,`filetype`,`data`,`created`,`downloads`,`user`,`comment`,`path`)
    values(?,?,?,?,?,?,?,?,?,?)";
		$result = $this->query($query,array((int) $itemId,$name,$size,$type,$data,(int) $now,0,$user,$comment,$fhash));
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

	//TODO DB abstraction continue here:
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

		$sort_mode = str_replace("_", " ", $sort_mode);
		$mid = '';
		$query = "select * from `tiki_tracker_items` $mid order by $sort_mode limit $offset,$maxRecords";
		$query_cant = "select count(*) from `tiki_tracker_items` $mid";
		$result = $this->query($query);
		$cant = $this->getOne($query_cant);
		$ret = array();

		while ($res = $result->fetchRow()) {
			$fields = array();

			$itid = $res["itemId"];
			$query2 = "select ttif.fieldId,value,isTblVisible,isMain from `tiki_tracker_item_fields` ttif, tiki_tracker_fields ttf where ttif.fieldId=ttf.fieldId and itemId=" . $res["itemId"] . " order by fieldId asc";
			$result2 = $this->query($query2);
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
			$res["comments"] = $this->getOne("select count(*) from `tiki_tracker_item_comments` where `itemId`=$itid");

			if ($pass)
				$ret[] = $res;
		}

		//$ret=$this->sort_items_by_condition($ret,$sort_mode);
		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}

	function get_tracker_item($itemId) {
		$query = "select * from `tiki_tracker_items` where `itemId`=$itemId";

		$result = $this->query($query);

		if (!$result->numRows())
			return false;

		$res = $result->fetchRow();
		$query = "select * from `tiki_tracker_item_fields` ttif, tiki_tracker_fields ttf where `ttif`.fieldId=ttf.fieldId and itemId=$itemId";
		$result = $this->query($query);
		$fields = array();

		while ($res2 = $result->fetchRow()) {
			$name = $res2["name"];

			$res["$name"] = $res2["value"];
		}

		return $res;
	}

	function replace_item($trackerId, $itemId, $ins_fields, $status = 'o') {
		global $user;

		global $smarty;
		global $notificationlib;
		global $sender_email;
		$now = date("U");
		$query = "update `tiki_trackers` set `lastModif`=$now where `trackerId`=$trackerId";
		$result = $this->query($query);

		if ($itemId) {
			$query = "update `tiki_tracker_items` set `status`='$status',lastModif=$now where `itemId`=$itemId";

			$result = $this->query($query);
		} else {
			$query = "replace into tiki_tracker_items(trackerId,created,lastModif,status) values($trackerId,$now,$now,'$status')";

			$result = $this->query($query);
			$new_itemId = $this->getOne("select max(itemId) from `tiki_tracker_items` where `created`=$now and trackerId=$trackerId");
		}

		$the_data = '';

		for ($i = 0; $i < count($ins_fields["data"]); $i++) {
			$name = $ins_fields["data"][$i]["name"];

			$fieldId = $ins_fields["data"][$i]["fieldId"];
			$value = addslashes($ins_fields["data"][$i]["value"]);
			// Now check if the item is 0 or not
			$the_data .= "$name = $value\n";

			if ($itemId) {
				$query = "update `tiki_tracker_item_fields` set `value`='$value' where `itemId`=$itemId and fieldId=$fieldId";

				$result = $this->query($query);
			} else {
				// We add an item
				$query = "replace into tiki_tracker_item_fields(itemId,fieldId,value) values($new_itemId,$fieldId,'$value')";

				$result = $this->query($query);
			}
		}

		$trackerName = $this->getOne("select `name` from `tiki_trackers` where `trackerId`=$trackerId");
		$emails = $notificationlib->get_mail_events('tracker_modified', $trackerId);
		$emails2 = $notificationlib->get_mail_events('tracker_item_modified', $itemId);
		$emails = array_merge($emails, $emails2);
		$smarty->assign('mail_date', date("U"));
		$smarty->assign('mail_user', $user);
		$smarty->assign('mail_action', 'New item added or modified:' . $itemId . ' at tracker ' . $trackerName);
		$smarty->assign('mail_data', $the_data);

		foreach ($emails as $email) {
			$mail_data = $smarty->fetch('mail/tracker_changed_notification.tpl');

			@mail($email, tra('Tracker was modified at '). $_SERVER["SERVER_NAME"], $mail_data,
				"From: $sender_email\r\nContent-type: text/plain;charset=utf-8\r\n");
		}

		$cant_items = $this->getOne("select count(*) from `tiki_tracker_items` where `trackerId`=$trackerId");
		$query = "update `tiki_trackers` set `items`=$cant_items where `trackerId`=$trackerId";
		$result = $this->query($query);

		if (!$itemId)
			$itemId = $new_itemId;

		return $itemId;
	}

	function remove_tracker_item($itemId) {
		$now = date("U");

		$trackerId = $this->getOne("select `trackerId` from `tiki_tracker_items` where `itemId`=$itemId");
		$query = "update `tiki_trackers` set `lastModif`=$now where `trackerId`=$trackerId";
		$result = $this->query($query);
		$query = "update `tiki_trackers` set `items`=items-1 where `trackerId`=$trackerId";
		$result = $this->query($query);
		$query = "delete from `tiki_tracker_item_fields` where `itemId`=$itemId";
		$result = $this->query($query);
		$query = "delete from `tiki_tracker_items` where `itemId`=$itemId";
		$result = $this->query($query);
		$query = "delete from `tiki_tracker_item_comments` where `itemId`=$itemId";
		$result = $this->query($query);
	}

	// List the available trackers
	function list_trackers($offset, $maxRecords, $sort_mode, $find) {
		$sort_mode = str_replace("_", " ", $sort_mode);

		if ($find) {
			$findesc = $this->qstr('%' . $find . '%');

			$mid = " where (name like $findesc or description like $findesc)";
		} else {
			$mid = "";
		}

		$query = "select * from `tiki_trackers` $mid order by $sort_mode limit $offset,$maxRecords";
		$query_cant = "select count(*) from `tiki_trackers` $mid";
		$result = $this->query($query);
		$cant = $this->getOne($query_cant);
		$ret = array();

		while ($res = $result->fetchRow()) {
			// Tracker fields are automatically counted when adding/removing fields to trackers
			$ret[] = $res;
		}

		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}

	// Lists all the fields for an existing tracker
	function list_tracker_fields($trackerId, $offset, $maxRecords, $sort_mode, $find) {
		$sort_mode = str_replace("_", " ", $sort_mode);

		if ($find) {
			$findesc = $this->qstr('%' . $find . '%');

			$mid = " where `trackerId`=$trackerId and (name like $findesc)";
		} else {
			$mid = " where `trackerId`=$trackerId ";
		}

		$query = "select * from `tiki_tracker_fields` $mid order by $sort_mode limit $offset,$maxRecords";
		$query_cant = "select count(*) from `tiki_tracker_fields` $mid";
		$result = $this->query($query);
		$cant = $this->getOne($query_cant);
		$ret = array();

		while ($res = $result->fetchRow()) {
			$res["options_array"] = split(',', $res["options"]);

			$ret[] = $res;
		}

		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}

	// Inserts or updates a tracker  
	function replace_tracker($trackerId, $name, $description, $showCreated, $showLastModif, $useComments, $useAttachments, $showStatus) {
		$description = addslashes($description);

		$name = addslashes($name);

		if ($trackerId) {
			$query = "update `tiki_trackers` set `name`='$name',description='$description', useAttachments='$useAttachments',useComments='$useComments', showCreated='$showCreated',showLastModif='$showLastModif',showStatus='$showStatus' where `trackerId`=$trackerId";

			$result = $this->query($query);
		} else {
			$now = date("U");

			$query = "replace into tiki_trackers(name,description,created,lastModif,items,showCreated,showLastModif,useComments,useAttachments,showStatus)
                values('$name','$description',$now,$now,0,'$showCreated','$showLastModif','$useComments','$useAttachments','$showStatus')";
			$result = $this->query($query);
			$trackerId = $this->getOne("select max(trackerId) from `tiki_trackers` where `name`='$name' and created=$now");
		}

		return $trackerId;
	}

	// Adds a new field to a tracker or modifies an existing field for a tracker
	function replace_tracker_field($trackerId, $fieldId, $name, $type, $isMain, $isTblVisible, $options) {
		$name = addslashes($name);

		$options = addslashes($options);
		// Check the name
		if ($fieldId) {
			$query = "update `tiki_tracker_fields` set `name`='$name',type='$type',isMain='$isMain',isTblVisible='$isTblVisible',options='$options' where `fieldId`=$fieldId";

			$result = $this->query($query);
		} else {
			$query = "replace into tiki_tracker_fields(trackerId,name,type,isMain,isTblVisible,options)
                values($trackerId,'$name','$type','$isMain','$isTblVisible','$options')";

			$result = $this->query($query);
			$fieldId = $this->getOne("select max(fieldId) from `tiki_tracker_fields` where `trackerId`=$trackerId and name='$name'");
			// Now add the field to all the existing items
			$query = "select `itemId` from `tiki_tracker_items` where `trackerId`=$trackerId";
			$result = $this->query($query);

			while ($res = $result->fetchRow()) {
				$itemId = $res['itemId'];

				$query2 = "replace into tiki_tracker_item_fields(itemId,fieldId,value) values($itemId,$fieldId,'')";
				$this->query($query2);
			}
		}

		return $fieldId;
	}

	function remove_tracker($trackerId) {
		// Remove the tracker
		$query = "delete from `tiki_trackers` where `trackerId`=$trackerId";

		$result = $this->query($query);
		// Remove the fields
		$query = "delete from `tiki_tracker_fields` where `trackerId`=$trackerId";
		$result = $this->query($query);
		// Remove the items (Remove fields for each item for this tracker)
		$query = "select `itemId` from `tiki_tracker_items` where `trackerId`=$trackerId";
		$result = $this->query($query);

		while ($res = $result->fetchRow()) {
			$query2 = "delete from `tiki_tracker_item_fields` where `itemId`=" . $res["itemId"];

			$result2 = $this->query($query2);
			$query2 = "delete from `tiki_tracker_item_comments` where `itemId`=" . $res["itemId"];
			$result2 = $this->query($query2);
		}

		$query = "delete from `tiki_tracker_items` where `trackerId`=$trackerId";
		$result = $this->query($query);
		$this->remove_object('tracker', $trackerId);
		return true;
	}

	function remove_tracker_field($fieldId) {
		$query = "delete from `tiki_tracker_fields` where `fieldId`=$fieldId";

		$result = $this->query($query);
		$query = "delete from `tiki_tracker_item_fields` where `fieldId`=$fieldId";
		$result = $this->query($query);
		return true;
	}

	function get_tracker($trackerId) {
		$query = "select * from `tiki_trackers` where `trackerId`=$trackerId";

		$result = $this->query($query);

		if (!$result->numRows())
			return false;

		$res = $result->fetchRow();
		return $res;
	}

	function get_tracker_field($fieldId) {
		$query = "select * from `tiki_tracker_fields` where `fieldId`=$fieldId";

		$result = $this->query($query);

		if (!$result->numRows())
			return false;

		$res = $result->fetchRow();
		return $res;
	}
/* End of tiki tracker construction functions */
}

$trklib = new TrackerLib($dbTiki);

?>
