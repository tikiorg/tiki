<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * Tracker Library
 *
 * \brief Functions to support accessing and processing of the Trackers.
 *
 * @package		Tiki
 * @subpackage		Trackers
 * @author		Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
 * @copyright		Copyright (c) 2002-2009, All Rights Reserved.
 * 			See copyright.txt for details and a complete list of authors.
 * @license		LGPL - See license.txt for details.
 * @version		SVN $Rev: 25023 $
 * @filesource
 * @link		http://dev.tiki.org/Trackers
 * @since		Always
 */
/**
 * This script may only be included, so it is better to die if called directly.
 */
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

/**
 * TrackerLib Class
 *
 * This class extends the TikiLib class.
 */
class TrackerLib extends TikiLib
{

	var $trackerinfo_cache;

	private function attachments() {
		return $this->table('tiki_tracker_item_attachments');
	}

	private function comments() {
		return $this->table('tiki_comments');
	}

	private function itemFields() {
		return $this->table('tiki_tracker_item_fields');
	}

	private function trackers() {
		return $this->table('tiki_trackers');
	}

	private function items() {
		return $this->table('tiki_tracker_items');
	}

	private function fields() {
		return $this->table('tiki_tracker_fields');
	}

	private function options() {
		return $this->table('tiki_tracker_options');
	}

	private function logs() {
		return $this->table('tiki_tracker_item_field_logs');
	}

	function remove_field_images($fieldId) {
		$itemFields = $this->itemFields();
		$values = $itemFields->fetchColumn('value', array('fieldId' => (int) $fieldId));
		foreach ($values as $file) {
			if (file_exists($file)) {
				unlink($file);
			}
		}
	}

	function add_item_attachment_hit($id) {
		global $prefs, $user;
		if ($user != 'admin' || $prefs['count_admin_pvs'] == 'y' ) {
			$attachments = $this->attachments();
			$attachments->update(array(
				'hits' => $attachments->increment(1),
			), array(
				'attId' => (int) $id,
			));
		}
		return true;
	}

	function get_item_attachment_owner($attId) {
		return $this->attachments()->fetchOne('user', array('attId' => (int) $attId));
	}

	function list_item_attachments($itemId, $offset = 0, $maxRecords = -1, $sort_mode = 'attId_asc', $find = '') {
		$attachments = $this->attachments();

		$order = $attachments->sortMode($sort_mode);
		$fields = array('user', 'attId', 'itemId', 'filename', 'filesize', 'filetype', 'hits', 'created', 'comment', 'longdesc', 'version');

		$conditions = array(
			'itemId' => (int) $itemId,
		);

		if ($find) {
			$conditions['filename'] = $attachments->like("%$find%");
		}

		return array(
			'data' => $attachments->fetchAll($fields, $conditions, $maxRecords, $offset, $order),
			'cant' => $attachments->fetchCount($conditions),
		);
	}

	function get_item_nb_attachments($itemId) {
		$attachments = $this->attachments();

		$ret = $attachments->fetchRow(array(
			'hits' => $attachments->sum('hits'),
			'attachments' => $attachments->count(),
		), array('itemId' => $itemId));

		return $ret ? $ret : array();
	}

	function get_item_nb_comments($itemId) {
		return $this->comments()->fetchCount(array(
			'object' => (int) $itemId,
			'objectType' => 'trackeritem',
		));
	}

	function list_all_attachements($offset=0, $maxRecords=-1, $sort_mode='created_desc', $find='') {
		$attachments = $this->attachments();

		$fields = array('user', 'attId', 'itemId', 'filename', 'filesize', 'filetype', 'hits', 'created', 'comment', 'path');
		$order = $attachments->sortMode($sort_mode);
		$conditions = array();

		if ($find) {
			$conditions['filename'] = $attachments->like("%$find%");
		}

		return array(
			'data' => $attachments->fetchAll($fields, $conditions, $maxRecords, $offset, $order),
			'cant' => $attachments->fetchCount($conditions),
		);
	}

	function file_to_db($path,$attId) {
		if (is_readable($path)) {
			$updateResult = $this->attachments()->update(array(
				'data' => file_get_contents($path),
				'path' => '',
			), array(
				'attId' => (int) $attId,
			));

			if ($updateResult) {
				unlink($path);
			}
		}
	}

	function db_to_file($path,$attId) {
		$attachments = $this->attachments();

		$data = $attachments->fetchOne('data', array('attId' => (int) $attId));
		if (false !== file_put_contents($path, $data)) {
			$attachments->update(array(
				'data' => '',
				'path' => basename($path),
			), array('attId' => (int) $attId));
		}
	}

	function get_item_attachment($attId) {
		return $this->attachments()->fetchFullRow(array('attId' => (int) $attId));
	}

	function remove_item_attachment($attId=0, $itemId=0) {
		global $prefs;
		$attachments = $this->attachments();
		$paths = array();

		if (empty($attId) && !empty($itemId)) {
			if ($prefs['t_use_db'] === 'n') {
				$paths = $attachments->fetchColumn('path', array('itemId' => $itemId));
			}

			$this->query('update `tiki_tracker_item_fields` ttif left join `tiki_tracker_fields` ttf using (`fieldId`) set `value`=? where ttif.`itemId`=? and ttf.`type`=?', array('', (int) $itemId, 'A'));
			$attachments->deleteMultiple(array('itemId' => $itemId));

		} else if (!empty($attId)) {
			if ($prefs['t_use_db'] === 'n') {
				$paths = $attachments->fetchColumn('path', array('attId' => (int) $attId));
			}
			$this->query('update `tiki_tracker_item_fields` ttif left join `tiki_tracker_fields` ttf using (`fieldId`) set `value`=? where ttif.`value`=? and ttf.`type`=?', array('', (int) $attId, 'A'));
			$attachments->delete(array('attId' => (int) $attId));
		}
		foreach (array_filter($paths) as $path) {
			@unlink ($prefs['t_use_dir'] . $path);
		}
	}

	function replace_item_attachment($attId, $filename, $type, $size, $data, $comment, $user, $fhash, $version, $longdesc, $trackerId=0, $itemId=0,$options='', $notif=true) {
		global $prefs;
		$attachments = $this->attachments();

		$comment = strip_tags($comment);
		$now = $this->now;
		if (empty($attId)) {
			$attId = $attachments->insert(array(
				'itemId' => (int) $itemId,
				'filename' => $filename,
				'filesize' => $size,
				'filetype' => $type,
				'data' => $data,
				'created' => $now,
				'hits' => 0,
				'user' => $user,
				'comment' => $comment,
				'path' => $fhash,
				'version' => $version,
				'longdesc' => $longdesc,
			));
		} elseif (empty($filename)) {
			$attachments->update(array(
				'user' => $user,
				'comment' => $comment,
				'version' => $version,
				'longdesc' => $longdesc,
			), array('attId' => $attId));
		} else {
			$path = $attachments->fetchOne('path', array('attId' => (int) $attId));
			if ($path) {
				@unlink ($prefs['t_use_dir'] . $path);
			}

			$attachments->update(array(
				'filename' => $filename,
				'filesize' => $size,
				'filetype' => $type,
				'data' => $data,
				'user' => $user,
				'comment' => $comment,
				'path' => $fhash,
				'version' => $version,
				'longdesc' => $longdesc,
			), array('attId' => (int) $attId));
		}

		if (!$notif) {
			return $attId;
		}

		$watchers = $this->get_notification_emails($trackerId, $itemId, $options);
		if (count($watchers > 0)) {
			$smarty = TikiLib::lib('smarty');
			$trackerName = $this->trackers()->fetchOne('name', array('trackerId' => (int) $trackerId));

			$smarty->assign('mail_date', $this->now);
			$smarty->assign('mail_user', $user);
			$smarty->assign('mail_action', 'New File Atttached to Item:' . $itemId . ' at tracker ' . $trackerName);
			$smarty->assign('mail_itemId', $itemId);
			$smarty->assign('mail_trackerId', $trackerId);
			$smarty->assign('mail_trackerName', $trackerName);
			$smarty->assign('mail_attId', $attId);
			$smarty->assign('mail_data', $filename."\n".$comment."\n".$version."\n".$longdesc);
			$foo = parse_url($_SERVER["REQUEST_URI"]);
			$machine = $this->httpPrefix( true ). $foo["path"];
			$smarty->assign('mail_machine', $machine);
			$parts = explode('/', $foo['path']);
			if (count($parts) > 1)
				unset ($parts[count($parts) - 1]);
			$smarty->assign('mail_machine_raw', $this->httpPrefix( true ). implode('/', $parts));
			if (!isset($_SERVER["SERVER_NAME"])) {
				$_SERVER["SERVER_NAME"] = $_SERVER["HTTP_HOST"];
			}
			include_once ('lib/webmail/tikimaillib.php');
			$smarty->assign('server_name', $_SERVER['SERVER_NAME']);
			$desc = $this->get_isMain_value($trackerId, $itemId);
			$smarty->assign('mail_item_desc', $desc);
			foreach ($watchers as $w) {
				$mail = new TikiMail($w['user']);
				$mail->setHeader("From", $prefs['sender_email']);
				$mail->setSubject($smarty->fetchLang($w['language'], 'mail/tracker_changed_notification_subject.tpl'));
				$mail->setText($smarty->fetchLang($w['language'], 'mail/tracker_changed_notification.tpl'));
				$mail->send(array($w['email']));
			}
		}

		return $attId;
	}

	function list_last_comments($trackerId = 0, $itemId = 0, $offset = -1, $maxRecords = -1) {
		global $user;
	    $mid = "1=1";
	    $bindvars = array();

	    if ($itemId != 0) {
			$mid .= " and `itemId`=?";
			$bindvars[] = (int) $itemId;
	    }

	    if ($trackerId != 0) {
			$query = "select t.*, t.object itemId from `tiki_comments` t left join `tiki_tracker_items` a on t.`object`=a.`itemId` where $mid and a.`trackerId`=? and t.`objectType` = 'trackeritem' order by t.`commentDate` desc";
			$bindvars[] = $trackerId;
			$query_cant = "select count(*) from `tiki_comments` t left join `tiki_tracker_items` a on t.`object`=a.`itemId` where $mid and a.`trackerId`=? AND t.`objectType` = 'trackeritem' order by t.`commentDate` desc";
	    } else {
			if (!$this->user_has_perm_on_object($user, $trackerId, 'tracker', 'tiki_p_view_trackers') ) {
				return array('cant'=>0);
			}

			$query = "select t.*, t.object itemId, a.`trackerId` from `tiki_comments` t left join `tiki_tracker_items` a on t.`object`=a.`itemId` where $mid AND t.`objectType` = 'trackeritem' order by `commentDate` desc";
			$query_cant = "select count(*) from `tiki_comments` where $mid AND `objectType` = 'trackeritem'";
	    }

	    $ret = $this->fetchAll($query,$bindvars,$maxRecords,$offset);
	    $cant = $this->getOne($query_cant,$bindvars);

		foreach ( $ret as &$res ) {
			if (!$trackerId && !$this->user_has_perm_on_object($user, $res['trackerId'], 'tracker', 'tiki_p_view_trackers') ) {
				--$cant;
				continue;
			}
			$res["parsed"] = $this->parse_comment($res["data"]);
		}

		return array(
			'data' => $ret,
			'cant' => $cant,
		);
	}

	function get_last_position($trackerId) {
		$fields = $this->fields();
		return $fields->fetchOne($fields->max('position'), array('trackerId' => (int) $trackerId));
	}

	function get_tracker_item($itemId) {
		$res = $this->items()->fetchFullRow(array('itemId' => (int) $itemId));
		if (! $res) {
			return false;
		}

		$itemFields = $this->itemFields();
		$fields = $itemFields->fetchMap('fieldId', 'value', array('itemId' => (int) $itemId));

		foreach ($fields as $id => $value) {
			$res[$id] = $value;
		}

		return $res;
	}

	function get_item_id($trackerId,$fieldId,$value) {
		$query = "select ttif.`itemId` from `tiki_tracker_items` tti, `tiki_tracker_fields` ttf, `tiki_tracker_item_fields` ttif ";
		$query.= " where tti.`trackerId`=ttf.`trackerId` and ttif.`fieldId`=ttf.`fieldId` and ttf.`trackerId`=? and ttf.`fieldId`=? and ttif.`value`=?";
		return $this->getOne($query,array((int) $trackerId,(int)$fieldId,$value));
	}

	function get_item($trackerId,$fieldId,$value) {
		$itemId = $this->get_item_id($trackerId,$fieldId,$value);
		return $this->get_tracker_item($itemId);
	}

	/* experimental shared */
	/* trackerId is useless */
	function get_item_value($trackerId,$itemId,$fieldId) {
		global $prefs;
		$value = $this->itemFields()->fetchOne('value', array(
			'fieldId' => (int) $fieldId,
			'itemId' => (int) $itemId,
		));

		if ($this->is_multilingual($fieldId) == 'y') {
			$list = json_decode($value, true);
			if (isset($list[$prefs['language']])) {
				return $list[$prefs['language']];
			}
		}
		
		return $value;
	}

	/*shared*/
	function list_tracker_items($trackerId, $offset, $maxRecords, $sort_mode, $fields, $status = '', $initial = '') {

		$filters = array();
		if ($fields) {
			$temp_max = count($fields["data"]);
			for ($i = 0; $i < $temp_max; $i++) {
				$fieldId = $fields["data"][$i]["fieldId"];
				$filters[$fieldId] = $fields["data"][$i];
			}
		}
		$csort_mode = '';
		if (substr($sort_mode,0,2) == "f_") {
			list($a,$csort_mode,$corder) = explode('_',$sort_mode, 3);
		}
		$trackerId = (int) $trackerId;
		if ($trackerId == -1) {
			$mid = " where 1=1 ";
			$bindvars = array();
		} else {
			$mid = " where tti.`trackerId`=? ";
			$bindvars = array($trackerId);
		}
		if ($status) {
			$mid.= " and tti.`status`=? ";
			$bindvars[] = $status;
		}
		if ($initial) {
			$mid.= "and ttif.`value` like ?";
			$bindvars[] = $initial.'%';
		}
		if (!$sort_mode) {
			$temp_max = count($fields["data"]);
			for ($i = 0; $i < $temp_max; $i++) {
				if ($fields['data'][$i]['isMain'] == 'y') {
					$csort_mode = $fields['data'][$i]['name'];
					break;
				}
			}
		}
		if ($csort_mode) {
			$sort_mode = $csort_mode."_desc";
			$bindvars[] = $csort_mode;
			$query = "select tti.*, ttif.`value` from `tiki_tracker_items` tti, `tiki_tracker_item_fields` ttif, `tiki_tracker_fields` ttf  ";
			$query.= " $mid and tti.`itemId`=ttif.`itemId` and ttf.`fieldId`=ttif.`fieldId` and ttf.`name`=? order by ttif.`value`";
			$query_cant = "select count(*) from `tiki_tracker_items` tti, `tiki_tracker_item_fields` ttif, `tiki_tracker_fields` ttf  ";
			$query_cant.= " $mid and tti.`itemId`=ttif.`itemId` and ttf.`fieldId`=ttif.`fieldId` and ttf.`name`=? ";
		} else {
			if (!$sort_mode) {
				$sort_mode = "lastModif_desc";
			}
			$query = "select * from `tiki_tracker_items` tti $mid order by ".$this->convertSortMode($sort_mode);
			$query_cant = "select count(*) from `tiki_tracker_items` tti $mid ";
		}
		$result = $this->fetchAll($query,$bindvars,$maxRecords,$offset);
		$cant = $this->getOne($query_cant,$bindvars);
		$ret = array();
		foreach ( $result as $res ) {
			$fields = array();
			$itid = $res["itemId"];
			$query2 = "select ttif.`fieldId`,`name`,`value`,`type`,`isTblVisible`,`isMain`,`position`
				from `tiki_tracker_item_fields` ttif, `tiki_tracker_fields` ttf
				where ttif.`fieldId`=ttf.`fieldId` and `itemId`=? order by `position` asc";
			$result2 = $this->fetchAll($query2,array((int) $res["itemId"]));
			$pass = true;
			$kx = "";
			foreach ( $result2 as $res2 ) {
				// Check if the field is visible!
				$fieldId = $res2["fieldId"];
				if (count($filters) > 0) {
					if (isset($filters[$fieldId]["value"]) and $filters[$fieldId]["value"]) {
						if (in_array($filters[$fieldId]["type"], array('a', 't'))) {
							if (!stristr($res2["value"], $filters[$fieldId]["value"]))
								$pass = false;
						} else {
							if (strtolower($res2["value"]) != strtolower($filters[$fieldId]["value"])) {
								$pass = false;
							}
						}
					}
					if (preg_replace("/[^a-zA-Z0-9]/","",$res2["name"]) == $csort_mode) {
						$kx = $res2["value"].$itid;
					}
				}
				$fields[] = $res2;
			}
			$res["field_values"] = $fields;
			$res["comments"] = $this->table('tiki_comments')->fetchCount(array(
				'object' => (int) $itid,
				'objectType' => 'trackeritem',
			));
			if ($pass) {
				$kl = $kx.$itid;
				$ret["$kl"] = $res;
			}
		}
		ksort($ret);
		//$ret=$this->sort_items_by_condition($ret,$sort_mode);
		$retval = array();
		$retval["data"] = array_values($ret);
		$retval["cant"] = $cant;
		return $retval;
	}

	/*shared*/
	function get_user_items($user) {
		$items = array();

		$query = "select ttf.`trackerId`, tti.`itemId` from `tiki_tracker_fields` ttf, `tiki_tracker_items` tti, `tiki_tracker_item_fields` ttif";
		$query .= " where ttf.`fieldId`=ttif.`fieldId` and ttif.`itemId`=tti.`itemId` and `type`=? and tti.`status`=? and `value`=?";
		$result = $this->fetchAll($query,array('u','o',$user));
		$ret = array();

		$trackers = $this->table('tiki_trackers');
		$trackerFields = $this->table('tiki_tracker_fields');
		$trackerItemFields = $this->table('tiki_tracker_item_fields');
		//FIXME Perm:filter ?
		foreach ( $result as $res ) {
			if (!$this->user_has_perm_on_object($user, $res['trackerId'], 'tracker', 'tiki_p_view_trackers')) {
				continue;
			}
			$itemId = $res["itemId"];

			$trackerId = $res["trackerId"];
			// Now get the isMain field for this tracker
			$fieldId = $trackerFields->fetchOne('fieldId', array(
				'isMain' => 'y',
				'trackerId' => (int) $trackerId
			));
			// Now get the field value
			$value = $trackerItemFields->fetchOne('value', array(
				'fieldId' => (int) $fieldId,
				'itemId' => (int) $itemId
			));
			$tracker = $trackers->fetchOne('name', array(
				'trackerId' => (int) $trackerId,
			));

			$aux["trackerId"] = $trackerId;
			$aux["itemId"] = $itemId;
			$aux["value"] = $value;
			$aux["name"] = $tracker;

			if (!in_array($itemId, $items)) {
				$ret[] = $aux;
				$items[] = $itemId;
			}
		}

		$groups = $this->get_user_groups($user);

		foreach ($groups as $group) {
			$query = "select ttf.`trackerId`, tti.`itemId` from `tiki_tracker_fields` ttf, `tiki_tracker_items` tti, `tiki_tracker_item_fields` ttif ";
			$query .= " where ttf.`fieldId`=ttif.`fieldId` and ttif.`itemId`=tti.`itemId` and `type`=? and tti.`status`=? and `value`=?";
			$result = $this->fetchAll($query,array('g','o',$group));

			foreach ( $result as $res ) {
				$itemId = $res["itemId"];

				$trackerId = $res["trackerId"];
				// Now get the isMain field for this tracker
				$fieldId = $trackerFields->fetchOne('fieldId', array(
					'isMain' => 'y',
					'trackerId' => (int) $trackerId
				));
				// Now get the field value
				$value = $trackerItemFields->fetchOne('value', array(
					'fieldId' => (int) $fieldId,
					'itemId' => (int) $itemId
				));
				$tracker = $trackers->fetchOne('name', array(
					'trackerId' => (int) $trackerId,
				));

				$aux["trackerId"] = $trackerId;
				$aux["itemId"] = $itemId;
				$aux["value"] = $value;
				$aux["name"] = $tracker;

				if (!in_array($itemId, $items)) {
					$ret[] = $aux;
					$items[] = $itemId;
				}
			}
		}
		return $ret;
	}

