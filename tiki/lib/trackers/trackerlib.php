<?php

include_once ('lib/notifications/notificationlib.php');

class TrackerLib extends TikiLib {

	var $trackerinfo_cache;

	function TrackerLib($db) {
		parent::TikiLib($db);
	}

	function add_item_attachment_hit($id) {
		global $count_admin_pvs, $user;
		if ($user != 'admin' || $count_admin_pvs == 'y' ) {
			$query = "update `tiki_tracker_item_attachments` set `downloads`=`downloads`+1 where `attId`=?";
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
		$query = "select `user`,`attId`,`itemId`,`filename`,`filesize`,`filetype`,`downloads`,`created`,`comment`,`longdesc`,`version` ";
		$query.= " from `tiki_tracker_item_attachments` $mid order by ".$this->convert_sortmode($sort_mode);
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
		$query = "insert into `tiki_tracker_item_attachments`(`itemId`,`filename`,`filesize`,`filetype`,`data`,`created`,`downloads`,`user`,";
		$query.= "`comment`,`path`,`version`,`longdesc`) values(?,?,?,?,?,?,?,?,?,?,?,?)";
		$result = $this->query($query,array((int) $itemId,$name,$size,$type,$data,(int) $now,0,$user,$comment,$fhash,$version,$longdesc));
	}

	function get_item_attachment($attId) {
		$query = "select * from `tiki_tracker_item_attachments` where `attId`=?";
		$result = $this->query($query,array((int) $attId));
		if (!$result->numRows()) return false;
		$res = $result->fetchRow();
		return $res;
	}

	function remove_item_attachment($attId) {
		global $t_use_dir;
		$path = $this->getOne("select `path` from `tiki_tracker_item_attachments` where `attId`=?",array((int) $attId));
		if ($path) @unlink ($t_use_dir . $path);
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
		if (!$result->numRows()) return false;
		$res = $result->fetchRow();
		return $res;
	}

	function get_last_position($id) {
		return $this->getOne("select max(`position`) from `tiki_tracker_fields` where `trackerId` = ?",array((int)$id));
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
			$id = $res2["fieldId"];
			$res["$id"] = $res2["value"];
		}

		return $res;
	}

	function get_item_id($trackerId,$fieldId,$value) {
		$query = "select distinct ttif.`itemid` from `tiki_tracker_items` tti, `tiki_tracker_fields` ttf, `tiki_tracker_item_fields` ttif ";
		$query.= " where tti.`trackerId`=ttf.`trackerId` and ttif.`fieldId`=ttf.`fieldId` and ttf.`trackerId`=? and ttf.`fieldId`=? and ttif.`value`=?";
		$ret = $this->getOne($query,array((int) $trackerId,(int)$fieldId,$value));
		return $ret;
	}

	function get_item($trackerId,$fieldId,$value) {
		$itemId = $this->get_item_id($trackerId,$fieldId,$value);
		return $this->get_tracker_item($itemId);
	}

	/* experimental shared */
	function get_item_value($trackerId,$itemId,$fieldId) {
		$query = "select ttif.`value` from `tiki_tracker_items` tti, `tiki_tracker_fields` ttf, `tiki_tracker_item_fields` ttif ";
		$query.= " where tti.`trackerId`=ttf.`trackerId` and ttif.`fieldId`=ttf.`fieldId` and ttf.`trackerId`=? and ttf.`fieldId`=? and ttif.`itemId`=?";
		return $this->getOne($query,array((int) $trackerId,(int)$fieldId,(int)$itemId));
	}

	/* experimental shared */
	function get_items_list($trackerId,$fieldId,$value,$status='o') {
		$query = "select distinct ttif.`itemid` from `tiki_tracker_items` tti, `tiki_tracker_fields` ttf, `tiki_tracker_item_fields` ttif ";
		$query.= " where tti.`trackerId`=ttf.`trackerId` and ttif.`fieldId`=ttf.`fieldId` and ttf.`trackerId`=? and ttf.`fieldId`=? and ttif.`value`=? and tti.`status`=?";
		$result = $this->query($query,array((int) $trackerId,(int)$fieldId,$value,$status));
		$ret = array();
		while ($res = $result->fetchRow()) {
			$ret[] = $res['itemid'];
		}
		return $ret;
	}