	/* experimental shared */
	function get_items_list($trackerId, $fieldId, $value, $status='o') {
		$query = "select distinct tti.`itemId`, tti.`itemId` i from `tiki_tracker_items` tti, `tiki_tracker_item_fields` ttif ";
		$query.= " where tti.`itemId`=ttif.`itemId` and ttif.`fieldId`=? and ttif.`value`=?";
		$bindvars = array((int)$fieldId, $value);
		if (!empty($status)) {
			$query .= ' and ' . $this->in('tti.status', str_split($status, 1), $bindvars);
		}
		$items = $this->fetchMap($query, $bindvars);
		return array_values($items);
	}

	function get_tracker($trackerId) {
		return $this->table('tiki_trackers')->fetchFullRow(array('trackerId' => (int) $trackerId));
	}
	
	function list_trackers($offset=0, $maxRecords=-1, $sort_mode='name_asc', $find='') {
		$categlib = TikiLib::lib('categ');
		$join = '';
		$where = '';
		$bindvars = array();
		if( $jail = $categlib->get_jail() ) {
			$categlib->getSqlJoin($jail, 'tracker', '`tiki_trackers`.`trackerId`', $join, $where, $bindvars);
		}	
		if ($find) {
			$findesc = '%' . $find . '%';
			$where .= ' and (`tiki_trackers`.`name` like ? or `tiki_trackers`.`description` like ?)';
			$bindvars = array_merge($bindvars, array($findesc, $findesc));
		}
		$query = "select * from `tiki_trackers` $join where 1=1 $where order by `tiki_trackers`.".$this->convertSortMode($sort_mode);
		$query_cant = "select count(*) from `tiki_trackers` $join where 1=1 $where";
		$result = $this->fetchAll($query,$bindvars,$maxRecords,$offset);
		$cant = $this->getOne($query_cant,$bindvars);
		$ret = array();
		$list = array();
		//FIXME Perm:filter ?
		foreach ( $result as $res ) {
			global $user;
			$add=$this->user_has_perm_on_object($user,$res['trackerId'],'tracker','tiki_p_view_trackers');
			if ($add) {
				$ret[] = $res;
				$list[$res['trackerId']] = $res['name'];
			}
		}
		$retval = array();
		$retval["list"] = $list;
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}

	// This function gets the prefix alias page name e.g. Org:230 for the pretty tracker
	// wiki page corresponding to a tracker item (230 in the example) using prefix aliases
	// Returns false if no such page is found.
	function get_trackeritem_pagealias($itemId) {
		$trackerId = $this->table('tiki_tracker_items')->fetchOne('trackerId', array('itemId' => $itemId));

		$semanticlib = TikiLib::lib('semantic');
		$t_links = $semanticlib->getLinksUsing('trackerid', array( 'toPage' => $trackerId ) );

		if (count($t_links)) {
			$p_links = $semanticlib->getLinksUsing('prefixalias', array( 'fromPage' => $t_links[0]['fromPage'] ) );
			if (count($p_links)) {
				$ret = $p_links[0]['toPage'] . $itemId;
				return $ret;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	function concat_item_from_fieldslist($trackerId,$itemId,$fieldsId,$status='o',$separator=' ') {
		$res='';
		$sts = preg_split('/\|/', $fieldsId, -1, PREG_SPLIT_NO_EMPTY);
		$definition = Tracker_Definition::get($trackerId);
		foreach ($sts as $field){
			$myfield = $definition->getField($field);

			$myfield['value'] = $this->get_item_value($trackerId,$itemId,$field);

			$res .= $separator;
			$res .= trim($this->field_render_value(array(
				'field' => $myfield,
				'process' => 'y',
			)));
		}
		return $res;
	}

	function concat_all_items_from_fieldslist($trackerId,$fieldsId,$status='o',$separator=' ') {
		$sts = preg_split('/\|/', $fieldsId, -1, PREG_SPLIT_NO_EMPTY);
		$res = array();
		foreach ($sts as $field){
			$myfield=$this->get_tracker_field($field);
			$is_date=($myfield['type']=='f');
			$is_trackerlink=($myfield['type']=='r');
			$tmp="";
			$tmp=$this->get_all_items($trackerId,$field,$status, false);//deliberatly do not check perm on categs on items
			$options = preg_split('/,/', $myfield["options"]);
			foreach ($tmp as $key=>$value){
				if ($is_date) $value=$this->date_format("%e/%m/%y",$value);
				if ($is_trackerlink){
					$value=$this->concat_item_from_fieldslist($options[0],$value,$options[3]);
				}
				if (!empty($res[$key])) {
					$res[$key].=$separator.$value;
				} else {
					$res[$key] = $value;
				}
			}
		}
		return $res;
	}


	function valid_status($status) {
		return in_array($status, array('o', 'c', 'p', 'op', 'oc', 'pc', 'opc'));
	}
	// allfields == false will not check the perm on categ
	function get_all_items($trackerId,$fieldId,$status='o', $allfields='') {
		global $prefs;
		$cachelib = TikiLib::lib('cache');

		$jail = '';
		$needToCheckCategPerms = $this->need_to_check_categ_perms($allfields);
		if ($prefs['feature_categories'] == 'y' && $needToCheckCategPerms) {
			$categlib = TikiLib::lib('categ');
			$jail = $categlib->get_jail();
		}

		$sort_mode = "value_asc";
		$cache = md5('trackerfield'.$fieldId.$status);
		if ($this->is_multilingual($fieldId) == 'y') {
			$multi_languages=$prefs['available_languages'];
			$cache = md5('trackerfield'.$fieldId.$status.$prefs['language']);
		} else {
			unset($multi_languages);
		}
		if (!empty($jail)) {
			$cache .= md5(serialize($jail));
		}

		if ( ( ! $ret = $cachelib->getSerialized($cache) ) || !$this->valid_status($status)) {
			$sts = preg_split('//', $status, -1, PREG_SPLIT_NO_EMPTY);
			$mid = "  (".implode('=? or ',array_fill(0,count($sts),'tti.`status`'))."=?) ";
			$fieldIdArray = preg_split('/\|/', $fieldId, -1, PREG_SPLIT_NO_EMPTY);
			$mid.= " and (".implode('=? or ',array_fill(0,count($fieldIdArray),'ttif.`fieldId`'))."=?) ";
			$bindvars = array_merge($sts,$fieldIdArray);
			$join = '';
			if (!empty($jail)) {
				$categlib->getSqlJoin($jail, 'trackeritem', 'tti.`itemId`', $join, $mid, $bindvars);
			}
			$query = "select ttif.`itemId` , ttif.`value` FROM `tiki_tracker_items` tti,`tiki_tracker_item_fields` ttif $join ";
			$query.= " WHERE  $mid and  tti.`itemId` = ttif.`itemId` order by ".$this->convertSortMode($sort_mode);
			$ret = $this->fetchAll($query,$bindvars);
			$cachelib->cacheItem($cache,serialize($ret));
		}
		if ($needToCheckCategPerms) {
			$ret = $this->filter_categ_items($ret);
		}
		$definition = Tracker_Definition::get($trackerId);
		$field = $definition->getField($fieldId);
		$ret2 = array();
		foreach ($ret as $res) {
			$k = $res['itemId'];
			$field['value'] = $res['value'];

			$rendered = $this->field_render_value(array(
				'field' => $field,
				'process' => 'y',
			));

			$ret2[$k] = trim(strip_tags($rendered));
		}
		return $ret2;
	}
	function need_to_check_categ_perms($allfields='') {
		global $prefs;
		if ($allfields === false) { // use for itemlink field - otherwise will be too slow
			return false;
		}
		$needToCheckCategPerms = false;
		if ($prefs['feature_categories'] == 'y') {
			$categlib = TikiLib::lib('categ');
			if (empty($allfields['data'])) {
				$needToCheckCategPerms = true;
			} else {
				foreach ($allfields['data'] as $f) {
					if ($f['type'] == 'e') {
						$needToCheckCategPerms = true;
						break;
					}
				}
			}
		}
		return $needToCheckCategPerms;
	}

	function get_all_tracker_items($trackerId){
		return $this->items()->fetchColumn('itemId', array('trackerId' => (int) $trackerId));
	}

	function getSqlStatus($status, &$mid, &$bindvars, $trackerId) {
		global $user;
		if (is_array($status)) {
			$status = implode('', $status);
		}

		// Check perms
		if ( $status && ! $this->user_has_perm_on_object($user, $trackerId, 'tracker', 'tiki_p_view_trackers_pending') && ! $this->group_creator_has_perm($trackerId, 'tiki_p_view_trackers_pending') ) {
			$status = str_replace('p', '', $status);
		}
		if ( $status && ! $this->user_has_perm_on_object($user, $trackerId, 'tracker', 'tiki_p_view_trackers_closed')  && ! $this->group_creator_has_perm($trackerId, 'tiki_p_view_trackers_closed') ) {
			$status = str_replace('c', '', $status);
		}

		if (!$status) {
			return false;
		} elseif ($status == 'opc') {
				return true;
		} elseif (strlen($status) > 1) {
			$sts = preg_split('//', $status, -1, PREG_SPLIT_NO_EMPTY);
			if (count($sts)) {
				$mid.= " and (".implode('=? or ',array_fill(0,count($sts),'`status`'))."=?) ";
				$bindvars = array_merge($bindvars,$sts);
			}
		} else {
			$mid.= " and tti.`status`=? ";
			$bindvars[] = $status;
		}
		return true;
	}
	function group_creator_has_perm($trackerId, $perm) {
		global $prefs;
		if ($groupCreatorFieldId = $this->get_field_id_from_type($trackerId, 'g', '1%')) {
			$tracker_info = $this->get_tracker($trackerId);
			$perms = $this->get_special_group_tracker_perm($tracker_info);
			return empty($perms[$perm])? false: true;
		} else {
			return false;
		}
	}
	/* group creator perms can only add perms,they can not take away perm
	   and they are only used if tiki_p_view_trackers is not set for the tracker and if the tracker ha a group creator field
	   must always be combined with a filter on the groups
	*/
	function get_special_group_tracker_perm($tracker_info, $global=false) {
		global $prefs;
		$userlib = TikiLib::lib('user');
		$smarty = TikiLib::lib('smarty');
		$ret = array();
		$perms = $userlib->get_object_permissions($tracker_info['trackerId'], 'tracker', $prefs['trackerCreatorGroupName']);
		foreach ($perms as $perm) {
			$ret[$perm['permName']] ='y';
			if ($global) {
				$p = $perm['permName'];
				global $$p;
				$$p = 'y';
				$smarty->assign("$p", 'y');
			}
		}
		if ($tracker_info['writerGroupCanModify'] == 'y') { // old configuration
			$ret['tiki_p_modify_tracker_items'] = 'y';
			if ($global) {
				$tiki_p_modify_tracker_items = 'y';
				$smarty->assign('tiki_p_modify_tracker_items', 'y');
			}
		}
		return $ret;
	}
	/* to filter filterfield is an array of fieldIds
	 * and the value of each field is either filtervalue or exactvalue
	 * ex: filterfield=array('1','2', 'sqlsearch'=>array('3', '4'), '5')
	 * ex: filtervalue=array(array('this', '*that'), '')
	 * ex: exactvalue= array('', array('there', 'those'), 'these', array('>'=>10))
	 * will filter items with fielId 1 with a value %this% or %that, and fieldId 2 with the value there or those, and fieldId 3 or 4 containing these and fieldId 5 > 10
	 * listfields = array(fieldId=>array('type'=>, 'name'=>...), ...)
	 * allfields is only for performance issue - check if one field is a category
	 */
	function list_items($trackerId, $offset=0, $maxRecords=-1, $sort_mode ='' , $listfields='', $filterfield = '', $filtervalue = '', $status = '', $initial = '', $exactvalue = '', $filter='', $allfields=null) {
		//echo '<pre>FILTERFIELD:'; print_r($filterfield); echo '<br />FILTERVALUE:';print_r($filtervalue); echo '<br />EXACTVALUE:'; print_r($exactvalue); echo '<br />STATUS:'; print_r($status); echo '<br />FILTER:'; print_r($filter); /*echo '<br />LISTFIELDS'; print_r($listfields);*/ echo '</pre>';
		global $prefs;

		$cat_table = '';
		$sort_tables = '';
		$sort_join_clauses = '';
		$csort_mode = '';
		$corder = '';
		$trackerId = (int)$trackerId;
		$numsort = false;

		$mid = ' WHERE tti.`trackerId` = ? ';
		$bindvars = array($trackerId);
		$join = '';

		if (!empty($filter)) {
			$mid2 = array();
			$this->parse_filter($filter, $mid2, $bindvars);
			if (!empty($mid2)) {
				$mid .= ' AND '.implode(' AND ', $mid2);
			}
		}

		if ( $status && ! $this->getSqlStatus($status, $mid, $bindvars, $trackerId) ) {
			return array('cant' => 0, 'data' => '');
		}
		if ( substr($sort_mode, 0, 2) == 'f_' ) {
			list($a, $asort_mode, $corder) = preg_split('/_/', $sort_mode);
		}
		if ( $initial ) {
			$mid .= ' AND ttif.`value` LIKE ?';
			$bindvars[] = $initial.'%';
			if (isset($asort_mode)) {
				$mid .= ' AND ttif.`fieldId` = ?';
				$bindvars[] = $asort_mode;
			}
		}
		if ( ! $sort_mode ) $sort_mode = 'lastModif_desc';

		if ( substr($sort_mode, 0, 2) == 'f_' or !empty($filterfield) ) {
			$cat_table = '';
			if ( substr($sort_mode, 0, 2) == 'f_' ) {
				$csort_mode = 'sttif.`value` ';
				$sort_tables = ' LEFT JOIN (`tiki_tracker_item_fields` sttif)'
					.' ON (tti.`itemId` = sttif.`itemId`'
					." AND sttif.`fieldId` = $asort_mode"
					.')';
				// Do we need a numerical sort on the field ?
				$field = $this->get_tracker_field($asort_mode);
				switch ($field['type']) {
				case 'C':
				case '*':
				case 'q':
				case 'n':
					$numsort = true;
					break;
				case 'l':
					// Do nothing, value is dynamic and thus cannot be sorted on
					$csort_mode = 1;
					$csort_tables = '';
					break;
				case 'r':
					$link_field = intval($field['fieldId']);
					$remote_field = intval($field['options_array'][1]);
					$sort_tables = '
						LEFT JOIN `tiki_tracker_item_fields` itemlink ON tti.itemId = itemlink.itemId AND itemlink.fieldId = ' . $link_field . '
						LEFT JOIN `tiki_tracker_item_fields` sttif ON itemlink.value = sttif.itemId AND sttif.fieldId = ' . $remote_field . '
					';
					break;
				case 's':
					if ($field['name'] == 'Rating' || $field['name'] == tra('Rating')) {
						$numsort = true;
					}
					break;
				}
			} else {
				list($csort_mode, $corder) = preg_split('/_/', $sort_mode);
				$csort_mode = 'tti.`'.$csort_mode.'` ';
			}

			if (empty($filterfield)) {
				$nb_filtered_fields = 0;
			} elseif ( ! is_array($filterfield) ) {
				$fv = $filtervalue;
				$ev = $exactvalue;
				$ff = $filterfield;
				$nb_filtered_fields = 1;
			} else {
				$nb_filtered_fields = count($filterfield);
			}

			for ( $i = 0 ; $i < $nb_filtered_fields ; $i++ ) {
				if ( is_array($filterfield) ) { //multiple filter on an exact value or a like value - each value can be simple or an array
					$ff = $filterfield[$i];
					$ev = isset($exactvalue[$i])? $exactvalue[$i]:'';
					$fv = isset($filtervalue[$i])?$filtervalue[$i]:'' ;
				}
				$filter = $this->get_tracker_field($ff);

				// Determine if field is an item list field and postpone filtering till later if so
				if ($filter["type"] == 'l' && isset($filter['options_array'][2]) && isset($filter['options_array'][2]) && isset($filter['options_array'][3]) ) {
					$linkfilter[] = array('filterfield' => $ff, 'exactvalue' => $ev, 'filtervalue' => $fv);
					continue;
				}
				$j = ( $i > 0 ) ? '0' : '';
				$cat_table .= " INNER JOIN `tiki_tracker_item_fields` ttif$i ON (ttif$i.`itemId` = ttif$j.`itemId`)";

				if (is_array($ff['sqlsearch'])) {
					$mid .= " AND ttif$i.`fieldId` in (".implode(',', array_fill(0,count($ff['sqlsearch']),'?')).')';
					$bindvars = array_merge($bindvars, $ff['sqlsearch']);
				} elseif ( $ff ) {
					$mid .= " AND ttif$i.`fieldId`=? ";
					$bindvars[] = $ff;
				}

				if ( $filter['type'] == 'e' && $prefs['feature_categories'] == 'y' && (!empty($ev) || !empty($fv)) ) { //category

					$value = empty($fv) ? $ev : $fv;
					if ( ! is_array($value) && $value != '' ) {
						$value = array($value);
						$not = '';
					} elseif (is_array($value) && array_key_exists('not', $value)) {
						$value = array($value['not']);
						$not = 'not';
					}
					if (empty($not)) {
						$cat_table .= " INNER JOIN `tiki_objects` tob$ff ON (tob$ff.`itemId` = tti.`itemId`)"
							." INNER JOIN `tiki_category_objects` tco$ff ON (tob$ff.`objectId` = tco$ff.`catObjectId`)";
						$mid .= " AND tob$ff.`type` = 'trackeritem' AND tco$ff.`categId` IN ( ";
					} else {
						$cat_table .= " left JOIN `tiki_objects` tob$ff ON (tob$ff.`itemId` = tti.`itemId`)"
							." left JOIN `tiki_category_objects` tco$ff ON (tob$ff.`objectId` = tco$ff.`catObjectId`)";
						$mid .= " AND tob$ff.`type` = 'trackeritem' AND tco$ff.`categId` NOT IN ( ";
					}
					$first = true;
					foreach ( $value as $k => $catId ) {
						if (is_array($catId)) {
							// this is a grouped AND logic for optimization indicated by the value being array 
							$innerfirst = true;
							foreach ( $catId as $c ) {
								if (is_array($c)) {
									$innerfirst = true;
									foreach ($c as $d) {
										$bindvars[] = $d; 
										if ($innerfirst)  
											$innerfirst = false;
										else
											$mid .= ','; 
										$mid .= '?';
									}
								} else {
									$bindvars[] = $c;
									$mid .= '?';
								} 
							}
							if ($k < count($value) - 1 ) {
								$mid .= " ) AND ";
								if (empty($not)) {
									$ff2 = $ff . '_' . $k;
									$cat_table .= " INNER JOIN `tiki_category_objects` tco$ff2 ON (tob$ff.`objectId` = tco$ff2.`catObjectId`)";
									$mid .= "tco$ff2.`categId` IN ( ";
								} else {
									$ff2 = $ff . '_' . $k;
									$cat_table .= " left JOIN `tiki_category_objects` tco$ff2 ON (tob$ff.`objectId` = tco$ff2.`catObjectId`)";
									$mid .= "tco$ff2.`categId` NOT IN ( ";
								}
							}
						} else {
							$bindvars[] = $catId;
							if ($first)
								$first = false;
							else
								$mid .= ',';
							$mid .= '?';
						}
					}
					$mid .= " ) ";
					if (!empty($not)) {
						$mid .= " OR tco$ff.`categId` IS NULL ";
					}
				} elseif ( $filter['type'] == 'usergroups' ) {
					$userFieldId = $this->get_field_id_from_type($trackerId, 'u', '1%'); // user creator field;
					$cat_table .= " INNER JOIN `tiki_tracker_item_fields` ttifu ON (tti.`itemId`=ttifu.`itemId`) INNER JOIN `users_users` uu ON (ttifu.`value`=uu.`login`) INNER JOIN `users_usergroups` uug ON (uug.`userId`=uu.`userId`)";
					$mid .= ' AND ttifu.`fieldId`=? AND  uug.`groupName`=? ';
					$bindvars[] = $userFieldId;
					$bindvars[] = empty($ev)?$fv: $ev;
				} elseif ( $filter['type'] == '*') { // star
					$mid .= " AND ttif$i.`value`*1>=? ";
					$bindvars[] = $ev;
					if (($j = array_search($ev, $filter['options_array'])) !== false && $j+1 < count($filter['options_array'])) {
						$mid .= " AND ttif$i.`value`*1<? ";
						$bindvars[] = $filter['options_array'][$j+1];
					}
				} elseif ($filter['type'] == 'r' && ($fv || $ev)) {
					$cv = $fv ? $fv : $ev;

					if (is_numeric($cv)) {
						$mid .= " AND ttif$i.`value` = ? ";
						$bindvars[] = $cv;
					} else {
						$cat_table .= " INNER JOIN tiki_tracker_item_fields ttif{$i}_remote ON ttif$i.`value` = ttif{$i}_remote.`itemId` AND ttif{$i}_remote.`fieldId` = " . intval($filter['options_array'][1]) . ' ';
						$mid .= " AND ttif{$i}_remote.`value` LIKE ? ";
						$bindvars[] = $ev ? $ev : "%$fv%";
					}
				} elseif ($ev) {
					if (is_array($ev)) {
						$keys = array_keys($ev);
						if (in_array((string)$keys[0], array('<', '>'))) {
							$mid .= " AND ttif$i.`value`".$keys[0]."? + 0";
							$bindvars[] = $ev[$keys[0]];
						} elseif (in_array((string)$keys[0], array('<=', '>='))) {
							$mid .= " AND (ttif$i.`value`".$keys[0]."? + 0 OR ttif$i.`value` = ?)";
							$bindvars[] = $ev[$keys[0]];
							$bindvars[] = $ev[$keys[0]];
						} elseif ($keys[0] == 'not') {
							$mid .= " AND ttif$i.`value` not in (".implode(',', array_fill(0,count($ev),'?')).")";
							$bindvars = array_merge($bindvars, array_values($ev));
						} else {
							$mid .= " AND ttif$i.`value` in (".implode(',', array_fill(0,count($ev),'?')).")";
							$bindvars = array_merge($bindvars, array_values($ev));
						}
					} elseif (is_array($ff['sqlsearch'])) {
						$mid .= " AND MATCH(ttif$i.`value`) AGAINST(? IN BOOLEAN MODE)";
						$bindvars[] = $ev;
					} else {
						$mid.= " AND ttif$i.`value`=? ";
						$bindvars[] = empty($ev)? $fv: $ev;
					}

				} elseif ( $fv ) {
					if (!is_array($fv)) {
						$value = array($fv);
					}
					$mid .= ' AND(';
					$cpt = 0;
					foreach ($value as $v) {
						if ($cpt++)
							$mid .= ' OR ';
						$mid .= " upper(ttif$i.`value`) like upper(?) ";
						if ( substr($v, 0, 1) == '*' || substr($v, 0, 1) == '%') {
							$bindvars[] = '%'.substr($v, 1);
						} elseif ( substr($v, -1, 1) == '*' || substr($v, -1, 1) == '%') {
							$bindvars[] = substr($v, 0, strlen($v)-1).'%';
						} else {
							$bindvars[] = '%'.$v.'%';
						}
					}
					$mid .= ')';
				} elseif (empty($ev) && empty($fv)) { // test null value
					$mid.= " AND ttif$i.`value`=? OR ttif$i.`value` IS NULL";
					$bindvars[] = '';
				}
			}
		} else {
			if (strpos($sort_mode, '_') !== false) {
				list($csort_mode, $corder) = preg_split('/_/', $sort_mode);
			} else {
				$csort_mode = $sort_mode;
				$corder = 'asc';
			}
			$csort_mode = "`" . $csort_mode . "`";
			if ($csort_mode == '`itemId`')
				$csort_mode = 'tti.`itemId`';
			$sort_tables = '';
			$cat_tables = '';
		}

		$needToCheckCategPerms = $this->need_to_check_categ_perms($allfields);
		if( $needToCheckCategPerms) {
			$categlib = TikiLib::lib('categ');
			if ( $jail = $categlib->get_jail() ) {
				$categlib->getSqlJoin($jail, 'trackeritem', 'tti.`itemId`', $join, $mid, $bindvars);
			}
		}
		$base_tables = '('
			.' `tiki_tracker_items` tti'
			.' INNER JOIN `tiki_tracker_item_fields` ttif ON tti.`itemId` = ttif.`itemId`'
			.' INNER JOIN `tiki_tracker_fields` ttf ON ttf.`fieldId` = ttif.`fieldId`'
			.')'.$join;

		$fieldIds = array();
		foreach ($listfields as $k => $f) {
			if (isset($f['fieldId'])) {
				$fieldIds[] = $f['fieldId'];
			} else {
				$fieldIds[] = $k;	// sometimes filterfields are provided with the fieldId only on the array keys
			}
		}

		if (!empty($fieldIds)) {
			$mid .= ' AND ' . $this->in('ttif.fieldId', $fieldIds, $bindvars);
		}

		$query = 'SELECT tti.*, ttif.`value`, ttf.`type`'
				.', '.( ($numsort) ? "right(lpad($csort_mode,40,'0'),40)" : $csort_mode).' as `sortvalue`'
			.' FROM '.$base_tables.$sort_tables.$cat_table
			.$mid
			.' GROUP BY tti.`itemId`'
			.' ORDER BY '.$this->convertSortMode('sortvalue_'.$corder);
		//echo htmlentities($query); print_r($bindvars);
		$query_cant = 'SELECT count(DISTINCT ttif.`itemId`) FROM '.$base_tables.$sort_tables.$cat_table.$mid;

		$ret1 = $this->fetchAll($query, $bindvars, $maxRecords, $offset);
		$cant = $this->getOne($query_cant, $bindvars);
		$type = '';
		$ret = array();
		if ($needToCheckCategPerms) {
			$ret1 = $this->filter_categ_items($ret1);
		}

		foreach ($ret1 as $res) {
			$res['itemUser'] = '';
			if ($listfields !== null) {
				$res['field_values'] = $this->get_item_fields($trackerId, $res['itemId'], $listfields, $res['itemUser']);
			}
			if (!empty($asort_mode)) {
				foreach ($res['field_values'] as $i=>$field)
					if ($field['fieldId'] == $asort_mode ) {
						$kx = $field['value'].'.'.$res['itemId'];
				}
			}
			if (isset($linkfilter) && $linkfilter) {
				$filterout = false;
				// NOTE: This implies filterfield if is link field has to be in fields set
				foreach ($res['field_values'] as $i=>$field) {
					foreach ($linkfilter as $lf) {
						if ($field['fieldId'] == $lf["filterfield"]) {
							// extra comma at the front and back of filtervalue to avoid ambiguity in partial match
							if ($lf["filtervalue"] && strpos(',' . implode(',',$field['items']) . ',', $lf["filtervalue"]) === false) {
								$filterout = true;
								break 2;
							} elseif ($lf["exactvalue"] && !in_array($lf['exactvalue'], $field['items'])) {
								$filterout = true;
								break 2;
							}
						}
					}	
				}
				if ($filterout) {
					continue;
				}	
			}

			$res['geolocation'] = TikiLib::lib('geo')->get_coordinates('trackeritem', $res['itemId']);

			if (empty($kx)) // ex: if the sort field is non visible, $kx is null
				$ret[] = $res;
			else
				$ret[$kx] = $res;
		}
		$retval = array();
		$retval['data'] = array_values($ret);
		$retval['cant'] = $cant;
		return $retval;
	}
	function filter_categ_items($ret) {
		//this is an approxomation - the perm should be function of the status
		$categlib = TikiLib::lib('categ');
		if (empty($ret['itemId']) || $categlib->is_categorized('trackeritem', $ret['itemId'])) {
			return Perms::filter(array('type' => 'trackeritem'), 'object', $ret, array('object' => 'itemId'), 'view_trackers');
		} else {
			return $ret;
		}
	}
	/* listfields fieldId=>ooptions */
	function get_item_fields($trackerId, $itemId, $listfields, &$itemUser, $alllang=false) {
		global $prefs, $user, $tiki_p_admin_trackers;

		$definition = Tracker_Definition::get($trackerId);
		$info = $this->get_tracker_item((int) $itemId);
		$factory = new Tracker_Field_Factory($definition);

		$userField = $definition->getUserField();
		$itemUser = $info[$userField];

		$fields = array();
		foreach ( $listfields as $fieldId =>$fopt ) {
			if (empty($fopt['fieldId'])) { // to accept listfield as a simple table
				$fopt['fieldId'] = $fieldId;
			}

			$fopt['trackerId'] = $trackerId;

			$handler = $factory->getHandler($fopt, $info);
			if ($handler) {
				$fopt = array_merge($fopt, $handler->getFieldData());
				$fields[] = $fopt;
			}
		}

		return($fields);
	}

	function replace_item($trackerId, $itemId, $ins_fields, $status = '', $ins_categs = 0, $bulk_import = false) {
		global $user, $prefs, $tiki_p_admin_trackers, $tiki_p_admin_users;
		$final_event = 'tiki.trackeritem.update';

		$categlib = TikiLib::lib('categ');
		$cachelib = TikiLib::lib('cache');
		$smarty = TikiLib::lib('smarty');
		$logslib = TikiLib::lib('logs');
		$userlib = TikiLib::lib('user');
		$tikilib = TikiLib::lib('tiki');
		$notificationlib = TikiLib::lib('notification');

		$items = $this->items();
		$itemFields = $this->itemFields();
		$fields = $this->fields();

		$fil = array();
		if (!empty($itemId)) {
			$fil = $itemFields->fetchMap('fieldId', 'value', array('itemId' => $itemId));
		}

		$old_values = $fil;

		$tracker_definition = Tracker_Definition::get($trackerId);
		$tracker_info = $tracker_definition->getInformation();

		if (!empty($itemId)) {
			$new_itemId = 0;
			$oldStatus = $this->items()->fetchOne('status', array('itemId' => $itemId));

			$status = $status ? $status : $oldStatus;
			$fil['status'] = $status;
			$old_values['status'] = $oldStatus;

			$items->update(array(
				'status' => $status,
				'lastModif' => $this->now,
				'lastModifBy' => $user
			), array(
				'itemId' => (int) $itemId,
			));
			$version = $this->last_log_version($itemId) + 1;
			if (($logslib->add_action('Updated', $itemId, 'trackeritem', $version)) == 0) {
				$version = 0;
			}
		} else {
			if (isset($tracker_info['newItemStatus'])) {
				$status = $tracker_info['newItemStatus'];
			}
			if (empty($status)) {
				$status = 'o';
			}
			$fil['status'] = $status;
			$old_values['status'] = '';

			$new_itemId = $items->insert(array(
				'trackerId' => (int) $trackerId,
				'created' => $this->now,
				'createdBy' => $user,
				'lastModif' => $this->now,
				'lastModifBy' => $user,
				'status' => $status,
			));

			$logslib->add_action('Created', $new_itemId, 'trackeritem');
			$version = 0;

			$final_event = 'tiki.trackeritem.create';
		}

		$currentItemId = $itemId ? $itemId : $new_itemId;
		$item_info = $this->get_item_info($currentItemId);

		if (!empty($oldStatus) || !empty($status)) {
			if (!empty($itemId) && $oldStatus != $status) {
			   $this->log($version, $itemId, -1, $oldStatus);
			}
		}

		$trackersync = false;
		if (!empty($prefs["user_trackersync_trackers"])) {
			$trackersync_trackers = preg_split('/\s*,\s*/', $prefs["user_trackersync_trackers"]);
			if (in_array($trackerId, $trackersync_trackers)) {
				$trackersync = true;
			}
		}
		
		// If this is a user tracker it needs to be detected right here before actual looping of fields happen
		$trackersync_user = $user;
		foreach($ins_fields["data"] as $i=>$array) {
			if ($array['type'] == 'u' && isset($array['options_array'][0]) && $array['options_array'][0] == '1') {
				if ($prefs['user_selector_realnames_tracker'] == 'y' && $array['type'] == 'u') {
					if (!$userlib->user_exists($array['value'])) {
						$finalusers = $userlib->find_best_user(array($array['value']), '' , 'login');
						if (!empty($finalusers[0]) && !(isset($_REQUEST['register']) && isset($_REQUEST['name']) && $_REQUEST['name'] == $array['value'])) {
							// It could be in fact that a new user is required (when no match is found or during registration even if match is found)
							$ins_fields['data'][$i]['value'] = $finalusers[0];
						}
					}
				}
				$trackersync_user = $array['value'];
			}
		}
		
		foreach($ins_fields["data"] as $i=>$array) {
			// Old values were prefilled at the begining of the function and only replaced at the end of the iteration
			$fieldId = $array['fieldId'];
			$old_value = isset($fil[$fieldId]) ? $fil[$fieldId] : null;

			$handler = $this->get_field_handler($array, array_merge($item_info, $fil));

			if (method_exists($handler, 'handleSave')) {
				$array = array_merge($array, $handler->handleSave($array['value'], $old_value));
				$value = $array['value'];

				if ($value !== false) {
					$this->modify_field($currentItemId, $array['fieldId'], $value);

					if ($itemId) {
						// On update, save old value
						$this->log($version, $itemId, $array['fieldId'], $old_value);
					}
					$fil[$fieldId] = $value;
				}
				continue;
			}

			$value = isset($array["value"]) ? $array["value"] : null;

			if ($array['type']=='p' && ($user == $trackersync_user || $tiki_p_admin_users == 'y')) {
				if ($array['options_array'][0] == 'password') {
					if (!empty($array['value']) && $prefs['change_password'] == 'y' && ($e = $userlib->check_password_policy($array['value'])) == '') {
						$userlib->change_user_password($trackersync_user, $array['value']);
					}
					if (!empty($itemId)) {
						$this->log($version, $itemId, $array['fieldId'], '?');
					}
				} elseif ($array['options_array'][0] == 'email') {
					if (!empty($array['value']) && validate_email($array['value'])) {
						$old_value = $userlib->get_user_email($trackersync_user);
						$userlib->change_user_email($trackersync_user, $array['value']);
					}
					if (!empty($itemId) && $old_value != $array['value']) {
						$this->log($version, $itemId, $array['fieldId'], $old_value);
					}
				} else {
					$old_value = $tikilib->get_user_preference($trackersync_user, $array['options_array'][0]);
					$tikilib->set_user_preference($trackersync_user, $array['options_array'][0], $array['value']);
					if (!empty($itemId) && $old_value != $array['value']) {
						$this->log($version, $itemId, $array['fieldId'], $array['value']);
					}
				}
			} elseif ($array['type'] == 'k') { //page selector
				if ($array['value'] != '') {
					$this->modify_field($currentItemId, $array['fieldId'], $value);
					if ($itemId) {
						// On update, save old value
						$this->log($version, $itemId, $array['fieldId'], $old_value);
					}
					$fil[$fieldId] = $value;
					if (!$this->page_exists($array['value'])) {
						$opts = preg_split('/,/', $array['options']);
						if (!empty($opts[2])) {
							$IP = $this->get_ip_address();
							$info = $this->get_page_info($opts[2]);
							$this->create_page($array['value'], 0, $info['data'], $this->now, '', $user, $IP, $info['description'], $info['lang'], $info['is_html'], array(), $info['wysiwyyg'], $info['wiki_authors_style']);
						}
					}
				}
			}	else {
				$is_date = in_array($array["type"], array('f', 'j'));
				$is_visible = !isset($array["isHidden"]) || $array["isHidden"] == 'n';

				if ($currentItemId || $array['type'] != 'q') {
					$this->modify_field($currentItemId, $fieldId, $value);
					if ($old_value) {
						if ($is_visible) {
							if ($is_date) {
								$dformat = $prefs['short_date_format'].' '.$prefs['short_time_format'];
								$old_value = $this->date_format($dformat, (int)$old_value);
								$new_value = $this->date_format($dformat, (int)$value);
							} else {
								$new_value = $value;
							}
							if ($old_value != $new_value && !empty($itemId)) {
								$this->log($version, $itemId, $array['fieldId'], $old_value);
							}
						}
					}
				}

				$fil[$fieldId] = $value;
			}
		}

		TikiLib::events()->trigger($final_event, array(
			'type' => 'trackeritem',
			'object' => $currentItemId,
			'version' => $version,
			'trackerId' => $trackerId,
			'values' => $fil,
			'old_values' => $old_values,
			'bulk_import' => $bulk_import,
		));

		return $currentItemId;
	}

	function modify_field($itemId, $fieldId, $value)
	{
		$conditions = array(
			'itemId' => (int) $itemId,
			'fieldId' => (int) $fieldId,
		);

		$this->itemFields()->insertOrUpdate(array(
			'value' => $value,
		), $conditions);
	}

	function groupName($tracker_info, $itemId) {
		if (empty($tracker_info['autoCreateGroupInc'])) {
			$groupName = $tracker_info['name'];
		} else {
			$userlib = TikiLib::lib('user');
			$group_info = $userlib->get_groupId_info($tracker_info['autoCreateGroupInc']);
			$groupName = $group_info['groupName'];
		}
		return "$groupName $itemId";
	}

	function _format_data($field, $data) {
		$data = trim($data);
		if($field['type'] == 'a') {
			if(isset($field["options_array"][3]) and $field["options_array"][3] > 0 and strlen($data) > $field["options_array"][3]) {
				$data = substr($data,0,$field["options_array"][3])." (...)";
			}
		} elseif ($field['type'] == 'c') {
			if($data != 'y') $data = 'n';
		}
		return $data;
	}

	/* Experimental feature.
	 * PHP's execution time limit of 30 seconds may have to be extended when
	 * importing large files ( > 1000 items).
	 */
	function import_items($trackerId, $indexField, $csvHandle, $csvDelimiter = "," , $replace = true) {

		// Read the first line.  It contains the names of the fields to import
		if (($data = fgetcsv($csvHandle, 4096, $csvDelimiter)) === FALSE) return -1;
		$nColumns = count($data);
		for ($i = 0; $i < $nColumns; $i++) {
			$data[$i] = trim($data[$i]);
		}
		$fields = $this->list_tracker_fields($trackerId, 0, -1, 'position_asc', '');
		$temp_max = count($fields["data"]);
		$indexId = -1;
		for ($i = 0; $i < $temp_max; $i++) {
			$column[$i] = -1;
			for ($j = 0; $j < $nColumns; $j++) {
				if($fields["data"][$i]['name'] == $data[$j]) {
					$column[$i] = $j;
				}
				if($indexField == $data[$j]) {
					$indexId = $j;
				}
			}
		}

		// If a primary key was specified, check that it was found among the columns of the file
		if($indexField && $indexId == -1) return -1;

		$total = 0;
		while (($data = fgetcsv($csvHandle, 4096, $csvDelimiter)) !== FALSE) {
			$status = array_shift($data);
			$itemId = array_shift($data);
			for ($i = 0; $i < $temp_max-2; $i++) {
				if (isset($data[$i])) {
					$fields["data"][$i]['value'] = $data[$i];
				} else {
					$fields["data"][$i]['value'] = "";
				}
			}
			$this->replace_item($trackerId, $itemId, $fields, $status, array(), true);
			$total++;
		}

		// TODO: Send a notification indicating that an import has been done on this tracker

		return $total;
	}

	/**
	 * Called from tiki-admin_trackers.php import button
	 * 
	 * @param int		$trackerId
	 * @param resource	$csvHandle 		file handle to import
	 * @param bool		$replace_rows 	make new items for those with existing itemId
	 * @param string	$dateFormat 	used for item fields of type date
	 * @param string	$encoding 		defaults "UTF8"
	 * @param string	$csvDelimiter 	defaults to ","
	 * @return number	items imported
	 */
	function import_csv($trackerId, $csvHandle, $replace_rows = true, $dateFormat='', $encoding='UTF8', $csvDelimiter=',', $updateLastModif = true) {
		$tikilib = TikiLib::lib('tiki');
		$items = $this->items();
		$itemFields = $this->itemFields();

		$tracker_info = $this->get_tracker_options($trackerId);
		if (($header = fgetcsv($csvHandle,100000,  $csvDelimiter)) === FALSE) {
			return 'Illegal first line';
		}
		if ($encoding == 'UTF-8') {
			// See en.wikipedia.org/wiki/Byte_order_mark
			if (substr($header[0],0,3) == "\xef\xbb\xbf") {
				$header[0] = substr($header[0],3);
			}
		}
		$max = count($header);
		for ($i = 0; $i < $max; $i++) {
			if ($encoding == 'ISO-8859-1') {
				$header[$i] = utf8_encode($header[$i]);
			}
			$header[$i] = preg_replace('/ -- [0-9]*$/', ' -- ', $header[$i]);
		}
		if (count($header) != count(array_unique($header))) {
			return 'Duplicate header names';
		}
		$total = 0;
		$need_reindex = array();
		$fields = $this->list_tracker_fields($trackerId, 0, -1, 'position_asc', '');
		while (($data = fgetcsv($csvHandle,100000,  $csvDelimiter)) !== FALSE) {
			$status = 'o';
			$itemId = 0;
			$created = $tikilib->now;
			$lastModif = $created;
			$cats = '';
			for ($i = 0; $i < $max; $i++) {
				if ($encoding == 'ISO-8859-1') {
					$data[$i] = utf8_encode($data[$i]);
				}
				if ($header[$i] == 'status') {
					if ($data[$i] == 'o' || $data[$i] =='p' || $data[$i] == 'c')
						$status = $data[$i];
				} elseif ($header[$i] == 'itemId') {
					$itemId = $data[$i];
				} elseif ($header[$i] == 'created' && is_numeric($data[$i])) {
					$created = $data[$i];
				} elseif ($header[$i] == 'lastModif' && is_numeric($data[$i])) {
					$lastModif = $data[$i];
				} elseif ($header[$i] == 'categs') { // for old compatibility
					$cats = preg_split('/,/',trim($data[$i]));
				}
			}
			if ($itemId && ($t = $this->get_tracker_for_item($itemId)) && $t == $trackerId && $replace_rows) {
				if (in_array('status', $header))
					$update['status'] = $status;
				if (in_array('created', $header))
					$update['created'] = (int) $created;
				if ($updateLastModif) {
					$update['lastModif'] = (int) $lastModif;
				}
				if (!empty($update)) {
					$items->update($update, array('itemId' => (int) $itemId));
				}
				$replace = true;
			} elseif ($itemId && !$t & $t === $trackerId) {
				$items->insert(array(
					'trackerId' => (int) $trackerId,
					'created' => (int) $created,
					'lastModif' => (int) $lastModif,
					'status' => $status,
					'itemId' => (int) $itemId,
				));
				$replace = false;
			} else {
				$itemId = $items->insert(array(
					'trackerId' => (int) $trackerId,
					'created' => (int) $created,
					'lastModif' => (int) $lastModif,
					'status' => $status,
				));
				if (empty($itemId) || $itemId < 1) {
					return "Problem inserting tracker item: trackerId=$trackerId, created=$created, lastModif=$lastModif, status=$status";
				}
				$replace = false;
			}
			$need_reindex[] = $itemId;
			if (!empty($cats)) {
				$this->categorized_item($trackerId, $itemId, "item $itemId", $cats);
			}
			for ($i = 0; $i < $max; ++$i) {
				if (!preg_match('/ -- $/', $header[$i])) {
					continue;
				}
				$h = preg_replace('/ -- $/', '', $header[$i]);
				foreach ($fields['data'] as $field) {
					if ($field['name'] == $h) {
						if ($field['type'] == 'p' && $field['options_array'][0] == 'password') {
							//$userlib->change_user_password($user, $ins_fields['data'][$i]['value']);
							continue;
						}
						switch ($field['type']) {
						case 'e':
							$cats = preg_split('/%%%/', trim($data[$i]));
							$catIds = array();
							if (!empty($cats)) {
								foreach ($cats as $c) {
									$categlib = TikiLib::lib('categ');
									if ($cId = $categlib->get_category_id(trim($c)))
										$catIds[] = $cId;
								}
								if (!empty($catIds)) {
									$this->categorized_item($trackerId, $itemId, "item $itemId", $catIds);
								}
							}
							$data[$i] = '';
							break;
						case 's':
							$data[$i] = '';
							break;
						case 'a':
							$data[$i] = preg_replace('/\%\%\%/',"\r\n",$data[$i]);
							break;
						case 'c':
							if (strtolower($data[$i]) == 'yes' || strtolower($data[$i]) == 'on')
								$data[$i] = 'y';
							elseif (strtolower($data[$i]) == 'no')
								$data[$i] = 'n';
							break;
						case 'f':
						case 'j':
							if ($dateFormat == 'mm/dd/yyyy') {
								list($m, $d, $y) = preg_split('#/#', $data[$i]);
								$data[$i] = $tikilib->make_time(0, 0, 0, $m, $d, $y);
							} elseif ($dateFormat == 'dd/mm/yyyy') {
								list($d, $m, $y) = preg_split('#/#', $data[$i]);
								$data[$i] = $tikilib->make_time(0, 0, 0, $m, $d, $y);
							} elseif ($dateFormat == 'yyyy-mm-dd') {
								list($y, $m, $d) = preg_split('#-#', $data[$i]);
								$data[$i] = $tikilib->make_time(0, 0, 0, $m, $d, $y);
							}
							break;
						case 'q':
							$data[$i] = $itemId;
							break;
						}

						if ($this->get_item_value($trackerId, $itemId, $field['fieldId']) !== false) {
							$itemFields->update(array('value' => $data[$i]), array(
								'itemId' => (int) $itemId,
								'fieldId' => (int) $field['fieldId'],
							));
						} else {
							$itemFields->insert(array(
								'itemId' => (int) $itemId,
								'fieldId' => (int) $field['fieldId'],
								'value' => $data[$i],
							));
						}
						break;
					}
				}
			}
			$total++;
		}

		$unifiedsearchlib = TikiLib::lib('unifiedsearch');

		foreach ( $need_reindex as $id ) {
			$unifiedsearchlib->invalidateObject('trackeritem', $id);
		}
		$unifiedsearchlib->processUpdateQueue();

		$cant_items = $items->fetchCount(array('trackerId' => (int) $trackerId));
		$this->trackers()->update(array('items' => (int) $cant_items, 'lastModif' => $this->now), array(
			'trackerId' => (int) $trackerId,
		));

		return $total;
	}

	
	function dump_tracker_csv($trackerId) {
		$tikilib = TikiLib::lib('tiki');
		$tracker_info = $this->get_tracker_options($trackerId);
		$fields = $this->list_tracker_fields($trackerId, 0, -1, 'position_asc', '');
		
		$trackerId = (int)$trackerId;
		
		// write out file header
		session_write_close();
		$this->write_export_header();
		
		// then "field names -- index" as first line
		$str = '';
		$str .= 'itemId,status,created,lastModif,';	// these headings weren't quoted in the previous export function
		if (count($fields['data']) > 0) {
			foreach ($fields['data'] as $field) {
				$str .= '"'.$field['name'].' -- '.$field['fieldId'].'",';
			}
		}
		echo $str;
		
		// prepare queries
		$mid = ' WHERE tti.`trackerId` = ? ';
		$bindvars = array($trackerId);
		$join = '';
		
		$query_items =	'SELECT tti.itemId, tti.status, tti.created, tti.lastModif'
						.' FROM  `tiki_tracker_items` tti'
						.$mid
						.' ORDER BY tti.`itemId` ASC';
		$query_fields =  'SELECT tti.itemId, ttif.`value`, ttf.`type`'
						.' FROM ('
						.' `tiki_tracker_items` tti'
						.' INNER JOIN `tiki_tracker_item_fields` ttif ON tti.`itemId` = ttif.`itemId`'
						.' INNER JOIN `tiki_tracker_fields` ttf ON ttf.`fieldId` = ttif.`fieldId`'
						.')'
						.$mid
						.' ORDER BY tti.`itemId` ASC, ttf.`position` ASC';
		$base_tables = '('
			.' `tiki_tracker_items` tti'
			.' INNER JOIN `tiki_tracker_item_fields` ttif ON tti.`itemId` = ttif.`itemId`'
			.' INNER JOIN `tiki_tracker_fields` ttf ON ttf.`fieldId` = ttif.`fieldId`'
			.')'.$join;
	
						
		$query_cant = 'SELECT count(DISTINCT ttif.`itemId`) FROM '.$base_tables.$mid;
		$cant = $this->getOne($query_cant, $bindvars);
		
		$avail_mem = $tikilib->get_memory_avail();
		$maxrecords_items = intval(($avail_mem - 10 * 1024 * 1025) / 5000);		// depends on size of items table (fixed)
		if ($maxrecords_items < 0) {	// cope with memory_limit = -1
			$maxrecords_items = -1;
		}
		$offset_items = 0;
		
		$items = $this->get_dump_items_array($query_items, $bindvars, $maxrecords_items, $offset_items);
		
		$avail_mem = $tikilib->get_memory_avail();							// update avail after getting first batch of items
		$maxrecords = (int)($avail_mem / 40000) * count($fields['data']);	// depends on number of fields
		if ($maxrecords < 0) {	// cope with memory_limit = -1
			$maxrecords = $cant * count($fields['data']);
		}
		$canto = $cant * count($fields['data']);
		$offset = 0;
		$lastItem = -1;
		$count = 0; $icount = 0;
		$field_values = array();
		
		// write out rows
		for ($offset = 0; $offset < $canto; $offset = $offset + $maxrecords) {
			$field_values = $this->fetchAll($query_fields, $bindvars, $maxrecords, $offset);
			$mem = memory_get_usage(true);
			
			foreach ( $field_values as $res ) {
				if ($lastItem != $res['itemId']) {
					$lastItem = $res['itemId'];
					echo "\n".$items[$lastItem]['itemId'].','.$items[$lastItem]['status'].','.$items[$lastItem]['created'].','.$items[$lastItem]['lastModif'].',';	// also these fields weren't traditionally escaped
					$count++;
					$icount++;
					if ($icount > $maxrecords_items && $maxrecords_items > 0) {
						$offset_items += $maxrecords_items;
						$items = $this->get_dump_items_array($query_items, $bindvars, $maxrecords_items, $offset_items);
						$icount = 0;
					}
				}
				echo '"' . str_replace(array('"', "\r\n", "\n"), array('\\"', '%%%', '%%%'), $res['value']) . '",';
			}
			ob_flush();
			flush();
			//if ($offset == 0) { $maxrecords = 1000 * count($fields['data']); }
		}
		echo "\n";
		ob_end_flush();
	}
	
	function get_dump_items_array($query, $bindvars, $maxrecords, $offset) {
		$items_array = $this->fetchAll($query, $bindvars, $maxrecords, $offset);
		$items = array();
		foreach ($items_array as $item) {
			$items[$item['itemId']] = $item;
		}
		unset($items_array);
		return $items;
	}

	function write_export_header() {
		header("Content-type: text/comma-separated-values; charset:".$_REQUEST['encoding']);
		if (!empty($_REQUEST['file'])) {
			if (preg_match('/.csv$/', $_REQUEST['file'])) {
				$file = $_REQUEST['file'];
			} else {
				$file = $_REQUEST['file'].'.csv';
			}
		} else {
			$file = tra('tracker').'_'.$_REQUEST['trackerId'].'.csv';
		}
		header("Content-Disposition: attachment; filename=$file");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
		header("Pragma: public");
	}

	// check the validity of each field values of a tracker item
	// and the presence of mandatory fields
	function check_field_values($ins_fields, $categorized_fields='', $trackerId='', $itemId='') {
		global $prefs;
		$mandatory_fields = array();
		$erroneous_values = array();
        if (isset($ins_fields)&&isset($ins_fields['data']))
		foreach($ins_fields['data'] as $f) {

			if ($f['type'] != 'q' and isset($f['isMandatory']) && $f['isMandatory'] == 'y') {

				if ($f['type'] == 'e') {
					if (!in_array($f['fieldId'], $categorized_fields)) {
						$mandatory_fields[] = $f;
					}
				} elseif (in_array($f['type'], array('a', 't')) && ($this->is_multilingual($f['fieldId']) == 'y')) {
					if (!isset($multi_languages)) {
						$multi_languages=$prefs['available_languages'];
					}
					//Check recipient
					if (isset($f['lingualvalue']) ) {
						foreach ($f['lingualvalue'] as $val) {
							foreach ($multi_languages as $num=>$tmplang) {	//Check if trad is empty
								if (!isset($val['lang']) ||!isset($val['value']) ||(($val['lang']==$tmplang) && strlen($val['value'])==0)) {
									$mandatory_fields[] = $f;
								}
							}
						}
					} else {
						$mandatory_fields[] = $f;
					}
				} elseif (in_array($f['type'], array('u', 'g')) && $f['options_array'][0] == 1) {
					;
				} elseif ($f['type'] == 'c' && (empty($f['value']) || $f['value'] == 'n')) {
					$mandatory_fields[] = $f;
				} elseif ($f['type'] == 'A' && !empty($itemId) && empty($f['value'])) {
					$val = $this->get_item_value($trackerId, $itemId, $f['fieldId']);
					if (empty($val)) {
						$mandatory_fields[] = $f;
					}
				} elseif (!isset($f['value']) or strlen($f['value']) == 0) {
					$mandatory_fields[] = $f;
				}
			}
			if (!empty($f['value'])) {

				switch ($f['type']) {
				// IP address (only for IPv4)
				case 'I':
					if (!$this->isValidIP($f['value'])) {
						$erroneous_values[] = $f;
					}
					break;
				// numeric
				case 'n':
					if(!is_numeric($f['value'])) {
						$f['error'] = tra('Field is not numeric');
						$erroneous_values[] = $f;
					}
					break;

				// email
				case 'm':
					if(!validate_email($f['value'],$prefs['validateEmail'])) {
						$erroneous_values[] = $f;
					}
					break;

				// password
				case 'p':
				if ($f['options_array'][0] == 'password') {
					$userlib = TikiLib::lib('user');
					if (($e = $userlib->check_password_policy($f['value'])) != '') {
						 $erroneous_values[] = $f;
					}
				} elseif ($f['options_array'][0] == 'email') {
					if (!validate_email($f['value'])) {
						$erroneous_values[] = $f;
					}
				}
				break;
				case 'a':
					if (isset($f['options_array'][5]) &&  $f['options_array'][5] > 0) {
						if (count(preg_split('/\s+/', trim($f['value']))) > $f['options_array'][5]) {
							$erroneous_values[] = $f;
						}
					}
					if (isset($f['options_array'][6]) &&  $f['options_array'][6] == 'y') {
						if (in_array($f['value'], $this->list_tracker_field_values($trackerId, $f['fieldId'], 'opc', 'y', '', $itemId))) {
							$erroneous_values[] = $f;
						}
					}
					break;
				}
			}
		}

		$res = array();
		$res['err_mandatory'] = $mandatory_fields;
		$res['err_value'] = $erroneous_values;
		return $res;
	}

	function remove_tracker_item($itemId) {
		global $user, $prefs;
		$res = $this->items()->fetchFullRow(array('itemId' => (int) $itemId));
		$trackerId = $res['trackerId'];
		$status = $res['status'];

		// ---- save image list before sql query ---------------------------------
		$fieldList = $this->list_tracker_fields($trackerId, 0, -1, 'name_asc', '');
		$imgList = array();
		foreach($fieldList['data'] as $f) {
			if( $f['type'] == 'i' ) {
				$imgList[] = $this->get_item_value($trackerId, $itemId, $f['fieldId']);
			}
		}
		$watchers = $this->get_notification_emails($trackerId, $itemId, $this->get_tracker_options( $trackerId));
		if (count($watchers > 0)) {
			$smarty = TikiLib::lib('smarty');
			$trackerName = $this->trackers()->fetchOne('name', array('trackerId' => (int) $trackerId));
			$smarty->assign('mail_date', $this->now);
			$smarty->assign('mail_user', $user);
			$smarty->assign('mail_action', 'deleted');
			$smarty->assign('mail_itemId', $itemId);
			$smarty->assign('mail_trackerId', $trackerId);
			$smarty->assign('mail_trackerName', $trackerName);
			$smarty->assign('mail_data', '');
			$foo = parse_url($_SERVER["REQUEST_URI"]);
			$machine = $this->httpPrefix( true ). $foo["path"];
			$smarty->assign('mail_machine', $machine);
			$parts = explode('/', $foo['path']);
			if (count($parts) > 1)
				unset ($parts[count($parts) - 1]);
			$smarty->assign('mail_machine_raw', $this->httpPrefix( true ). implode('/', $parts));
			if (!isset($_SERVER["SERVER_NAME"])) {
				$_SERVER["SERVER_NAME"] = $_SERVER["HTTP_HOST"];
			}
			include_once ('lib/webmail/tikimaillib.php');
			$smarty->assign('server_name', $_SERVER['SERVER_NAME']);
			foreach ($watchers as $w) {
				$mail = new TikiMail($w['user']);
				$mail->setHeader("From", $prefs['sender_email']);
				$mail->setSubject($smarty->fetchLang($w['language'], 'mail/tracker_changed_notification_subject.tpl'));
				$mail->setText($smarty->fetchLang($w['language'], 'mail/tracker_changed_notification.tpl'));
				$mail->send(array($w['email']));
			}
		}

		$this->trackers()->update(array(
			'lastModif' => $this->now,
			'items' => $this->trackers()->decrement(1),
		), array('trackerId' => (int) $trackerId));

		$this->itemFields()->deleteMultiple(array('itemId' => (int) $itemId));
		$this->comments()->deleteMultiple(array(
			'object' => (int) $itemId,
			'objectType' => 'trackeritem',
		));
		$this->attachments()->deleteMultiple(array('itemId' => (int) $itemId));
		$this->items()->delete(array('itemId' => (int) $itemId));

		// ---- delete image from disk -------------------------------------
		foreach($imgList as $img) {
			if( file_exists($img) ) {
				unlink( $img );
			}
		}

		$cachelib = TikiLib::lib('cache');
		$cachelib->invalidate('trackerItemLabel'.$itemId);
		foreach($fieldList['data'] as $f) {
			$cachelib->invalidate(md5('trackerfield'.$f['fieldId'].$status));
			$cachelib->invalidate(md5('trackerfield'.$f['fieldId'].'opc'));
			$cachelib->invalidate(md5('trackerfield'.$f['fieldId'].'opc'));
			if ($status == 'o') {
				$cachelib->invalidate(md5('trackerfield'.$f['fieldId'].'op'));
				$cachelib->invalidate(md5('trackerfield'.$f['fieldId'].'oc'));
			} elseif ($status == 'c') {
				$cachelib->invalidate(md5('trackerfield'.$f['fieldId'].'oc'));
				$cachelib->invalidate(md5('trackerfield'.$f['fieldId'].'pc'));
			} elseif ($status == 'p') {
				$cachelib->invalidate(md5('trackerfield'.$f['fieldId'].'op'));
				$cachelib->invalidate(md5('trackerfield'.$f['fieldId'].'pc'));
			}
		}

		$options=$this->get_tracker_options($trackerId);
		if (isset ($option) && isset($option['autoCreateCategories']) && $option['autoCreateCategories']=='y') {
			$currentCategId=$categlib->get_category_id("Tracker Item $itemId");
			$categlib->remove_category($currentCategId);
		}
		$this->remove_object("trackeritem", $itemId);
		if (isset($options['autoCreateGroup']) && $options['autoCreateGroup'] == 'y') {
			$userlib = TikiLib::lib('user');
			$groupName = $this->groupName($options, $itemId);
			$userlib->remove_group($groupName);
		}
		$this->remove_item_log($itemId);
		$todolib = TikiLib::lib('todo');
		$todolib->delObjectTodo('trackeritem', $itemId);

		$multilinguallib = TikiLib::lib('multilingual');
		$multilinguallib->detachTranslation('trackeritem', $itemId);

		require_once('lib/search/refresh-functions.php');
		refresh_index('trackeritem', $itemId);

		return true;
	}

	// filter examples: array('fieldId'=>array(1,2,3)) to look for a list of fields
	// array('or'=>array('isSearchable'=>'y', 'isTplVisible'=>'y')) for fields that are visible ou searchable
	// array('not'=>array('isHidden'=>'y')) for fields that are not hidden
	function parse_filter($filter, &$mids, &$bindvars) {
		$tikilib = TikiLib::lib('tiki');
		foreach ($filter as $type=>$val) {
			if ($type == 'or') {
				$midors = array();
				$this->parse_filter($val, $midors, $bindvars);
				$mids[] = '('.implode(' or ', $midors).')';
			} elseif ($type == 'not') {
				$midors = array();
				$this->parse_filter($val, $midors, $bindvars);
				$mids[] = '!('.implode(' and ', $midors).')';
			} elseif ($type == 'createdBefore') {
				$mids[] = 'tti.`created` < ?';
				$bindvars[] = $val;
			} elseif ($type == 'createdAfter') {
				$mids[] = 'tti.`created` > ?';
				$bindvars[] = $val;
			} elseif ($type == 'lastModifBefore') {
				$mids[] = 'tti.`lastModif` < ?';
				$bindvars[] = $val;
			} elseif ($type == 'lastModifAfter') {
				$mids[] = 'tti.`lastModif` > ?';
				$bindvars[] = $val;
			} elseif ($type == 'notItemId') {
				$mids[] = 'tti.`itemId` NOT IN('.implode(",",array_fill(0,count($val),'?')).')';
				$bindvars = $val; 
			} elseif (is_array($val)) {
				if (count($val) > 0) {
					if (!strstr($type, '`')) $type = "`$type`";
					$mids[] = "$type in (".implode(",",array_fill(0,count($val),'?')).')';
					$bindvars = array_merge($bindvars, $val);
				}
			} else {
				if (!strstr($type, '`')) $type = "`$type`";
				$mids[] = "$type=?";
				$bindvars[] = $val;
			}
		}
	}

	// Lists all the fields for an existing tracker
	function list_tracker_fields($trackerId, $offset=0, $maxRecords=-1, $sort_mode='position_asc', $find='', $tra_name=true, $filter='', $fields='') {
		global $prefs;
		$smarty = TikiLib::lib('smarty');
		$fieldsTable = $this->fields();

		if (!empty($trackerId)) {
			$conditions = array('trackerId' => (int) $trackerId);
		}
		if ($find) {
			$conditions['name'] = $fieldsTable->like("%$find%");
		}
		if (!empty($fields)) {
			$conditions['fieldId'] = $fieldsTable->in($fields);
		}

		if (!empty($filter)) {
			$mids = array();
			$bindvars = array();
			$this->parse_filter($filter, $mids, $bindvars);
			$conditions['filter'] = $fieldsTable->expr(implode(' AND ', $mids), $bindvars);
		}

		$result = $fieldsTable->fetchAll($fieldsTable->all(), $conditions, $maxRecords, $offset, $fieldsTable->sortMode($sort_mode));
		$cant = $fieldsTable->fetchCount($conditions);

		foreach( $result as & $res ) {
			$res['options_array'] = preg_split('/\s*,\s*/', trim($res['options']));
			$res['itemChoices'] = ( $res['itemChoices'] != '' ) ? unserialize($res['itemChoices']) : array();
			$res['visibleBy'] = ($res['visibleBy'] != '') ? unserialize($res['visibleBy']) : array();
			$res['editableBy'] = ($res['editableBy'] != '') ? unserialize($res['editableBy']) : array();
			if ($tra_name && $prefs['feature_multilingual'] == 'y' && $prefs['language'] != 'en') {
				$res['name'] = tra($res['name']);
			}
			if ($res['type'] == 'p' && $res['options_array'][0] == 'language') {
				$smarty->assign('languages', $this->list_languages());	
			}
			$ret[] = $res;
		}

		return array(
			'data' => $result,
			'cant' => $cant,
		);
	}

	// Inserts or updates a tracker
	function replace_tracker($trackerId, $name, $description, $options, $descriptionIsParsed) {
		$trackers = $this->trackers();

		$data = array(
			'name' => $name,
			'description' => $description,
			'descriptionIsParsed' => $descriptionIsParsed,
			'lastModif' => $this->now,
		);

		if ($trackerId) {
			$conditions = array('trackerId' => (int) $trackerId);
			if ($trackers->fetchCount($conditions)) {
				$trackers->update($data, $conditions);
			} else {
				$data['trackerId'] = (int) $trackerId;
				$data['items'] = 0;
				$data['created'] = $this->now;
				$trackers->insert($data);
			}
		} else {
			$data['created'] = $this->now;
			$trackerId = $trackers->insert($data);
		}

		$optionTable = $this->options();
		$optionTable->deleteMultiple(array('trackerId' => (int) $trackerId));

		foreach ($options as $kopt=>$opt) {
			$optionTable->insert(array(
				'trackerId' => $trackerId,
				'name' => $kopt,
				'value' => $opt,
			));
		}

		$ratingId = $this->get_field_id_from_type($trackerId, 's', null, true, 'Rating');

		if (isset($options['useRatings']) && $options['useRatings'] == 'y') {
			if (!$ratingId) {
				$ratingId = 0;
			}

			$ratingoptions = isset($options['ratingOptions']) ? $options['ratingOptions'] : '';
			$showratings = isset($options['showRatings']) ? $options['showRatings'] : 'n';
			$this->replace_tracker_field($trackerId,$ratingId,'Rating','s','-','-',$showratings,'y','n','-',0,$ratingoptions);
		} else {
			$this->fields()->delete(array('fieldId' => (int) $ratingId));
		}
		$this->clear_tracker_cache($trackerId);

		global $prefs;
		require_once('lib/search/refresh-functions.php');
		refresh_index('trackers', $trackerId);

		if ($descriptionIsParsed == 'y') {
			$tikilib = TikiLib::lib('tiki');
			$tikilib->object_post_save(array('type'=>'tracker', 'object'=>$trackerId, 'href'=>"tiki-view_tracker.php?trackerId=$trackerId", 'description'=>$description), array( 'content' => $description ));
		}

		return $trackerId;
	}

	function clear_tracker_cache($trackerId) {
		$cachelib = TikiLib::lib('cache');

		foreach ($this->get_all_tracker_items($trackerId) as $itemId) {
		    $cachelib->invalidate('trackerItemLabel'.$itemId);
		}
	}


	function replace_tracker_field($trackerId, $fieldId, $name, $type, $isMain, $isSearchable, $isTblVisible, $isPublic, $isHidden, $isMandatory, $position, $options, $description='',$isMultilingual='', $itemChoices=null, $errorMsg='', $visibleBy=null, $editableBy=null, $descriptionIsParsed='n', $validation='', $validationParam='', $validationMessage='', $permName = null) {
		// Serialize choosed items array (items of the tracker field to be displayed in the list proposed to the user)
		if ( is_array($itemChoices) && count($itemChoices) > 0 && !empty($itemChoices[0]) ) {
			$itemChoices = serialize($itemChoices);
		} else {
			$itemChoices = '';
		}
		if (is_array($visibleBy) && count($visibleBy) > 0 && !empty($visibleBy[0])) {
			$visibleBy = serialize($visibleBy);
		} else {
			$visibleBy = '';
		}
		if (is_array($editableBy) && count($editableBy) > 0 && !empty($editableBy[0])) {
			$editableBy = serialize($editableBy);
		} else {
			$editableBy = '';
		}

		$fields = $this->fields();

		$data = array(
			'name' => $name,
			'permName' => empty($permName) ? null : $permName,
			'type' => $type,
			'isMain' => $isMain,
			'isSearchable' => $isSearchable,
			'isTblVisible' => $isTblVisible,
			'isPublic' => $isPublic,
			'isHidden' => $isHidden,
			'isMandatory' => $isMandatory,
			'position' => (int) $position,
			'options' => $options,
			'isMultilingual' => $isMultilingual,
			'description' => $description,
			'itemChoices' => $itemChoices,
			'errorMsg' => $errorMsg,
			'visibleBy' => $visibleBy,
			'editableBy' => $editableBy,
			'descriptionIsParsed' => $descriptionIsParsed,
			'validation' => $validation,
			'validationParam' => $validationParam,
			'validationMessage' => $validationMessage,
		);

		if ($fieldId) {
			// -------------------------------------
			// remove images when needed
			$old_field = $this->get_tracker_field($fieldId);
			if (!empty($old_field['fieldId'])) {
				if( $old_field['type'] == 'i' && $type != 'i' ) {
					$this->remove_field_images( $fieldId );
				}

				$fields->update($data, array('fieldId' => (int) $fieldId));
			} else {
				$data['trackerId'] = (int) $trackerId;
				$data['fieldId'] = (int) $fieldId;
				$fields->insert($data);
			}
		} else {
			$data['trackerId'] = (int) $trackerId;
			$fieldId = $fields->insert($data);
			
			if (! $permName) {
				// Apply a default value to perm name when not specified
				$fields->update(array(
					'permName' => 'f_' . $fieldId,
				), array(
					'fieldId' => $fieldId,
				));
			}

			$itemFields = $this->itemFields();
			foreach ($this->get_all_tracker_items($trackerId) as $itemId) {
				$itemFields->deleteMultiple(array('itemId' => (int) $itemId, 'fieldId' => $fieldId));
				$itemFields->insert(array(
					'itemId' => (int) $itemId,
					'fieldId' => (int) $fieldId,
					'value' => '',
				));
			}
		}

		$this->clear_tracker_cache($trackerId);
		return $fieldId;
	}

	function replace_rating($trackerId,$itemId,$fieldId,$user,$new_rate) {
		global $tiki_p_tracker_vote_ratings, $tiki_p_tracker_revote_ratings;
		$itemFields = $this->itemFields();

		if ($new_rate === NULL) {
			$new_rate = 0;
		}

		if ($tiki_p_tracker_vote_ratings != 'y') {
			return;
		}
		$key = "tracker.$trackerId.$itemId";
		$olrate = $this->get_user_vote($key,$user);
		if ($tiki_p_tracker_revote_ratings != 'y' && ($olrate !== null && $olrate !== false)) {
			return;
		}
		$count = $itemFields->fetchCount(array('itemId' => (int) $itemId, 'fieldId' => (int) $fieldId));
		$this->register_user_vote( $user, $key, $new_rate, array(), true );
		if (!$count) {
			$itemFields->insert(array(
				'value' => (int) $new_rate,
				'itemId' => (int) $itemId,
				'fieldId' => (int) $fieldId,
			));
			return $new_rate;
		} else {
			if ($olrate === NULL) {
				$olrate = 0;
			}

			$conditions = array(
				'itemId' => (int) $itemId,
				'fieldId' => (int) $fieldId,
			);

			$val = $itemFields->fetchOne('value', $conditions);
			$newval = $val - $olrate + $new_rate;

			$itemFields->update(array(
				'value' => $newval,
			), $conditions);

			return $newval;
		}
	}

	function replace_star($userValue, $trackerId, $itemId, &$field, $user, $updateField=true) {
		global $tiki_p_tracker_vote_ratings, $tiki_p_tracker_revote_ratings; 
		if ($field['type'] != '*' && $field['type'] != 'STARS') {
			return;
		} 
		if ($userValue != 'NULL' && isset($field['rating_options']) && !in_array($userValue, $field['rating_options'])) {
			return;
		}
		if ($userValue != 'NULL' && !isset($field['rating_options']) && !in_array($userValue, $field['options_array'])) {
			// backward compatability with trackerlist rating which does not have rating options
			return;
		} 
		if ($tiki_p_tracker_vote_ratings != 'y') {
			return;
		}
		$key = "tracker.$trackerId.$itemId.".$field['fieldId'];
		if ($tiki_p_tracker_revote_ratings != 'y' && (($v = $this->get_user_vote($key, $user)) !== null && $v !== false)) {
			return;
		}
		
		$this->register_user_vote($user, $key, $userValue, array(), true);

		$votings = $this->table('tiki_user_votings');
		$data = $votings->fetchRow(array(
			'count' => $votings->count(),
			'total' => $votings->sum('optionId'),
		), array('id' => $key));
		$field['numvotes'] = $data['count'];
		$field['my_rate'] = $userValue;
		$field['voteavg'] = $field['value'] = $data['total'] / $field['numvotes'];
	}
	function update_star_field($trackerId, $itemId, &$field) {
		global $user;
		$votings = $this->table('tiki_user_votings');

		if ($field['type'] == 's' && $field['name'] == tra('Rating')) { // global rating to an item - value is the sum of the votes
			$key = 'tracker.'.$trackerId.'.'.$itemId; 
		} elseif ($field['type'] == '*' || $field['type'] == 'STARS') { // field rating - value is the average of the votes
			$key = "tracker.$trackerId.$itemId.".$field['fieldId']; 
		}
		$data = $votings->fetchRow(array(
                        'count' => $votings->count(),
                        'total' => $votings->sum('optionId'),
                ), array('id' => $key));
		$field['numvotes'] = $data['count'];
		$total = $data['total']; 
		$field['voteavg'] = $total/$field['numvotes'];
		// be careful optionId is the value - not the optionId
		$field['my_rate'] = $votings->fetchOne('optionId', array('id' => $key, 'user' => $user));
	}

	function remove_tracker($trackerId) {

		// ---- delete image from disk -------------------------------------
		$fieldList = $this->list_tracker_fields($trackerId, 0, -1, 'name_asc', '');
		foreach($fieldList['data'] as $f) {
			if( $f['type'] == 'i' ) {
				$this->remove_field_images($f['fieldId']);
			}
		}

		$options=$this->get_tracker_options($trackerId);
		if (isset ($option) && isset($option['autoCreateCategories']) && $option['autoCreateCategories']=='y') {
			$categlib = TikiLib::lib('categ');
			$currentCategId=$categlib->get_category_id("Tracker $trackerId");
			$categlib->remove_category($currentCategId);
		}

		foreach ($this->get_all_tracker_items($trackerId) as $itemId) {
			$this->remove_tracker_item($itemId);
		}

		$conditions = array(
			'trackerId' => (int) $trackerId,
		);

		$this->fields()->deleteMultiple($conditions);
		$this->options()->deleteMultiple($conditions);
		$this->trackers()->delete($conditions);

		$this->remove_object('tracker', $trackerId);

		$this->clear_tracker_cache($trackerId);

		return true;
	}

	function remove_tracker_field($fieldId,$trackerId) {
		$cachelib = TikiLib::lib('cache');
		$logslib = TikiLib::lib('logs');

		// -------------------------------------
		// remove images when needed
		$field = $this->get_tracker_field($fieldId);
		if( $field['type'] == 'i' ) {
			$this->remove_field_images($fieldId);
		}

		$conditions = array(
			'fieldId' => (int) $fieldId,
		);

		$this->fields()->delete($conditions);
		$this->itemFields()->deleteMultiple($conditions);

		$cachelib->invalidate(md5('trackerfield'.$fieldId.'o'));
		$cachelib->invalidate(md5('trackerfield'.$fieldId.'p'));
		$cachelib->invalidate(md5('trackerfield'.$fieldId.'c'));
		$cachelib->invalidate(md5('trackerfield'.$fieldId.'op'));
		$cachelib->invalidate(md5('trackerfield'.$fieldId.'oc'));
		$cachelib->invalidate(md5('trackerfield'.$fieldId.'pc'));
		$cachelib->invalidate(md5('trackerfield'.$fieldId.'opc'));
		$cachelib->invalidate(md5('trackerfield'.$fieldId.'poc'));

		$this->clear_tracker_cache($trackerId);
		$logslib->add_log('admintrackerfields', 'removed tracker field ' . $fieldId . ' from tracker ' . $trackerId);

		return true;
	}

	/**
	 * Returns the trackerId of the tracker possessing the item ($itemId)
	 */
	function get_tracker_for_item($itemId) {
		return $this->items()->fetchOne('trackerId', array('itemId' => (int) $itemId));
	}

	function get_tracker_options($trackerId) {
		return $this->options()->fetchMap('name', 'value', array('trackerId' => (int) $trackerId));
	}

	function get_trackers_options($trackerId, $option='', $find='', $not='') {
		$options = $this->options();
		$conditions = array();

		if (!empty($trackerId)) {
			$conditions['trackerId'] = (int) $trackerId;
		}

		if (!empty($option)) {
			$conditions['name'] = $option;
		}

		if ($not == 'null' || $not == 'empty') {
			$conditions['value'] = $options->not('');
		}

		if (!empty($find)) {
			$conditions['value'] = $options->like("%$find%");
		}

		return $options->fetchAll($options->all(), $conditions);
	}

	function get_tracker_field($fieldId) {
		$res = $this->fields()->fetchFullRow(array('fieldId' => (int) $fieldId));
		$res['options_array'] = preg_split('/,/', $res['options']);
		$res['itemChoices'] = ( $res['itemChoices'] != '' ) ? unserialize($res['itemChoices']) : array();
		$res['visibleBy'] = ($res['visibleBy'] != '') ? unserialize($res['visibleBy']) : array();
		$res['editableBy'] = ($res['editableBy'] != '') ? unserialize($res['editableBy']) : array();
		return $res;
	}

	function get_field_id($trackerId,$name) {
		return $this->fields()->fetchOne('fieldId', array(
			'trackerId' => (int) $trackerId,
			'name' => $name,
		));
	}

	function get_field_id_from_type($trackerId, $type, $option=NULL, $first=true, $name=null) {
		static $memo;
		if (!is_array($type) && isset($memo[$trackerId][$type][$option])) {
			return $memo[$trackerId][$type][$option];
		}

		$conditions = array(
			'trackerId' => (int) $trackerId,
		);
		$fields = $this->fields();

		if (is_array($type)) {
			$conditions['type'] = $fields->in($type, true);
		} else {
			$conditions['type'] = $fields->exactly($type);
		}

		if (!empty($option)) {
			$conditions['options'] = $fields->like($option);
		}

		if (!empty($name)) {
			$conditions['name'] = $name;
		}

		if ($first) {
			$fieldId = $fields->fetchOne('fieldId', $conditions);
			$memo[$trackerId][$type][$option] = $fieldId;
			return $fieldId;
		} else {
			return $fields->fetchColumn('fieldId', $conditions);
		}
	}

/*
** function only used for the popup for more infos on attachements
*  returns an array with field=>value
*/
	function get_moreinfo($attId) {
		$query = "select o.`value`, o.`trackerId` from `tiki_tracker_options` o";
		$query.= " left join `tiki_tracker_items` i on o.`trackerId`=i.`trackerId` ";
		$query.= " left join `tiki_tracker_item_attachments` a on i.`itemId`=a.`itemId` ";
		$query.= " where a.`attId`=? and o.`name`=?";
		$result = $this->query($query,array((int)$attId, 'orderAttachments'));
		$resu = $result->fetchRow();
		if ($resu) {
			$resu['orderAttachments'] = $resu['value'];
		} else {
			$query = "select `orderAttachments`, t.`trackerId` from `tiki_trackers` t ";
			$query.= " left join `tiki_tracker_items` i on t.`trackerId`=i.`trackerId` ";
			$query.= " left join `tiki_tracker_item_attachments` a on i.`itemId`=a.`itemId` ";
			$query.= " where a.`attId`=? ";
			$result = $this->query($query,array((int)$attId));
			$resu = $result->fetchRow();
		}
		if (strstr($resu['orderAttachments'],'|')) {
			$fields = preg_split('/,/',substr($resu['orderAttachments'],strpos($resu['orderAttachments'],'|')+1));
			$res = $this->attachments()->fetchRow($fields, array('attId' => (int) $attId));
			$res["trackerId"] = $resu['trackerId'];
			$res["longdesc"] = isset($res['longdesc'])?$this->parse_data($res['longdesc']):'';
		} else {
			$res = array(tra("Message") => tra("No extra information for that attached file. "));
			$res['trackerId'] = 0;
		}
		return $res;
	}

	function field_types() {

		$types = array();

		$factory = new Tracker_Field_Factory(false);
		foreach ($factory->getFieldTypes() as $key => $info) {
			$types[$key] = array(
				'label' => $info['name'],
				'opt' => count($info['params']) === 0,
				'help' => $this->build_help_for_type($info),
			);
		}

		return $types;
	}

	private function build_help_for_type($info)
	{
		$function = tr('Function');
		$text = "<p><strong>$function:</strong> {$info['description']}</p>";

		if (count($info['params'])) {
			$text .= '<dl>';
			foreach ($info['params'] as $key => $param) {
				if (isset($param['count'])) {
					$text .= "<dt>{$param['name']}[{$param['count']}]</dt>";
				} else {
					$text .= "<dt>{$param['name']}</dt>";
				}

				$text .= "<dd>{$param['description']}</dd>";

				if (isset($param['options'])) {
					$text .= "<dd><ul>";
					foreach ($param['options'] as $k => $label) {
						$text .= "<li><strong>{$k}</strong> = <em>$label</em></li>";
					}
					$text .= "</ul></dd>";
				}
			}
			$text .= '</dl>';
		}

		return "<div>{$text}</div>";
	}

	function status_types() {
		$status['o'] = array('label'=>tra('open'),'perm'=>'tiki_p_view_trackers','image'=>'img/icons2/status_open.gif');
		$status['p'] = array('label'=>tra('pending'),'perm'=>'tiki_p_view_trackers_pending','image'=>'img/icons2/status_pending.gif');
		$status['c'] = array('label'=>tra('closed'),'perm'=>'tiki_p_view_trackers_closed','image'=>'img/icons2/status_closed.gif');
		return $status;
	}

	function get_isMain_value($trackerId, $itemId) {
	    global $prefs;

	    $query = "select tif.`value` from `tiki_tracker_item_fields` tif, `tiki_tracker_items` i, `tiki_tracker_fields` tf where i.`itemId`=? and i.`itemId`=tif.`itemId` and tf.`fieldId`=tif.`fieldId` and tf.`isMain`=? ";
		$result = $this->getOne($query, array( (int)$itemId, "y"));

		if ($result{0} === '{') {
			$result = json_decode($result, true);
			if (isset($result[$prefs['language']])) {
				return $result[$prefs['language']];
			} else {
				return reset($result);
			}
		}

		return $result;
	}
	function get_main_field($trackerId) {
		return $this->fields()->fetchOne('fieldId', array('isMain' => 'y', 'trackerId' => $trackerId));
	}

	function categorized_item($trackerId, $itemId, $mainfield, $ins_categs, $parent_categs_only = array()) {
		$categlib = TikiLib::lib('categ');
		$cat_type = "trackeritem";
		$cat_objid = $itemId;
		$cat_desc = '';
		if (empty($mainfield))
				$cat_name = $itemId;
		else
				$cat_name = $mainfield;
		$cat_href = "tiki-view_tracker_item.php?trackerId=$trackerId&itemId=$itemId";
		// The following needed to ensure category field exist for item (to be readable by list_items)

		$definition = Tracker_Definition::get($trackerId);
		foreach($definition->getCategorizedFields() as $t) {
			$this->itemFields()->insert(array(
				'itemId' => $itemId,
				'fieldId' => $t,
				'value' => '',
			), true);
		}
		$old_categs = $categlib->get_object_categories('trackeritem', $itemId);
		if (is_array($ins_categs)) {
			$new_categs = array_diff($ins_categs, $old_categs);
			$del_categs = array_diff($old_categs, $ins_categs);
			if (!empty($parent_categs_only)) {
				// put back categories that were not meant to be deleted (e.g. Tracker plugin)
				foreach($del_categs as $d) {
					$parentId = $categlib->get_category_parent($d);
					if (!in_array($parentId, $parent_categs_only)) {
						$undel_categs[] = $d;
					}
				}
				if (isset($undel_categs)) {
					$del_categs = array_diff($del_categs, $undel_categs);
				}
			}
			$remain_categs = array_diff($old_categs, $new_categs, $del_categs);
			$ins_categs = array_merge($remain_categs, $new_categs);
		}
		$categlib->update_object_categories($ins_categs, $cat_objid, $cat_type, $cat_desc, $cat_name, $cat_href);
	}
	function move_up_last_fields($trackerId, $fieldId, $delta=1) {
		$type = ($delta > 0) ? 'increment' : 'decrement';

		$this->fields()->update(array(
			'position' => $this->fields()->$type(abs($delta)),
		), array(
			'trackerId' => (int) $trackerId,
			'fieldId' => (int) $fieldId,
		));
	}
	/* list all the values of a field
	 */
	function list_tracker_field_values($trackerId, $fieldId, $status='o', $distinct='y', $lang='', $exceptItemId='') {
		$mid = '';
		$bindvars[] = (int)$fieldId;
		if (!$this->getSqlStatus($status, $mid, $bindvars, $trackerId)) {
			return null;
		}
		$sort_mode = "value_asc";
		$distinct = $distinct == 'y'?'distinct': '';
		if (!empty($exceptItemId)) {
			$mid .= ' and ttif.`itemId` != ? ';
			$bindvars[] = $exceptItemId;
		}
		$query = "select $distinct(ttif.`value`) from `tiki_tracker_item_fields` ttif, `tiki_tracker_items` tti where tti.`itemId`= ttif.`itemId`and ttif.`fieldId`=? $mid order by ".$this->convertSortMode($sort_mode);
		$result = $this->query( $query, $bindvars);
		$ret = array();
		while ($res = $result->fetchRow()) {
			$ret[] = $res['value'];
		}
		return $ret;
	}

	/* tests if a value exists in a field
	 */
	function check_field_value_exists($value, $fieldId, $exceptItemId = 0) {
		$fields = $this->fields();

		$conditions = array(
			'fieldId' => (int) $fieldId,
			'value' => $value,
		);
		
		if ($exceptItemId > 0) {
			$conditions['itemId'] = $fields->not((int) $exceptItemId);
		}

		return $fields->fetchCount($conditions) > 0;
	}

	function is_multilingual($fieldId){
		global $prefs;

		if ($fieldId<1) {
			return 'n';
		}

		if ($prefs['feature_multilingual'] != 'y') {
			return 'n';
		}

		$is = $this->fields()->fetchOne('isMultilingual', array('fieldId' => (int) $fieldId));

		return ($is == 'y') ? 'y' : 'n';
	}

	/* return the values of $fieldIdOut of an item that has a value $value for $fieldId */
	function get_filtered_item_values($fieldId, $value, $fieldIdOut) {
		$query = "select ttifOut.`value` from `tiki_tracker_item_fields` ttifOut, `tiki_tracker_item_fields` ttif
			where ttifOut.`itemId`= ttif.`itemId`and ttif.`fieldId`=? and ttif.`value`=? and ttifOut.`fieldId`=?";
		$bindvars = array($fieldId, $value, $fieldIdOut);
		$result = $this->query($query, $bindvars);
		$ret = array();
		while ($res = $result->fetchRow()) {
			$ret[] = $res['value'];
		}
		return $ret;
	}
	/* look if a tracker has only one item per user and if an item has already being created for the user  or the IP*/
	function get_user_item(&$trackerId, $trackerOptions, $userparam=null, $user= null, $status='') {
		global $prefs;
		$tikilib = TikiLib::lib('tiki');
		$userlib = TikiLib::lib('user');
		if (empty($user)) {
			$user = $GLOBALS['user'];
		}
		if (empty($trackerId) && $prefs['userTracker'] == 'y') {
			$utid = $userlib->get_tracker_usergroup($user);
			if (!empty($utid['usersTrackerId'])) {
				$trackerId = $utid['usersTrackerId'];
				$itemId = $this->get_item_id($trackerId, $utid['usersFieldId'], $user);
			}
			return $itemId;
		}
		$userreal=$userparam!=null?$userparam:$user;
		if (!empty($userreal)) {
			if ($fieldId = $this->get_field_id_from_type($trackerId, 'u', '1%')) { // user creator field
				$value = $userreal;
				$items = $this->get_items_list($trackerId, $fieldId, $value, $status);
				if (!empty($items))
					return $items[0];
			}
		}
		if ($fieldId = $this->get_field_id_from_type($trackerId, 'I', '1')) { // IP creator field
			$IP = $tikilib->get_ip_address();
			$items = $this->get_items_list($trackerId, $fieldId, $IP, $status);
			if (!empty($items))
				return $items[0];
			else
				return 0;
		}
	}
	function get_item_creator($trackerId, $itemId) {
		if ($fieldId = $this->get_field_id_from_type($trackerId, 'u', '1%')) { // user creator field
			return $this->get_item_value($trackerId, $itemId, $fieldId);
		} else {
			return null;
		}
	}
	function get_item_group_creator($trackerId, $itemId) {
		if ($fieldId = $this->get_field_id_from_type($trackerId, 'g', '1%')) { // group creator field
			return $this->get_item_value($trackerId, $itemId, $fieldId);
		} else {
			return null;
		}
	}
	/* find the best fieldwhere you can do a filter on the initial
	 * 1) if sort_mode and sort_mode is a text and the field is visible
	 * 2) the first main taht is text
	 */
	function get_initial_field($list_fields, $sort_mode) {
		if (preg_match('/^f_([^_]*)_?.*/', $sort_mode, $matches)) {
			if (isset($list_fields[$matches[1]])) {
				$type = $list_fields[$matches[1]]['type'];
				if (in_array($type, array('t', 'a', 'm')))
					return $matches[1];
			}
		}
		foreach($list_fields as $fieldId=>$field) {
			if ($field['isMain'] == 'y' && in_array($field['type'], array('t', 'a', 'm'))) {
				return $fieldId;
			}
		}
	}
	function get_nb_items($trackerId) {
		return $this->items()->fetchCount(array('trackerId' => (int) $trackerId));
	}
	function duplicate_tracker($trackerId, $name, $description = '', $descriptionIsParsed = 'n') {
		$tracker_info = $this->get_tracker($trackerId);

		if ($options = $this->get_tracker_options($trackerId)) {
			$tracker_info = array_merge($tracker_info,$options);
		} else {
			$options = array();
		}

		$newTrackerId = $this->replace_tracker(0, $name, $description, array(), $descriptionIsParsed);
		$fields = $this->list_tracker_fields($trackerId, 0, -1, 'position_asc', '');
		foreach($fields['data'] as $field) {
			$newFieldId = $this->replace_tracker_field($newTrackerId, 0, $field['name'], $field['type'], $field['isMain'], $field['isSearchable'], $field['isTblVisible'], $field['isPublic'], $field['isHidden'], $field['isMandatory'], $field['position'], $field['options'], $field['description'], $field['isMultilingual'], $field['itemChoices']);
			if ($options['defaultOrderKey'] == $field['fieldId']) {
				$options['defaultOrderKey'] = $newFieldId;
			}
		}

		foreach ($options as $name=>$val) {
			$this->options()->insert(array(
				'trackerId' => $newTrackerId,
				'name' => $name,
				'value' => $val,
			));
		}
		return $newTrackerId;
	}
	function get_notification_emails($trackerId, $itemId, $options, $status='', $oldStatus='') {
		global $prefs, $user;
		$watchers_global = $this->get_event_watches('tracker_modified',$trackerId);
		$watchers_local = $this->get_local_notifications($itemId, $status, $oldStatus);
		$watchers_item = $itemId ? $this->get_event_watches('tracker_item_modified',$itemId, array('trackerId'=>$trackerId)) : array();
		
		// use daily reports feature only if tracker item has been added or updated
		if ($prefs['feature_daily_report_watches'] == 'y' && !empty($status)) {
			$reportslib = TikiLib::lib('reports');
			$reportslib->makeReportCache($watchers_global,
				array('event' => 'tracker_item_modified', 'itemId' => $itemId, 'trackerId' => $trackerId, 'user' => $user)
			);
			$reportslib->makeReportCache($watchers_item,
				array('event' => 'tracker_item_modified', 'itemId' => $itemId, 'trackerId' => $trackerId, 'user' => $user)
			);
		}
		
		$watchers_outbound = array();
		if( array_key_exists( "outboundEmail", $options ) && $options["outboundEmail"] ) {
			$emails3 = preg_split('/,/', $options['outboundEmail']);
			foreach ($emails3 as $w) {
				global $user_preferences;
				$userlib = TikiLib::lib('user');
				$u = $userlib->get_user_by_email($w);
				$this->get_user_preferences($u, array('user', 'language', 'mailCharset'));
				if (empty($user_preferences[$u]['language'])) $user_preferences[$u]['language'] = $prefs['site_language'];
				if (empty($user_preferences[$u]['mailCharset'])) $user_preferences[$u]['mailCharset'] = $prefs['users_prefs_mailCharset'];
				$watchers_outbound[] = array('email'=>$w, 'user'=>$u, 'language'=>$user_preferences[$u]['language'], 'mailCharset'=>$user_preferences[$u]['mailCharset']);
			}
		}

		$emails = array();
		$watchers = array();
		foreach (array('watchers_global', 'watchers_local', 'watchers_item', 'watchers_outbound') as $ws) {
			if (!empty($$ws)) {
				foreach($$ws as $w) {
					$wl = strtolower($w['email']);
					if (!in_array($wl, $emails)) {
						$emails[] = $wl;
						$watchers[] = $w;
					}
				}
			}
		}
		return $watchers;
	}
	/* sort allFileds function of a list of fields */
	function sort_fields($allFields, $listFields) {
		$tmp = array();
		foreach ($listFields as $fieldId) {
			if (substr($fieldId, 0, 1) == '-') {
				$fieldId = substr($fieldId, 1);
			}
			foreach ($allFields['data'] as $i=>$field) {
				if ($field['fieldId'] == $fieldId && $field['fieldId']) {
					$tmp[] = $field;
					$allFields['data'][$i]['fieldId'] = 0;
					break;
				}
			}
		}
		// do not forget the admin fields like user selector
		foreach ($allFields['data'] as $field) {
			if ($field['fieldId']) {
				$tmp[] = $field;
			}
		}
		$allFields['data'] = $tmp;
		$allFields['cant'] = count($tmp);
		return $allFields;
	}
	/* return all the values+field options  of an item for a type field (ex: return all the user selector value for an item) */
	function get_item_values_by_type($itemId, $typeField) {
		$query = "select ttif.`value`, ttf.`options` from `tiki_tracker_fields` ttf, `tiki_tracker_item_fields` ttif";
		$query .= " where ttif.`itemId`=? and ttf.`type`=? and ttf.`fieldId`=ttif.`fieldId`";
		$ret = $this->fetchAll($query, array($itemId, $typeField));
		foreach ( $ret as &$res ) {
			$res['options_array'] = preg_split('/,/', $res['options']);
		}
		return $ret;
	}
	/* return all the emails that are locally watching an item */
	function get_local_notifications($itemId, $status='', $oldStatus='') {
		global $user_preferences, $prefs;
		$tikilib = TikiLib::lib('tiki');
		$userlib = TikiLib::lib('user');
		$emails = array();
		// user field watching item
		$res = $this->get_item_values_by_type($itemId, 'u');
		if (is_array($res)) {
			foreach ($res as $f) {
				if (isset($f['options_array'][1]) && $f['options_array'][1] == 1) {
					$tikilib->get_user_preferences($f['value'], array('email', 'user', 'language', 'mailCharset'));
					$emails[] = array('email'=>$userlib->get_user_email($f['value']), 'user'=>$f['value'], 'language'=>$user_preferences[$f['value']]['language'], 'mailCharset'=>$user_preferences[$f['value']]['mailCharset']);
				}
			}
		}
		// email field watching status change
		if ($status != $oldStatus) {
			$res = $this->get_item_values_by_type($itemId, 'm');
			if (is_array($res)) {
				foreach ($res as $f) {
					if ((isset($f['options_array'][1]) && $f['options_array'][1] == 'o' && $status == 'o')
						|| (isset($f['options_array'][2]) && $f['options_array'][2] == 'p' && $status == 'p')
						|| (isset($f['options_array'][3]) && $f['options_array'][3] == 'c' && $status == 'c')) {
						$emails[] = array('email'=> $f['value'], 'user'=>'', 'language'=>$prefs['language'], 'mailCharset'=>$prefs['users_prefs_mailCharset'], 'action'=>'status');
					}
				}
			}
		}
		return $emails;
	}
	function get_join_values($trackerId, $itemId, $fieldIds, $finalTrackerId='', $finalFields='', $separator=' ', $status='') {
		$smarty = TikiLib::lib('smarty');
		$select[] = "`tiki_tracker_item_fields` t0";
		$where[] = " t0.`itemId`=?";
		$bindVars[] = $itemId;
		$mid = '';
		for ($i = 0, $tmp_count = count($fieldIds) - 1 ; $i < $tmp_count ; $i += 2) {
			$j = $i + 1;
			$k = $j + 1;
			$select[] = "`tiki_tracker_item_fields` t$j";
			$select[] = "`tiki_tracker_item_fields` t$k";
			$where[] = "t$i.`value`=t$j.`value` and t$i.`fieldId`=? and t$j.`fieldId`=?";
			$bindVars[] = $fieldIds[$i];
			$bindVars[] = $fieldIds[$j];
			$where[] = "t$j.`itemId`=t$k.`itemId` and t$k.`fieldId`=?";
			$bindVars[] = $fieldIds[$k];
		}
		if (!empty($status)) {
			$this->getSqlStatus($status, $mid, $bindVars, $trackerId);
			$select[] = '`tiki_tracker_items` tti';
			$mid .= " and tti.`itemId`=t$k.`itemId`";
		}
		$query = "select t$k.* from ".implode(',',$select).' where '.implode(' and ',$where).$mid;
		$result = $this->query($query, $bindVars);
		$ret = array();
		while ($res = $result->fetchRow()) {
			$field_value['value'] = $res['value'];
			$field_value['trackerId'] = $trackerId;
			$field_value['type'] = $this->fields()->fetchOne('type', array(
				'fieldId' => (int) $res['fieldId'],
			));
			if (!$field_value['type']) {
				$ret[$res['itemId']] = tra('Tracker field setup error - display field not found: ') . '#' . $res['fieldId'];
			} else {
				$ret[$res['itemId']] = $this->get_field_handler($field_value, $res)->renderOutput(array(
					'showlinks' => 'n',
					'list_mode' => 'n',
				));
			}
			if (is_array($finalFields) && count($finalFields)) {
				$i = 0;
				foreach ($finalFields as $f) {
					if (!$i++)
						continue;
					$field_value = $this->get_tracker_field($f);
					$ff = $this->get_item_value($finalTrackerId, $res['itemId'], $f);;
					$field_value['value'] = $ff;
					$ret[$res['itemId']] = $this->get_field_handler($field_value, $res)->renderOutput(array(
						'showlinks' => 'n',
					));
				}
			}
		}
		return $ret;
	}
	function get_left_join_sql($fieldIds) {
		$sql = '';
		for ($i = 0, $tmp_count = count($fieldIds); $i < $tmp_count; $i += 3) {
			$j = $i + 1;
			$k = $j + 1;
			$tti = $i ? "t$i" : 'tti';
			$sttif = $k < $tmp_count - 1 ? "t$k" : 'sttif';
			$sql .= " LEFT JOIN (`tiki_tracker_item_fields` t$i) ON ($tti.`itemId`= t$i.`itemId` and t$i.`fieldId`=".$fieldIds[$i].")";
			$sql .= " LEFT JOIN (`tiki_tracker_item_fields` t$j) ON (t$i.`value`= t$j.`value` and t$j.`fieldId`=".$fieldIds[$j].")";
			$sql .= " LEFT JOIN (`tiki_tracker_item_fields` $sttif) ON (t$j.`itemId`= $sttif.`itemId` and $sttif.`fieldId`=".$fieldIds[$k].")";
		}
		return $sql;
	}
	function get_item_info($itemId) {
		return $this->items()->fetchFullRow(array('itemId' => (int) $itemId));
	}
	function rename_page($old, $new) {
		$query = "update `tiki_tracker_item_fields` ttif left join `tiki_tracker_fields` ttf on (ttif.fieldId = ttf.fieldId) set ttif.`value`=? where ttif.`value`=? and ttf.`type` = ?";
		$this->query($query, array($new, $old, 'k'));
	}
	function build_date($input, $format, $ins_id) {
		if (is_array($format)) {
			$format = $format['options_array'][0];
		}

		$tikilib = TikiLib::lib('tiki');
		$value = '';
		$monthIsNull = empty($input[$ins_id.'Month']) || $input[$ins_id.'Month'] == null || $input[$ins_id.'Month'] == 'null'|| $input[$ins_id.'Month'] == '';
		$dayIsNull = empty($input[$ins_id.'Day']) || $input[$ins_id.'Day'] == null || $input[$ins_id.'Day'] == 'null' || $input[$ins_id.'Day'] == '';
		$yearIsNull = empty($input[$ins_id.'Year']) || $input[$ins_id.'Year'] == null || $input[$ins_id.'Year'] == 'null' || $input[$ins_id.'Year'] == '';
		$hourIsNull = !isset($input[$ins_id.'Hour']) || $input[$ins_id.'Hour'] == null || $input[$ins_id.'Hour'] == 'null' || $input[$ins_id.'Hour'] == ''|| $input[$ins_id.'Hour'] == ' ';
		$minuteIsNull = empty($input[$ins_id.'Minute']) || $input[$ins_id.'Minute'] == null || $input[$ins_id.'Minute'] == 'null' || $input[$ins_id.'Minute'] == '' || $input[$ins_id.'Minute'] == ' ';
		if ($format == 'd') {
			if ($monthIsNull || $dayIsNull || $yearIsNull) { // all the values must be blank
				$value = '';
			} else {
				$value = $tikilib->make_time(0, 0, 0, $input[$ins_id.'Month'], $input[$ins_id.'Day'], $input[$ins_id.'Year']);
			}
		} elseif ($format == 't') { // all the values must be blank
			if ($hourIsNull || $minuteIsNull) {
				$value = '';
			} else {
				//if (isset($input[$ins_id.'Meridian']) && $input[$ins_id.'Meridian'] == 'pm') $input[$ins_id.'Hour'] += 12;
				$now = $tikilib->now;
				//Convert 12-hour clock hours to 24-hour scale to compute time
				if (isset($input[$ins_id.'Meridian'])) {
					$input[$ins_id.'Hour'] = date('H', strtotime($input[$ins_id.'Hour'] . ':00 ' . $input[$ins_id.'Meridian']));
				}
				$value = $tikilib->make_time($input[$ins_id.'Hour'], $input[$ins_id.'Minute'], 0, $tikilib->date_format("%m", $now), $tikilib->date_format("%d", $now), $tikilib->date_format("%Y", $now));
			}
		} else {
			if ($monthIsNull || $dayIsNull || $yearIsNull || $hourIsNull || $minuteIsNull) { // all the values must be blank
				$value = '';
			} else {
				//if (isset($input[$ins_id.'Meridian']) && $input[$ins_id.'Meridian'] == 'pm') $input[$ins_id.'Hour'] += 12;
				//Convert 12-hour clock hours to 24-hour scale to compute time
				if (isset($input[$ins_id.'Meridian'])) {
					$input[$ins_id.'Hour'] = date('H', strtotime($input[$ins_id.'Hour'] . ':00 ' . $input[$ins_id.'Meridian']));
				}
				$value = $tikilib->make_time($input[$ins_id.'Hour'], $input[$ins_id.'Minute'], 0, $input[$ins_id.'Month'], $input[$ins_id.'Day'], $input[$ins_id.'Year']);
			}
		}
		return $value;
	}
	/* get the fields from the pretty tracker template
	* return a list of fieldIds */
	function get_pretty_fieldIds($resource, $type='wiki', &$outputPretty) {
		$tikilib = TikiLib::lib('tiki');
		$smarty = TikiLib::lib('smarty');
		if ($type == 'wiki') {
			$wiki_info = $tikilib->get_page_info($resource);
			if (!empty($wiki_info)) {
				$f = $wiki_info['data'];
			}
		} else {
			$resource_name = $smarty->get_filename($resource);
			$f = $smarty->_read_file($resource_name);
		}
		if (!empty($f)) {
			preg_match_all('/\$f_([0-9]+)(\|output)?/', $f, $matches);
			foreach ($matches[2] as $i=>$val) {
				if (!empty($val))
					$outputPretty[] = $matches[1][$i];
			}
			return $matches[1];
		}
		return array();
	}
	
	/**
	 * @param mixed $value		string or array to process
	 */
	function replace_pretty_tracker_refs( &$value ) {
		$smarty = TikiLib::lib('smarty');
		
		if( is_array( $value ) ) {
			foreach( $value as &$v ) {
				$this->replace_pretty_tracker_refs( $v );
			}
		} else {
			// array syntax for callback function needed for some versions of PHP (5.2.0?) - thanks to mariush on http://php.net/preg_replace_callback
			$value = preg_replace_callback('/\{\$(f_\d+)\}/', array( &$this, '_pretty_tracker_replace_value'), $value);
		}
	}
	
	static function _pretty_tracker_replace_value($matches) {
		$smarty = TikiLib::lib('smarty');
		$s_var = null;
		if (!empty($matches[1])) { 
			$s_var = $smarty->get_template_vars($matches[1]);
		}
		if (!is_null($s_var)) {
			$r = $s_var;
		} else {
			$r = $matches[0];
		}
		return $r;
	}

	function nbComments($user) {
		return $this->comments()->fetchCount(array(
			'userName' => $user,
			'objectType' => 'trackeritem',
		));
	}
	function lastModif($trackerId) {
		return $this->items()->fetchOne($this->items()->max('lastModif'), array('trackerId' => (int) $trackerId));
	}
	function get_field($fieldId, $fields) {
		foreach ($fields as $f) {
			if ($f['fieldId'] == $fieldId) {
				return $f;
			}
		}
		return false;
	}
	function fieldId_is_editable($field, $item) {
		global $tiki_p_admin_trackers, $user;
		if ($tiki_p_admin_trackers == 'y') {
			return true;
		}
		if (in_array($field['type'], array('u', 'g', 'I'))) {
			return false;
		}
		if (empty($field['isHidden']) || $field['isHidden'] == 'n') {
			return true;
		}
		if ($field['isHidden'] == 'p' || $field['isHidden'] == 'y') {
			return false;
		}
		if (isset($item['createdBy']) && $user == $item['createdBy'] && $field['isHidden'] == 'ec') {
			return true;
		}
		return false;
	}

	function flaten($fields) {
		$new = array();
		if (empty($fields))
			return $new;
		foreach ($fields as $field) {
			if (is_array($field)) {
				$new = array_merge($new, $this->flaten($field));
			} else {
				$new[] = $field;
			}
		}		
		return $new;
	}
	function test_field_type($fields, $types) {
		$new = $this->flaten($fields);
		$table = $this->fields();

		return $table->fetchCount(array(
			'fieldId' => $table->in($new),
			'type' => $table->in($types, true),
		));
	}
	function get_computed_info($options, $trackerId=0, &$fields=null) {
		preg_match_all('/#([0-9]+)/', $options, $matches);
		$nbDates = 0;
		foreach($matches[1] as $k => $match) {
			if (empty($fields)) {
				$allfields = $this->list_tracker_fields($trackerId, 0, -1, 'position_asc', '');
				$fields = $allfields['data'];
			}
			foreach($fields as $k => $field) {
				if ($field['fieldId'] == $match && in_array($field['type'], array('f', 'j'))) {
					++$nbDates;
					$info = $field;
					break;
				} else if ($field['fieldId'] == $match && $field['type'] == 'C') {
					$info = $this-> get_computed_info($field['options'], $trackerId, $fields);
					if (!empty($info) && ($info['computedtype'] == 'f' || $info['computedtype'] == 'j')) {
						++$nbDates;
						break;
					}
				}
			}
		}
		if ($nbDates == 0) {
			return null;
		} elseif ($nbDates % 2 == 0) {
			return array('computedtype'=>'duration', 'options'=>$info['options'] ,'options_array'=>$info['options_array']);
		} else {
			return array('computedtype'=>'f', 'options'=>$info['options'] ,'options_array'=>$info['options_array']);
		}
	}
	function change_status($items, $status) {
		if (!count($items)) {
			return;
		}
		$table = $this->items();
		$table->updateMultiple(array('status' => $status), array(
			'itemId' => $table->in($items),
		));
	}
	function log($version, $itemId, $fieldId, $value='') {
		if (empty($version)) {
		   return;
		}
		$values = (array) $value;
		foreach ($values as $v) {
			$this->logs()->insert(array(
				'version' => $version,
				'itemId' => $itemId,
				'fieldId' => $fieldId,
				'value' => $v,
			));
		}
	}
	function last_log_version($itemId) {
		$logs = $this->logs();

		return $logs->fetchOne($logs->max('version'), array('itemId' => $itemId));
	}
	function remove_item_log($itemId) {
		$this->logs()->deleteMultiple(array('itemId' => $itemId));
	}
	function get_item_history($item_info=null, $fieldId=0, $filter='', $offset=0, $max=-1) {
		global $prefs;
		if (!empty($fieldId)) {
			$mid2[] = $mid[] = 'ttifl.`fieldId`=?';
			$bindvars[] = $fieldId;
		}
		if (!empty($item_info['itemId'])) {
			$mid[] = 'ttifl.`itemId`=?';
			$bindvars[] = $item_info['itemId'];
			if ($prefs['feature_categories'] == 'y') {
				$categlib = TikiLib::lib('categ');
				$item_categs = $categlib->get_object_categories('trackeritem', $item_info['itemId']);
				}
			}
		$query = 'select ttifl.*, ttf.* from `tiki_tracker_item_fields` ttifl left join `tiki_tracker_fields` ttf on (ttf.`fieldId`=ttifl.`fieldId`) where '.implode(' and ', $mid);
		$all = $this->fetchAll($query, $bindvars, -1, 0);
		foreach ($all as $f) {
			if (!empty($item_categs) && $f['type'] == 'e') {//category
				$f['options_array'] = explode(',',$f['options']);
				$all_descends = (isset($f['options_array'][3]) && $f['options_array'][3] == 1);
				$field_categs = $categlib->get_child_categories($f['options_array'][0], $all_descends);
				$aux = array();
				foreach ($field_categs as $cat) {
					$aux[] = $cat['categId'];
				}
				$field_categs = $aux;
				$f['value'] = implode(',', array_intersect($field_categs, $item_categs)); 
			}
			$last[$f['fieldId'].$f['lang']] = $f['value'];	
		}
		
		$last[-1] = $item_info['status']; 
		$mid[] = 'ta.`objectType`=?';
		$bindvars[] = 'trackeritem';
		if (!empty($filter)) {
			foreach ($filter as $key=>$f) {
		 		switch($key) {
					case 'version':
						$mid[] = 'ttifl.`version`=?';
						$bindvars[] = $f;
				}  		
			}
		}
		$query = 'select * from `tiki_tracker_item_field_logs` ttifl left join `tiki_actionlog` ta on (ta.`comment`=ttifl.`version` and ta.`object`=ttifl.`itemId`) where '.implode(' and ', $mid).' order by ttifl.`itemId` asc, ttifl.`version` desc, ttifl.`fieldId` asc';
		$all = $this->fetchAll($query, $bindvars, -1, 0);
		$history['cant'] = count($all);
		$history['data'] = array();
		$i = 0;
		foreach ($all as $hist) {
			if ($i >= $offset && ($max == -1 || $i < $offset + $max)) {
				$hist['new'] = isset($last[$hist['fieldId']])? $last[$hist['fieldId']]: '';
				$history['data'][] = $hist;
			}
			$last[$hist['fieldId']] = $hist['value'];
			++$i;
		}
		return $history;	
	}
	function move_item($trackerId, $itemId, $newTrackerId) {
		$newFields = $this->list_tracker_fields($newTrackerId, 0, -1, 'name_asc');
		foreach ($newFields['data'] as $field) {
			$translation[$field['name']] = $field;
		}
		$this->items()->update(array('trackerId' => $newTrackerId), array('itemId' => $itemId));

		$this->trackers()->update(array(
			'items' => $this->trackers()->decrement(1),
		), array('trackerId' => $trackerId));
		$this->trackers()->update(array(
			'items' => $this->trackers()->increment(1),
		), array('trackerId' => $newTrackerId));

		$newFields = $this->list_tracker_fields($newTrackerId, 0, -1, 'name_asc');
		$query = 'select ttif.*, ttf.`name`, ttf.`type`, ttf.`options` from `tiki_tracker_item_fields` ttif, `tiki_tracker_fields` ttf where ttif.itemId=? and ttif.`fieldId`=ttf.`fieldId`';
		$fields = $this->fetchAll($query, array($itemId));

		foreach ($fields as $field) {
			if (empty($translation[$field['name']]) || $field['type'] != $translation[$field['name']]['type'] || $field['options'] != $translation[$field['name']]['options']) { // delete the field
				$this->itemFields()->delete(array(
					'itemId' => $field['itemId'],
					'fieldId' => $field['fieldId'],
				));
			} else { // transfer
				$this->itemFields()->update(array(
					'fieldId' => $translation[$field['name']]['fieldId'],
				), array(
					'itemId' => $field['itemId'],
					'fieldId' => $field['fieldId'],
				));
			}
		}
	}

	/* copy the fields of one item ($from) to another one ($to) of the same tracker - except/only for some fields */
	/* note: can not use the generic function as they return not all the multilingual fields */
	function copy_item($from, $to, $except=null, $only=null, $status=null) {
		global $user, $prefs;

		if ($prefs['feature_categories'] == 'y') {
			$categlib = TikiLib::lib('categ');
			$cats = $categlib->get_object_categories('trackeritem', $from);
		}
		if (empty($to)) {
			$is_new = 'y';
			$info_to['trackerId'] = $this->items()->fetchOne('trackerId', array('itemId' => $from));
			$info_to['status'] = empty($status)? $this->items()->fetchOne('status', array('itemId' => $from)): $status;
			$info_to['created'] = $info_to['lastModif'] = $this->now;
			$info_to['createdBy'] = $info_to['lastModifBy'] = $user;
			$to = $this->items()->insert($info_to);
		}

		$query = 'select ttif.*, ttf.`type`, ttf.`options` from `tiki_tracker_item_fields` ttif left join `tiki_tracker_fields` ttf on (ttif.`fieldId` = ttf.`fieldId`) where `itemId`=?';
		$result = $this->fetchAll($query, array($from));
		$clean = array();
		foreach ($result as $res) {
			$res['options_array'] = preg_split('/\s*,\s*/', $res['options']);
			if ($prefs['feature_categories'] == 'y' && $res['type'] == 'e') {//category
				if ((!empty($except) && in_array($res['fieldId'], $except))
					|| (!empty($only) && !in_array($res['fieldId'], $only))) {// take away the categories from $cats
					$childs = $categlib->get_child_categories($res['options_array'][0]);
					$local = array();
					foreach ($childs as $child) {
						$local[] = $child['categId'];
					}
					$cats = array_diff($cats, $local);
				}
			}
			
			if ((!empty($except) && in_array($res['fieldId'], $except))
				|| (!empty($only) && !in_array($res['fieldId'], $only))
				|| ($res['type'] == 'q')
				) {
				continue;
			}
			if (!empty($is_new) && in_array($res['type'], array('u', 'g', 'I')) && ($res['options_array'][0] == 1 || $res['options_array'][0] == 2)) {
				$res['value'] = ($res['type'] == 'u')?$user: (($res['type'] =='g')? $_SESSION['u_info']['group']: TikiLib::get_ip_address());
			}
			if (in_array($res['type'], array('A', 'N'))) {// attachment - image
				continue; //not done yet
			}
			//echo "duplic".$res['fieldId'].' '. $res['value'].'<br>';
			if (!in_array($res['fieldId'], $clean)) {
				$this->itemFields()->delete(array(
					'itemId' => $to,
					'fieldId' => $res['fieldId'],
				));
				$clean[] = $res['fieldId'];
			}

			$data = array(
				'itemId' => $to,
				'fieldId' => $res['fieldId'],
				'value' => $res['value'],
			);

			$this->itemFields()->insert($data);
		}

		if (!empty($cats)) {
			$trackerId = $this->items()->fetchOne('trackerId', array('itemId' => $from));
			$this->categorized_item($trackerId, $to, "item $to", $cats);
		}
		return $to;
	}
	function export_attachment($itemId, $archive) {
		global $prefs;
		$files = $this->list_item_attachments( $itemId, 0, -1, 'attId_asc' );
		foreach( $files['data'] as $file ) {
			$localZip = "item_$itemId/".$file['filename'];
			$complete = $this->get_item_attachment( $file['attId'] );
			if (!empty($complete['path']) && file_exists($prefs['t_use_dir'].$complete['path'])) {
				if (!$archive->addFile($prefs['t_use_dir'].$complete['path'], $localZip))
					return false;
			} elseif (!empty($complete['data'])) {
				if (!$archive->addFromString($localZip, $complete['data']))
					return false;
			}
		}
		return true;
	}
	/* fill a calendar structure with items
	 * fieldIds contains one date or 2 dates
	 */
	function fillTableViewCell($items, $fieldIds, &$cell) {
		$smarty = TikiLib::lib('smarty');
		if (empty($items)) {
			return;
		}
		foreach ($items[0]['field_values'] as $i => $field) {
			if ($field['fieldId'] == $fieldIds[0]) {
				$iStart = $i;
			} elseif (count($fieldIds) > 1 && $field['fieldId'] == $fieldIds[1]) {
				$iEnd = $i;
			}
		}
		foreach ($cell as $i => $line) {
			foreach ($line as $j => $day) {
				if (!$day['focus']) {
					continue;
				}
				$overs = array();
				foreach ($items as $item) {
					$endDay = TikiLib::make_time(23,59,59, $day['month'], $day['day'], $day['year']);
					if ((count($fieldIds) == 1 && $item['field_values'][$iStart]['value'] >= $day['date'] && $item['field_values'][$iStart]['value'] <= $endDay)
						|| (count($fieldIds) > 1 && $item['field_values'][$iStart]['value'] <= $endDay && $item['field_values'][$iEnd]['value'] >= $day['date'])) {
							$cell[$i][$j]['items'][] = $item;
							$overs[] = preg_replace('|(<br /> *)*$|m', '', $item['over']);
					}
				}
				if (!empty($overs)) {
					$smarty->assign_by_ref('overs', $overs);
					$cell[$i][$j]['over'] = $smarty->fetch('tracker_calendar_over.tpl');
				}
			}
		}
		//echo '<pre>'; print_r($cell); echo '</pre>';
	}
	function get_tracker_by_name($name) {
		return $this->trackers()->fetchOne('trackerId', array('name' => $name));
	}
	
	function get_field_by_name($trackerId, $fieldName) {
		return $this->fields()->fetchOne('fieldId', array(
			'trackerId' => $trackerId,
			'name' => $fieldName
		));
	}
	
	function get_field_by_names($trackerName, $fieldName) {
		$trackerId = $this->trackers()->fetchOne('trackerId', array('name' => $trackerName));
		return $fieldId = $this->fields()->fetchOne('fieldId', array(
			'trackerId' => $trackerId,
			'name' => $fieldName
		));
	}
	
	function get_fields_by_names($trackerName, $fieldNames) {
		$fields = array();
		foreach($fieldNames as $fieldName) {
			$fields[$fieldName] = $this->get_field_by_names($trackerName, $fieldName);
		}
		return $fields;
	}
	
	function get_field_handler($field, $item = array())
	{
		$trackerId = (int) $field['trackerId'];

		$definition = Tracker_Definition::get($trackerId);

		$factory = new Tracker_Field_Factory($definition);

		return $factory->getHandler($field, $item);
	}

	function get_field_value($field, $item)
	{
		$handler = $this->get_field_handler($field, $item);
		$values = $handler->getFieldData();

		return isset($values['value']) ? $values['value'] : null;
	}

	private function parse_comment($data) {
		return nl2br(htmlspecialchars($data));
	}

	function send_replace_item_notifications($args)
	{
		global $prefs;

		// Don't send a notification if this operation is part of a bulk import
		if($args['bulk_import']) {
			return;
		}

		$trackerId = $args['trackerId'];
		$itemId = $args['object'];

		$new_values = $args['values'];
		$old_values = $args['old_values'];

		$the_data = $this->generate_watch_data($old_values, $new_values, $trackerId, $itemId, $args['version']);

		$tracker_definition = Tracker_Definition::get($trackerId);
		$tracker_info = $tracker_definition->getInformation();

		$watchers = $this->get_notification_emails($trackerId, $itemId, $tracker_info, $new_values['status'], $old_values['status']);

		if (count($watchers) > 0) {
			$simpleEmail = isset($tracker_info['simpleEmail']) ? $tracker_info['simpleEmail'] : "n";

			$trackerName = $tracker_info['name'];
			if (!isset($_SERVER["SERVER_NAME"])) {
				$_SERVER["SERVER_NAME"] = $_SERVER["HTTP_HOST"];
			}
			include_once('lib/webmail/tikimaillib.php');
			if( $simpleEmail == "n" ) {
				$desc = $this->get_isMain_value($trackerId, $itemId);
				if ($tracker_info['doNotShowEmptyField'] === 'y') {	// remove empty fields if tracker says so
					$the_data = preg_replace('/\[-\[.*?\]-\] -\[\(.*?\)\]-:\n\n----------\n/', '', $the_data);
				}
				
				$smarty = TikiLib::lib('smarty');
				
				$smarty->assign('mail_date', $this->now);
				$smarty->assign('mail_user', $user);
				$smarty->assign('mail_itemId', $itemId);
				$smarty->assign('mail_item_desc', $desc);
				$smarty->assign('mail_trackerId', $trackerId);
				$smarty->assign('mail_trackerName', $trackerName);
				$smarty->assign('server_name', $_SERVER['SERVER_NAME']);
				$foo = parse_url($_SERVER["REQUEST_URI"]);
				$machine = $this->httpPrefix( true ). $foo["path"];
				$smarty->assign('mail_machine', $machine);
				$parts = explode('/', $foo['path']);
				if (count($parts) > 1)
					unset ($parts[count($parts) - 1]);
				$smarty->assign('mail_machine_raw', $this->httpPrefix( true ). implode('/', $parts));
				$smarty->assign_by_ref('status', $new_values['status']);
				foreach ($watchers as $watcher) {
					$label = $itemId ? tra('Item Modification', $watcher['language']) : tra('Item creation', $watcher['language']);
					$mail_action = "\r\n$label\r\n\r\n";
					$mail_action.= tra('Tracker', $watcher['language']).":\n   $trackerName\r\n";
					$mail_action.= tra('Item', $watcher['language']).":\n   $itemId $desc";

					$smarty->assign('mail_action', $mail_action);
					$smarty->assign('mail_data', $the_data);
					if (isset($watcher['action']))
						$smarty->assign('mail_action', $watcher['action']);
					$smarty->assign('mail_to_user', $watcher['user']);
					$mail_data = $smarty->fetchLang($watcher['language'], 'mail/tracker_changed_notification.tpl');
					$mail = new TikiMail($watcher['user']);
					$mail->setSubject($smarty->fetchLang($watcher['language'], 'mail/tracker_changed_notification_subject.tpl'));
					$mail->setText($mail_data);
					$mail->setHeader("From", $prefs['sender_email']);
					$mail->send(array($watcher['email']));
				}
			} else {
					// Use simple email
				$foo = parse_url($_SERVER["REQUEST_URI"]);
				$machine = $this->httpPrefix( true ). $foo["path"];
				$parts = explode('/', $foo['path']);
				if (count($parts) > 1) {
					unset ($parts[count($parts) - 1]);
				}
				$machine = $this->httpPrefix( true ). implode('/', $parts);

				$userlib = TikiLib::lib('user');

				if (!empty($user)) {
					$my_sender = $userlib->get_user_email($user);
				} else { // look if a email field exists
					$fieldId = $this->get_field_id_from_type($trackerId, 'm');
					if (!empty($fieldId)) {
						$my_sender = $this->get_item_value($trackerId, $itemId, $fieldId);
					}
				}

				// Try to find a Subject in $the_data looking for strings marked "-[Subject]-" TODO: remove the tra (language translation by submitter)
				$the_string = '/^\[-\['.tra('Subject').'\]-\] -\[[^\]]*\]-:\n(.*)/m';
				$subject_test_unchanged = preg_match( $the_string, $the_data, $unchanged_matches );
				$the_string = '/^\[-\['.tra('Subject').'\]-\]:\n(.*)\n(.*)\n\n(.*)\n(.*)/m';
				$subject_test_changed = preg_match( $the_string, $the_data, $matches );
				$subject = '';

				if( $subject_test_unchanged == 1 ) {
					$subject = $unchanged_matches[1];
				}
				if( $subject_test_changed == 1 ) {
					$subject = $matches[1].' '.$matches[2].' '.$matches[3].' '.$matches[4];
				}

				$i = 0;
				foreach ($watchers as $watcher) {
					$mail = new TikiMail($watcher['user']);
					// first we look for strings marked "-[...]-" to translate by watcher language
					$translate_strings[$i] = preg_match_all( '/-\[([^\]]*)\]-/', $the_data, $tra_matches );
					$watcher_subject = $subject;
					$watcher_data = $the_data;
					if ($translate_strings[$i] > 0) {
						foreach ($tra_matches[1] as $match) {
							// now we replace the marked strings with correct translations
							$tra_replace = tra($match, $watcher['language']);
							$tra_match = "/-\[".preg_quote($match)."\]-/m";
							$watcher_subject = preg_replace($tra_match, $tra_replace, $watcher_subject);
							$watcher_data = preg_replace($tra_match, $tra_replace, $watcher_data);
						}
					}

					$mail->setSubject('['.$trackerName.'] '.str_replace('> ','',$watcher_subject).' ('.tra('Tracker was modified at ', $watcher['language']). $_SERVER["SERVER_NAME"].' '.tra('by', $watcher['language']).' '.$user.')');
					$mail->setText(tra('View the tracker item at:', $watcher['language'])."  $machine/tiki-view_tracker_item.php?itemId=$itemId\n\n" . $watcher_data);
					if( ! empty( $my_sender ) ) {
						$mail->setHeader("Reply-To", $my_sender);
					}
					$mail->send(array($watcher['email']));
					$i++;
				}
			}
		}
	}

	private function generate_watch_data($old, $new, $trackerId, $itemId, $version)
	{
		$tracker_definition = Tracker_Definition::get($trackerId);

		$oldStatus = $old['status'];
		$newStatus = $new['status'];

		$the_data = '';
		if (!empty($oldStatus) || !empty($newStatus)) {
			if (!empty($itemId) && $oldStatus != $newStatus) {
			   $this->log($version, $itemId, -1, $oldStatus);
			}
			$the_data .= '-[Status]-: ';
			$statusTypes = $this->status_types();
			if (isset($oldStatus) && $oldStatus != $newStatus) {
				$the_data .= $statusTypes[$oldStatus]['label'] . ' -> ';
			}

			if (!empty($newStatus)) {
				$the_data .= $statusTypes[$newStatus]['label'];
			}
			$the_data .=  "\n----------\n";
		}

		foreach ($tracker_definition->getFields() as $field) {
			$fieldId = $field['fieldId'];

			$old_value = isset($old[$fieldId]) ? $old[$fieldId] : '';
			$new_value = isset($new[$fieldId]) ? $new[$fieldId] : '';

			if ($old_value == $new_value) {
				continue;
			}

			$handler = $this->get_field_handler($field);
			$the_data .= $handler->watchCompare($old_value, $new_value);
			$the_data .=  "\n----------\n";
		}

		return $the_data;
	}

	private function tracker_is_syncable($trackerId)
	{
		global $prefs;
		if (!empty($prefs["user_trackersync_trackers"])) {
			$trackersync_trackers = preg_split('/\s*,\s*/', $prefs["user_trackersync_trackers"]);
			return in_array($trackerId, $trackersync_trackers);
		}

		return false;
	}

	private function get_tracker_item_user($trackerId, $values)
	{
		global $user, $prefs;
		$userlib = TikiLib::lib('user');
		$trackersync_user = $user;
		
		$definition = Tracker_Definition::get($trackerId);
		$fieldId = $definition->getUserField();
		$value = isset($values[$fieldId]) ? $values[$fieldId] : '';

		if ($value) {
			$trackersync_user = $value;
		}

		return $trackersync_user;
	}

	private function get_tracker_item_coordinates($trackerId, $values)
	{
		$definition = Tracker_Definition::get($trackerId);

		if ($fieldId = $definition->getGeolocationField()) {
			if (isset($values[$fieldId])) {
				return TikiLib::lib('geo')->parse_coordinates($values[$fieldId]);
			}
		}
	}

	function sync_user_realname($args)
	{
		global $prefs;

		$trackerId = $args['trackerId'];

		if (! $this->tracker_is_syncable($trackerId)) {
			return;
		}

		if (false === $trackersync_user = $this->get_tracker_item_user($trackerId, $args['values'])) {
			return;
		}

		if (!empty($prefs["user_trackersync_realname"])) {
			// Fields to concatenate are delimited by + and priority sets are delimited by , 
			$trackersync_realnamefields = preg_split('/\s*,\s*/', $prefs["user_trackersync_realname"]);

			foreach ($trackersync_realnamefields as $fields) {
				$parts = array();
				$fields = preg_split('/\s*\+\s*/', $fields);
				foreach ($fields as $field) {
					$field = (int) $field;
					if (isset($args['values'][$field])) {
						$parts[] = $args['values'][$field];
					}
				}

				$realname = implode(' ', $parts);

				if (! empty($realname)) {
					TikiLib::lib('tiki')->set_user_preference($trackersync_user, 'realName', $realname);
				}
			}
		}
	}

	function sync_user_geo($args)
	{
		global $prefs;

		$trackerId = $args['trackerId'];

		if (! $this->tracker_is_syncable($trackerId)) {
			return;
		}

		if (false === $trackersync_user = $this->get_tracker_item_user($trackerId, $args['values'])) {
			return;
		}

		if ($geo = $this->get_tracker_item_coordinates($trackerId, $args['values'])) {
			$tikilib = TikiLib::lib('tiki');

			$tikilib->set_user_preference($trackersync_user, 'lon', $geo['lon']);
			$tikilib->set_user_preference($trackersync_user, 'lat', $geo['lat']);
			if (!empty($geo['zoom'])) {
				$tikilib->set_user_preference($trackersync_user, 'zoom', $geo['zoom']);
			}
		}
	}

	function sync_item_geo($args)
	{
		$trackerId = $args['trackerId'];
		$itemId = $args['object'];

		if ($geo = $this->get_tracker_item_coordinates($trackerId, $args['values'])) {
			if ($geo && $itemId) {
				TikiLib::lib('geo')->set_coordinates('trackeritem', $itemId, $geo);
			}
		}
	}

	function sync_item_auto_categories($args)
	{
		$trackerId = $args['trackerId'];
		$itemId = $args['itemId'];
		$definition = Tracker_Definition::get($trackerId);

		if ($definition->isEnabled('autoCreateCategories')) {
			$categlib = TikiLib::lib('categ');
			$tracker_item_desc = $this->get_isMain_value($trackerId, $itemId);

			// Verify that parentCat exists Or Create It
			$parentcategId = $categlib->get_category_id("Tracker $trackerId");
			if ( ! isset($parentcategId) ) {
				$parentcategId = $categlib->add_category(0, "Tracker $trackerId", $definition->getConfiguration('description'));
			}
			// Verify that the sub Categ doesn't already exists
			$currentCategId = $categlib->get_category_id("Tracker Item $itemId");
			if ( ! isset($currentCategId) || $currentCategId == 0 ) {
				$currentCategId = $categlib->add_category($parentcategId,"Tracker Item $itemId",$tracker_item_desc);
			} else {
				$categlib->update_category($currentCategId, "Tracker Item $itemId", $tracker_item_desc, $parentcategId);
			}
			$cat_type = "trackeritem";
			$cat_objid = $itemId;
			$cat_desc = '';
			$cat_name = "Tracker Item $itemId";
			$cat_href = "tiki-view_tracker_item.php?trackerId=$trackerId&itemId=$itemId";
			// ?? HAS to do it ?? $categlib->uncategorize_object($cat_type, $cat_objid);
			$catObjectId = $categlib->is_categorized($cat_type, $cat_objid);
			if ( ! $catObjectId ) {
				$catObjectId = $categlib->add_categorized_object($cat_type, $cat_objid, $cat_desc, $cat_name, $cat_href);
			}
			$categlib->categorize($catObjectId, $currentCategId);
		}
	}

	private function get_item_categories($trackerId, $values)
	{
		$definition = Tracker_Definition::get($trackerId);
		$categories = array();

		foreach ($definition->getFields() as $field) {
			if ($field['type'] == 'e') {
				$fieldId = $field['fieldId'];
				$value = isset($values[$fieldId]) ? $values[$fieldId] : null;

				if ($value) {
					$categories = array_merge($categories, explode(',', $value));
				}
			}
		}

		return array_unique(array_filter($categories));
	}

	function sync_user_groups($args)
	{
		$trackerId = $args['trackerId'];

		if (! $this->tracker_is_syncable($trackerId)) {
			return;
		}

		if (false === $trackersync_user = $this->get_tracker_item_user($trackerId, $args['values'])) {
			return;
		}

		$userlib = TikiLib::lib('user');
		$categlib = TikiLib::lib('categ');

		$old_categories = $this->get_item_categories($trackerId, $args['old_values']);
		$current_categories = $this->get_item_categories($trackerId, $args['values']);

		$new_categs = array_diff($current_categories, $old_categories);
		$del_categs = array_diff($old_categories, $current_categories);

		global $prefs;
		$sig_catids = $categlib->get_category_descendants($prefs['user_trackersync_parentgroup']);
		$sig_add = array_intersect($sig_catids, $new_categs);
		$sig_del = array_intersect($sig_catids, $del_categs);
		$groupList = $userlib->list_all_groups();
		foreach ($sig_add as $c) {
			$groupName = $categlib->get_category_name($c, true);
			if (in_array($groupName, $groupList)) {
				$userlib->assign_user_to_group($trackersync_user, $groupName);
			}
		}
		foreach ($sig_del as $c) {
			$groupName = $categlib->get_category_name($c, true);
			if (in_array($groupName, $groupList)) {
				$userlib->remove_user_from_group($trackersync_user, $groupName);
			}
		}
	}

	function invalidate_item_cache($args)
	{
		$itemId = $args['object'];

		$cachelib = TikiLib::lib('cache');
		$cachelib->invalidate('trackerItemLabel'.$itemId);

		$fields = array_merge(array_keys($args['values']), array_keys($args['old_values']));
		$fields = array_unique($fields);
		
		foreach ($fields as $fieldId) {
			$old = isset($args['old_values'][$fieldId]) ? $args['old_values'][$fieldId] : null;
			$new = isset($args['values'][$fieldId]) ? $args['values'][$fieldId] : null;

			if ($old !== $new) {
				$cachelib->invalidate(md5('trackerfield'.$fieldId.'o'));
				$cachelib->invalidate(md5('trackerfield'.$fieldId.'c'));
				$cachelib->invalidate(md5('trackerfield'.$fieldId.'p'));
				$cachelib->invalidate(md5('trackerfield'.$fieldId.'op'));
				$cachelib->invalidate(md5('trackerfield'.$fieldId.'oc'));
				$cachelib->invalidate(md5('trackerfield'.$fieldId.'pc'));
				$cachelib->invalidate(md5('trackerfield'.$fieldId.'opc'));
			}
		}
	}
	
	function group_tracker_create($args)
	{
		global $user, $group;
		$trackerId = $args['trackerId'];
		$itemId = $args['object'];
		$new_itemId = $args['new_itemId'];
		$tracker_info = $args['tracker_info'];
		$definition = Tracker_Definition::get($trackerId);

		if ($definition->isEnabled('autoCreateGroup')) {
			$creatorGroupFieldId = $definition->getWriterGroupField();

			if (!empty($creatorGroupFieldId) && $definition->isEnabled('autoAssignGroupItem')) {
				$autoCopyGroup = $definition->getConfiguration('autoCopyGroup');
				if ($autoCopyGroup) {
					$this->modify_field($new_itemId, $tracker_info['autoCopyGroup'], $group);
					$fil[$tracker_info['autoCopyGroup']] = $group;
				}
				
			}
			$desc = $this->get_isMain_value($trackerId, $itemId);
			if (empty($desc)) {
				$desc = $definition->getConfiguration('description');
			}

			$userlib = TikiLib::lib('user');
			$groupName = $args['values'][$creatorGroupFieldId];
			if ($userlib->add_group($groupName, $desc, '', 0, $trackerId, '', 'y', 0, '', '', $creatorGroupFieldId)) {
				if ($groupId = $definition->getConfiguration('autoCreateGroupInc')) {
					$userlib->group_inclusion($groupName, $this->table('users_groups')->fetchOne('groupName', array('id' => $groupId)));
				}
			}
			if ($definition->isEnabled('autoAssignCreatorGroup')) {
				$userlib->assign_user_to_group($user, $groupName);
			}
			if ($definition->isEnabled('autoAssignCreatorGroupDefault')) {
				$userlib->set_default_group($user, $groupName);
				$_SESSION['u_info']['group'] = $groupName;
			}
		}
	}

	function update_tracker_summary($args)
	{
		$items = $this->items();
		$trackerId = (int) $args['trackerId'];
		$cant_items = $items->fetchCount(array('trackerId' => $trackerId));
		$this->trackers()->update(array('items' => (int) $cant_items, 'lastModif' => $this->now), array(
			'trackerId' =>  $trackerId,
		));
	}

	function sync_freetags($args)
	{
		$definition = Tracker_Definition::get($args['trackerId']);

		if ($field = $definition->getFreetagField()) {
			global $user;
			$freetaglib = TikiLib::lib('freetag');
			$freetaglib->update_tags($user, $args['object'], 'trackeritem', $args['values'][$field]);
		}
	}

	function update_create_missing_pages($args)
	{
		global $user;
		$tikilib = TikiLib::lib('tiki');

		$definition = Tracker_Definition::get($args['trackerId']);

		foreach ($definition->getFields() as $field) {
			$fieldId = $field['fieldId'];
			$value = $args['values'][$fieldId];
			if ($field['type'] == 'k' && $value != '' && !empty($field['options'][2])) {
				if (!$this->page_exists($value)) {
					$IP = $this->get_ip_address();
					$info = $this->get_page_info($field['options'][2]);
					$tikilib->create_page($value, 0, $info['data'], $tikilib->now, '', $user, $IP, $info['description'], $info['lang'], $info['is_html'], array(), $info['wysiwyyg'], $info['wiki_authors_style']);
				}
			}
		}
	}

	public function get_maximum_value($fieldId)
	{
		return $this->itemFields()->fetchOne($this->itemFields()->expr('MAX(CAST(`value` as UNSIGNED))'), array('fieldId' => (int) $fieldId));
	}

	function sync_categories($args)
	{
		$definition = Tracker_Definition::get($args['trackerId']);
		$ins_categs = array();

		foreach ($definition->getCategorizedFields() as $fieldId) {
			if (isset($args['values'][$fieldId])) {
				$ins_categs = array_merge($ins_categs, array_filter(explode(',', $args['values'][$fieldId])));
			}
		}

		$this->categorized_item($args['trackerId'], $args['object'], "item {$args['object']}", $ins_categs);
	}
	
	public function field_render_value( $params ) {
		$field = $params['field'];
		$item = isset($params['item']) ? $params['item'] : array();
		
		if (isset($field['value'])) {
			$item[$field['fieldId']] = $field['value'];
		}

		$handler = $this->get_field_handler($field, $item);

		if (isset($params['process']) && $params['process'] == 'y') {
			$field = array_merge($field, $handler->getFieldData($field));
			$handler = $this->get_field_handler($field, $item);
		}

		if ($handler) {
			$context = $params;
			unset($context['item']);
			unset($context['field']);
			if (!isset($context['list_mode'])) {
				$context['list_mode'] = 'n';
			} 
			$r = $handler->renderOutput($context);
			return $r;
		}
	}
}

global $trklib;
$trklib = new TrackerLib;