	function get_all_items($trackerId,$fieldId,$status='o') {
		global $cachelib;
		$sort_mode = "value_asc";
		$cache = md5($trackerId.'.'.$fieldId);
		if (!$cachelib->isCached($cache)) {
			$query = "select distinct ttif.`itemid`, ttif.`value` from `tiki_tracker_items` tti, `tiki_tracker_fields` ttf, `tiki_tracker_item_fields` ttif ";
			$query.= " where tti.`trackerId`=ttf.`trackerId` and ttif.`fieldId`=ttf.`fieldId` and ttf.`trackerId`=? and ttf.`fieldId`=? and tti.`status`=? order by ".$this->convert_sortmode($sort_mode);
			$result = $this->query($query,array((int) $trackerId,(int)$fieldId,$status));
			$ret = array();
			while ($res = $result->fetchRow()) {
				$k = $res['itemid'];
				$ret[$k] = $res['value'];
			}
			$cachelib->cacheItem($cache,serialize($ret));
			return $ret;
		} else {
			return unserialize($cachelib->getCached($cache));
		}
	}

	/* experimental shared */
	function list_items($trackerId, $offset, $maxRecords, $sort_mode, $listfields, $filterfield='', $filtervalue='', $status = '', $initial = '',$exactvalue='',$numsort=false) {
		
		$mid = " where tti.`trackerId`=? ";
		$bindvars = array((int) $trackerId);

		if ($status) {
			if (sizeof($status > 1)) {
				$sts = preg_split('//', $status, -1, PREG_SPLIT_NO_EMPTY);
				$mid.= " and (".implode('=? or ',array_fill(0,count($sts),'`status`'))."=?) ";
				$bindvars = array_merge($bindvars,$sts);
			} else {
				$mid.= " and tti.`status`=? ";
				$bindvars[] = $status;
			}
		}
		if (!$sort_mode) {
			$sort_mode = "lastModif_desc";
		}

		$csort_mode = '';
		$corder = "asc";
		if (substr($sort_mode,0,2) == "f_" or $filtervalue or $exactvalue) {
			if ($initial) {
				$mid.= "and ttif.`value` like ?";
				$bindvars[] = $initial.'%';
			}
			if ($exactvalue) {
				$mid.= "and ttif.`value`=?";
				$bindvars[] = $exactvalue;
				$csort_mode = $filterfield;
				$corder = "asc";
			} elseif ($filtervalue) {
				$mid.= "and ttif.`value` like ?";
				$bindvars[] = '%'.$filtervalue.'%';
				$csort_mode = $filterfield;
				$corder = "asc";
			} else {
				list($a,$csort_mode,$corder) = split('_',$sort_mode);
			}
			$bindvars[] = $csort_mode;
			if ($numsort) { 
				$query = "select tti.*, ttif.`value`,ttf.`type`, right(lpad(ttif.`value`,40,'0'),40) as ok from `tiki_tracker_items` tti, `tiki_tracker_item_fields` ttif, `tiki_tracker_fields` ttf  ";
				$query.= " $mid and tti.`itemId`=ttif.`itemId` and ttf.`fieldId`=ttif.`fieldId` and ttif.`fieldId`=? order by ".$this->convert_sortmode('ok_'.$corder);
			} else {
				$query = "select tti.*, ttif.`value`,ttf.`type` from `tiki_tracker_items` tti, `tiki_tracker_item_fields` ttif, `tiki_tracker_fields` ttf  ";
				$query.= " $mid and tti.`itemId`=ttif.`itemId` and ttf.`fieldId`=ttif.`fieldId` and ttif.`fieldId`=? order by ttif.".$this->convert_sortmode('value_'.$corder);
			}
			$query_cant = "select count(*) from `tiki_tracker_items` tti, `tiki_tracker_item_fields` ttif, `tiki_tracker_fields` ttf  ";
			$query_cant.= " $mid and tti.`itemId`=ttif.`itemId` and ttf.`fieldId`=ttif.`fieldId` and ttif.`fieldId`=? ";
		} else {
			$query = "select * from `tiki_tracker_items` tti $mid order by ".$this->convert_sortmode($sort_mode);
			$query_cant = "select count(*) from `tiki_tracker_items` tti $mid ";
		}
		$result = $this->query($query,$bindvars,$maxRecords,$offset);
		$cant = $this->getOne($query_cant,$bindvars);
		$type = '';
		$ret = array();
		$opts = $optsl = array();
		while ($res = $result->fetchRow()) {
			$fields = array();
			$opts = array();
			$itid = $res["itemId"];
			$query2 = "select ttf.`fieldId`, `value`,`isPublic` from `tiki_tracker_item_fields` ttif, `tiki_tracker_fields` ttf 
				where ttif.`fieldId`=ttf.`fieldId` and `isPublic`=? and `itemId`=? order by `position` asc";
			$result2 = $this->query($query2,array('y',(int) $res["itemId"]));
			$last = array();
			$res2 = array();
			$kx = "";
			while ($res1 = $result2->fetchRow()) {
				$inid = $res1['fieldId'];
				$fil[$inid] = $res1['value'];
			}
			foreach ($listfields as $fieldId=>$fopt) {
				if (isset($fil[$fieldId])) {
					$fopt['value'] = $fil[$fieldId];
				} else {
					$fopt['value'] = "";
				}
				$fopt["linkId"] = '';
				if ($fopt["type"] == 'r') {
					$fopt["links"] = array();
					if (!$opts) {
						$opts = split(',',$fopt['options']);
					}
					$fopt["linkId"] = $this->get_item_id($opts[0],$opts[1],$fopt["value"]);
					$fopt["trackerId"] = $opts[0];
				} elseif ($fopt["type"] == 'a') {
					$fopt["pvalue"] = $this->parse_data(trim($fopt["value"]));
				} elseif ($fopt["type"] == 'l') {
					if (!$optsl) {
						$optsl = split(',',$fopt['options']);
					}
					$fopt["links"] = array();
					$lst = $last[$optsl[2]];
					if ($lst) {
						$links = $this->get_items_list($optsl[0],$optsl[1],$lst);
						foreach ($links as $link) {
							$fopt["links"][$link] = $this->get_item_value($optsl[0],$link,$optsl[3]);
						}
						$fopt["trackerId"] = $optsl[0];
					}
				}
				if (isset($fopt["options"])) {
					$fopt["options_array"] = split(',',$fopt["options"]);
				}
				if (!$csort_mode || ($fieldId == $csort_mode)) {
					$kx = $fopt["value"].'.'.$itid;
				}
				$last[$fieldId] = $fopt["value"];
				$fields[] = $fopt;
			}
// var_dump($fields);die();
			$res["field_values"] = $fields;
			$res["comments"] = $this->getOne("select count(*) from `tiki_tracker_item_comments` where `itemId`=?",array((int) $itid));
			$ret["$kx"] = $res;
		}
		
		if ($corder == 'asc') {
			uksort($ret, 'strnatcasecmp');
		} else {
			krsort($ret);
		}
		
		//$ret=$this->sort_items_by_condition($ret,$sort_mode);
		$retval = array();
		$retval["data"] = array_values($ret);
		$retval["cant"] = $cant;
		return $retval;
	}


	function replace_item($trackerId, $itemId, $ins_fields, $status = '') {
		global $user;

		global $smarty;
		global $notificationlib;
		global $sender_email;
		$now = date("U");
		
		if ($itemId) {
			if ($status) {
				$query = "update `tiki_tracker_items` set `status`=?,`lastModif`=? where `itemId`=?";
				$result = $this->query($query,array($status,(int) $now,(int) $itemId));
			} else {
				$query = "update `tiki_tracker_items` set `lastModif`=? where `itemId`=?";
				$result = $this->query($query,array((int) $now,(int) $itemId));
			}
		} else {
			if (!$status) {
				$status = $this->getOne("select `value` from `tiki_tracker_options` where `trackerId`=? and `name`=?",array((int) $trackerId,'newItemStatus'));
			}
			$query = "insert into `tiki_tracker_items`(`trackerId`,`created`,`lastModif`,`status`) values(?,?,?,?)";
			$result = $this->query($query,array((int) $trackerId,(int) $now,(int) $now,$status));
			$new_itemId = $this->getOne("select max(`itemId`) from `tiki_tracker_items` where `created`=? and `trackerId`=?",array((int) $now,(int) $trackerId));
		}
		$the_data = '';

		for ($i = 0; $i < count($ins_fields["data"]); $i++) {
			if (isset($ins_fields["data"][$i]["type"]) and $ins_fields["data"][$i]["type"] == 'e') {
			} elseif (isset($ins_fields["data"][$i]["fieldId"])) {
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
			$ret[] = $res;
		}
		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}

				// Inserts or updates a tracker  
	function replace_tracker($trackerId, $name, $description, $options) {

			$now = date("U");
		if ($trackerId) {
			$query = "update `tiki_trackers` set `name`=?,`description`=?,`lastModif`=? where `trackerId`=?";
			$result = $this->query($query,array($name,$description,(int)date('U'),(int) $trackerId));
			$this->query("delete from `tiki_tracker_options` where `trackerId`=?",array((int)$trackerId));
			foreach ($options as $kopt=>$opt) {
				$this->query("insert into `tiki_tracker_options`(`trackerId`,`name`,`value`) values(?,?,?)",array((int)$trackerId,$kopt,$opt));
			}
		} else {
			$this->getOne("delete from `tiki_trackers` where `name`=?",array($name),false);
			$query = "insert into `tiki_trackers`(`name`,`description`,`created`,`lastModif`) values(?,?,?,?)";
			$result = $this->query($query,array($name,$description,(int) $now,(int) $now));
			$this->query("delete from `tiki_tracker_options` where `trackerId`=?",array((int)$trackerId));
			foreach ($options as $kopt=>$opt) {
				$this->query("insert into `tiki_tracker_options`(`trackerId`,`name`,`value`) values(?,?,?)",array((int)$trackerId,$kopt,$opt));
			}			
			$trackerId = $this->getOne("select max(`trackerId`) from `tiki_trackers` where `name`=? and `created`=?",array($name,(int) $now));
		}

		return $trackerId;
	}


	function replace_tracker_field($trackerId, $fieldId, $name, $type, $isMain, $isSearchable, $isTblVisible, $isPublic, $isHidden, $position, $options) {
	
		if ($fieldId) {
			$query = "update `tiki_tracker_fields` set `name`=? ,`type`=?,`isMain`=?,`isSearchable`=?,
				`isTblVisible`=?,`isPublic`=?,`isHidden`=?,`position`=?,`options`=? where `fieldId`=?";
			$bindvars=array($name,$type,$isMain,$isSearchable,$isTblVisible,$isPublic,$isHidden,(int)$position,$options,(int) $fieldId);

			$result = $this->query($query, $bindvars);
		} else {
			$this->getOne("delete from `tiki_tracker_fields` where `trackerId`=? and `name`=?",
				array((int) $trackerId,$name),false);
			$query = "insert into `tiki_tracker_fields`(`trackerId`,`name`,`type`,`isMain`,`isSearchable`,`isTblVisible`,`isPublic`,`isHidden`,`position`,`options`)
                values(?,?,?,?,?,?,?,?,?,?)";

			$result = $this->query($query,array((int) $trackerId,$name,$type,$isMain,$isSearchable,$isTblVisible,$isPublic,$isHidden,$position,$options));
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

	function remove_tracker_field($fieldId,$trackerId) {
		$query = "delete from `tiki_tracker_fields` where `fieldId`=?";
		$bindvars=array((int) $fieldId);
		$result = $this->query($query,$bindvars);
		$query = "delete from `tiki_tracker_item_fields` where `fieldId`=?";
		$result = $this->query($query,$bindvars);
		return true;
	}

	function get_tracker_options($trackerId) {
		$query = "select * from `tiki_tracker_options` where `trackerId`=?";
		$result = $this->query($query,array((int) $trackerId));
		if (!$result->numRows()) return false;
		$res = array();
		while ($opt = $result->fetchRow()) {
			$res["{$opt['name']}"] = $opt['value'];
		}
		return $res;
	}

	function get_tracker_field($fieldId) {
		$query = "select * from `tiki_tracker_fields` where `fieldId`=?";
		$result = $this->query($query,array((int) $fieldId));
		if (!$result->numRows()) return false;
		$res = $result->fetchRow();
		return $res;
	}

	function get_field_id($trackerId,$name) {
		return $this->getOne("select `fieldId` from `tiki_tracker_fields` where `trackerId`=? and `name`=?",array((int)$trackerId,$name));
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
		$type['c'] = array('label'=>tra('checkbox'),      'opt'=>true,  'help'=>tra('Checkbox options: put 1 if you need that next field is on the same row.'));
		$type['n'] = array('label'=>tra('numeric field'), 'opt'=>true,  'help'=>tra('Numeric options: 1,size,prepend,append with size in chars, prepend will be display before the field append wil be display just after, and initial 1 to make that next text field or checkbox is in same row. If you indicate only 1 it means next field is in same row too.'));
		$type['t'] = array('label'=>tra('text field'),    'opt'=>true,  'help'=>tra('Text options: 1,size,prepend,append with size in chars, prepend will be display before the field append wil be display just after, and initial 1 to make that next text field or checkbox is in same row. If you indicate only 1 it means next field is in same row too.'));
		$type['a'] = array('label'=>tra('textarea'),      'opt'=>true,  'help'=>tra('Textarea options: options,width,height with option is 1 or 0, rest is size indicated in chars and lines.'));
		$type['d'] = array('label'=>tra('drop down'),     'opt'=>true,  'help'=>tra('Dropdown options: list of items separated with commas.') );
		$type['u'] = array('label'=>tra('user selector'), 'opt'=>true,  'help'=>tra('User Selector: use options for automatic field feeding : you can use 1 for author login or 2 for modificator login.'));
		$type['g'] = array('label'=>tra('group selector'),'opt'=>true,  'help'=>tra('Group Selector: use options for automatic field feeding : you can use 1 for group of creation and 2 for group where modification come from. The default group has to be set, or the first group that come is chosen for a user, or the default group is Registered.'));
		$type['f'] = array('label'=>tra('date and time'), 'opt'=>false);
		$type['j'] = array('label'=>tra('jscalendar'),    'opt'=>false);
		$type['i'] = array('label'=>tra('image'),         'opt'=>true, 'help'=>tra('Image options: xSize,ySize indicated in pixels.')  );
		$type['x'] = array('label'=>tra('action'),        'opt'=>true, 'help'=>tra('Action options: Label,post,tiki-index.php,page:fieldname,highlight=test') );
		$type['h'] = array('label'=>tra('header'),        'opt'=>false);
		$type['e'] = array('label'=>tra('category'),      'opt'=>true, 'help'=>tra('Category options: parentId') );
		$type['r'] = array('label'=>tra('item link'),     'opt'=>true, 'help'=>tra('Item Link options: trackerId,fieldId links to item from trackerId which fieldId matches the content of that field.') );
		$type['l'] = array('label'=>tra('items list'),    'opt'=>true, 'help'=>tra('Items list options: trackerId,fieldIdThere, fieldIdHere, displayFieldIdThere displays the list of displayFieldIdThere from item in tracker trackerId where fieldIdThere matches fieldIdHere.') );
		return $type;
	}
	
	function status_types() {
		$status['o'] = array('label'=>tra('open'),'perm'=>'tiki_p_view_trackers','image'=>'img/icons2/status_open.gif');
		$status['p'] = array('label'=>tra('pending'),'perm'=>'tiki_p_view_trackers_pending','image'=>'img/icons2/status_pending.gif');
		$status['c'] = array('label'=>tra('closed'),'perm'=>'tiki_p_view_trackers_closed','image'=>'img/icons2/status_closed.gif');
		return $status;
	}
}

$trklib = new TrackerLib($dbTiki);

?>
