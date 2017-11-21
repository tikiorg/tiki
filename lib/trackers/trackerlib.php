<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
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
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
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

	public $trackerinfo_cache;
	private $sectionFormats = [];

	function __construct()
	{
		$this->now = time();
		$this->registerSectionFormat('flat', 'view', 'trackeroutput/layout_flat.tpl', tr('Flat'));
		$this->registerSectionFormat('flat', 'edit', 'trackerinput/layout_flat.tpl', tr('Flat'));
		$this->registerSectionFormat('tab', 'view', 'trackeroutput/layout_tab.tpl', tr('Tabs'));
		$this->registerSectionFormat('tab', 'edit', 'trackerinput/layout_tab.tpl', tr('Tabs'));
	}

	function registerSectionFormat($layout, $mode, $template, $label)
	{
		if ($template) {
			$this->sectionFormats[$layout][$mode] = [
				'template' => $template,
				'label' => $label,
			];
		}
	}

	function unregisterSectionFormat($layout)
	{
		unset($this->sectionFormats[$layout]);
	}

	function getSectionFormatTemplate($layout, $mode)
	{
		if (isset($this->sectionFormats[$layout][$mode])) {
			return $this->sectionFormats[$layout][$mode]['template'];
		} elseif ($layout == 'config' || $layout === 'n') {
			// Special handling for config, fallback to default flat (also for when sectionFormat gets saved as "n" in legacy trackers)
			return $this->getSectionFormatTemplate('flat', $mode);
		} else {
			throw new Exception(tr('No template available for %0 - %1', $layout, $mode));
		}
	}

	function getGlobalSectionFormats()
	{
		$out = [];
		foreach ($this->sectionFormats as $layout => $modes) {
			if (count($modes) == 2) {
				$first = reset($modes);
				$out[$layout] = $first['label'];
			}
		}

		$out['config'] = tr('Configured');

		return $out;
	}

	private function attachments()
	{
		return $this->table('tiki_tracker_item_attachments');
	}

	private function comments()
	{
		return $this->table('tiki_comments');
	}

	private function itemFields()
	{
		return $this->table('tiki_tracker_item_fields', false);
	}

	private function trackers()
	{
		return $this->table('tiki_trackers');
	}

	private function items()
	{
		return $this->table('tiki_tracker_items');
	}

	private function fields()
	{
		return $this->table('tiki_tracker_fields');
	}

	private function options()
	{
		return $this->table('tiki_tracker_options');
	}

	private function logs()
	{
		return $this->table('tiki_tracker_item_field_logs', false);
	}

	private function groupWatches()
	{
		return $this->table('tiki_group_watches');
	}

	private function userWatches()
	{
		return $this->table('tiki_user_watches');
	}

	public function remove_field_images($fieldId)
	{
		$itemFields = $this->itemFields();
		$values = $itemFields->fetchColumn('value', ['fieldId' => (int) $fieldId]);
		foreach ($values as $file) {
			if (file_exists($file)) {
				unlink($file);
			}
		}
	}

	public function add_item_attachment_hit($id)
	{
		global $prefs, $user;
		if (StatsLib::is_stats_hit()) {
			$attachments = $this->attachments();
			$attachments->update(['hits' => $attachments->increment(1)], ['attId' => (int) $id]);
		}
		return true;
	}

	public function get_item_attachment_owner($attId)
	{
		return $this->attachments()->fetchOne('user', ['attId' => (int) $attId]);
	}

	public function list_item_attachments($itemId, $offset = 0, $maxRecords = -1, $sort_mode = 'attId_asc', $find = '')
	{
		$attachments = $this->attachments();

		$order = $attachments->sortMode($sort_mode);
		$fields = ['user', 'attId', 'itemId', 'filename', 'filesize', 'filetype', 'hits', 'created', 'comment', 'longdesc', 'version'];

		$conditions = [
			'itemId' => (int) $itemId,
		];

		if ($find) {
			$conditions['filename'] = $attachments->like("%$find%");
		}

		return [
			'data' => $attachments->fetchAll($fields, $conditions, $maxRecords, $offset, $order),
			'cant' => $attachments->fetchCount($conditions),
		];
	}

	public function get_item_nb_attachments($itemId)
	{
		$attachments = $this->attachments();

		$ret = $attachments->fetchRow(
			['hits' => $attachments->sum('hits'), 'attachments' => $attachments->count()],
			['itemId' => $itemId]
		);

		return $ret ? $ret : [];
	}

	public function get_item_nb_comments($itemId)
	{
		return $this->comments()->fetchCount(['object' => (int) $itemId, 'objectType' => 'trackeritem']);
	}

	public function list_all_attachements($offset = 0, $maxRecords = -1, $sort_mode = 'created_desc', $find = '')
	{
		$attachments = $this->attachments();

		$fields = ['user', 'attId', 'itemId', 'filename', 'filesize', 'filetype', 'hits', 'created', 'comment', 'path'];
		$order = $attachments->sortMode($sort_mode);
		$conditions = [];

		if ($find) {
			$conditions['filename'] = $attachments->like("%$find%");
		}

		return [
			'data' => $attachments->fetchAll($fields, $conditions, $maxRecords, $offset, $order),
			'cant' => $attachments->fetchCount($conditions),
		];
	}

	public function file_to_db($path, $attId)
	{
		if (is_readable($path)) {
			$updateResult = $this->attachments()->update(
				['data' => file_get_contents($path),	'path' => ''],
				['attId' => (int) $attId]
			);

			if ($updateResult) {
				unlink($path);
			}
		}
	}

	public function db_to_file($path, $attId)
	{
		$attachments = $this->attachments();

		$data = $attachments->fetchOne('data', ['attId' => (int) $attId]);
		if (false !== file_put_contents($path, $data)) {
			$attachments->update(['data' => '', 'path' => basename($path)], ['attId' => (int) $attId]);
		}
	}

	public function get_item_attachment($attId)
	{
		return $this->attachments()->fetchFullRow(['attId' => (int) $attId]);
	}

	public function remove_item_attachment($attId = 0, $itemId = 0)
	{
		global $prefs;
		$attachments = $this->attachments();
		$paths = [];

		if (empty($attId) && ! empty($itemId)) {
			if ($prefs['t_use_db'] === 'n') {
				$paths = $attachments->fetchColumn('path', ['itemId' => $itemId]);
			}

			$this->query('update `tiki_tracker_item_fields` ttif left join `tiki_tracker_fields` ttf using (`fieldId`) set `value`=? where ttif.`itemId`=? and ttf.`type`=?', ['', (int) $itemId, 'A']);
			$attachments->deleteMultiple(['itemId' => $itemId]);
		} elseif (! empty($attId)) {
			if ($prefs['t_use_db'] === 'n') {
				$paths = $attachments->fetchColumn('path', ['attId' => (int) $attId]);
			}
			$this->query('update `tiki_tracker_item_fields` ttif left join `tiki_tracker_fields` ttf using (`fieldId`) set `value`=? where ttif.`value`=? and ttf.`type`=?', ['', (int) $attId, 'A']);
			$attachments->delete(['attId' => (int) $attId]);
		}
		foreach (array_filter($paths) as $path) {
			@unlink($prefs['t_use_dir'] . $path);
		}
	}

	public function replace_item_attachment($attId, $filename, $type, $size, $data, $comment, $user, $fhash, $version, $longdesc, $trackerId = 0, $itemId = 0, $options = '', $notif = true)
	{
		global $prefs;
		$attachments = $this->attachments();

		$comment = strip_tags($comment);
		$now = $this->now;
		if (empty($attId)) {
			$attId = $attachments->insert(
				[
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
				]
			);
		} elseif (empty($filename)) {
			$attachments->update(
				[
					'user' => $user,
					'comment' => $comment,
					'version' => $version,
					'longdesc' => $longdesc,
				],
				['attId' => $attId]
			);
		} else {
			$path = $attachments->fetchOne('path', ['attId' => (int) $attId]);
			if ($path) {
				@unlink($prefs['t_use_dir'] . $path);
			}

			$attachments->update(
				[
					'filename' => $filename,
					'filesize' => $size,
					'filetype' => $type,
					'data' => $data,
					'user' => $user,
					'comment' => $comment,
					'path' => $fhash,
					'version' => $version,
					'longdesc' => $longdesc,
				],
				['attId' => (int) $attId]
			);
		}

		if (! $notif) {
			return $attId;
		}

		$options["attachment"] = ["attId" => $attId, "filename" => $filename, "comment" => $comment];
		$watchers = $this->get_notification_emails($trackerId, $itemId, $options);

		if (count($watchers > 0)) {
			$smarty = TikiLib::lib('smarty');
			$trackerName = $this->trackers()->fetchOne('name', ['trackerId' => (int) $trackerId]);

			$smarty->assign('mail_date', $this->now);
			$smarty->assign('mail_user', $user);
			$smarty->assign('mail_action', 'New File Attached to Item:' . $itemId . ' at tracker ' . $trackerName);
			$smarty->assign('mail_itemId', $itemId);
			$smarty->assign('mail_trackerId', $trackerId);
			$smarty->assign('mail_trackerName', $trackerName);
			$smarty->assign('mail_attId', $attId);
			$smarty->assign('mail_data', $filename . "\n" . $comment . "\n" . $version . "\n" . $longdesc);
			$foo = parse_url($_SERVER["REQUEST_URI"]);
			$machine = $this->httpPrefix(true) . $foo["path"];
			$smarty->assign('mail_machine', $machine);
			$parts = explode('/', $foo['path']);
			if (count($parts) > 1) {
				unset($parts[count($parts) - 1]);
			}
			$smarty->assign('mail_machine_raw', $this->httpPrefix(true) . implode('/', $parts));
			if (! isset($_SERVER["SERVER_NAME"])) {
				$_SERVER["SERVER_NAME"] = $_SERVER["HTTP_HOST"];
			}
			include_once('lib/webmail/tikimaillib.php');
			$smarty->assign('server_name', $_SERVER['SERVER_NAME']);
			$desc = $this->get_isMain_value($trackerId, $itemId);
			$smarty->assign('mail_item_desc', $desc);
			foreach ($watchers as $w) {
				$mail = new TikiMail($w['user']);

				if (! isset($w['template'])) {
					$w['template'] = '';
				}
				$content = $this->parse_notification_template($w['template']);

				$mail->setSubject($smarty->fetchLang($w['language'], $content['subject']));
				$mail_data = $smarty->fetchLang($w['language'], $content['template']);
				if (isset($w['templateFormat']) && $w['templateFormat'] == 'html') {
					$mail->setHtml($mail_data, str_replace('&nbsp;', ' ', strip_tags($mail_data)));
				} else {
					$mail->setText(str_replace('&nbsp;', ' ', strip_tags($mail_data)));
				}
				$mail->send([$w['email']]);
			}
		}

		return $attId;
	}

	public function list_last_comments($trackerId = 0, $itemId = 0, $offset = -1, $maxRecords = -1)
	{
		global $user;
		$mid = "1=1";
		$bindvars = [];

		if ($itemId != 0) {
			$mid .= " and `itemId`=?";
			$bindvars[] = (int) $itemId;
		}

		if ($trackerId != 0) {
			$query = "select t.*, t.object itemId from `tiki_comments` t left join `tiki_tracker_items` a on t.`object`=a.`itemId` where $mid and a.`trackerId`=? and t.`objectType` = 'trackeritem' order by t.`commentDate` desc";
			$bindvars[] = $trackerId;
			$query_cant = "select count(*) from `tiki_comments` t left join `tiki_tracker_items` a on t.`object`=a.`itemId` where $mid and a.`trackerId`=? AND t.`objectType` = 'trackeritem' order by t.`commentDate` desc";
		} else {
			if (! $this->user_has_perm_on_object($user, $trackerId, 'tracker', 'tiki_p_view_trackers')) {
				return ['cant' => 0];
			}

			$query = "select t.*, t.object itemId, a.`trackerId` from `tiki_comments` t left join `tiki_tracker_items` a on t.`object`=a.`itemId` where $mid AND t.`objectType` = 'trackeritem' order by `commentDate` desc";
			$query_cant = "select count(*) from `tiki_comments` where $mid AND `objectType` = 'trackeritem'";
		}

		$ret = $this->fetchAll($query, $bindvars, $maxRecords, $offset);
		$cant = $this->getOne($query_cant, $bindvars);

		foreach ($ret as &$res) {
			if (! $trackerId && ! $this->user_has_perm_on_object($user, $res['trackerId'], 'tracker', 'tiki_p_view_trackers')) {
				--$cant;
				continue;
			}
			$res["parsed"] = $this->parse_comment($res["data"]);
		}

		return [
			'data' => $ret,
			'cant' => $cant,
		];
	}

	public function get_last_position($trackerId)
	{
		$fields = $this->fields();
		return $fields->fetchOne($fields->max('position'), ['trackerId' => (int) $trackerId]);
	}

	public function get_tracker_item($itemId)
	{
		$res = $this->items()->fetchFullRow(['itemId' => (int) $itemId]);
		if (! $res) {
			return false;
		}

		$itemFields = $this->itemFields();
		$fields = $itemFields->fetchMap('fieldId', 'value', ['itemId' => (int) $itemId]);

		foreach ($fields as $id => $value) {
			$res[$id] = $value;
		}
		return $res;
	}

	function get_all_item_id($trackerId, $fieldId, $value)
	{
		$query = "select distinct ttif.`itemId` from `tiki_tracker_items` tti, `tiki_tracker_fields` ttf, `tiki_tracker_item_fields` ttif ";
		$query .= " where tti.`trackerId`=ttf.`trackerId` and ttif.`fieldId`=ttf.`fieldId` and ttf.`trackerId`=? and ttf.`fieldId`=? ";
		$value = "%$value%";
		$query .= " and ttif.`value` LIKE ?";

		$result = $this->fetchAll($query, [(int) $trackerId, (int)$fieldId, $value]);

		$itemIds = [];
		foreach ($result as $row) {
			$itemIds[] = $row['itemId'];
		}
		return $itemIds;
	}

	public function get_item_id($trackerId, $fieldId, $value, $partial = false)
	{
		$query = "select ttif.`itemId` from `tiki_tracker_items` tti, `tiki_tracker_fields` ttf, `tiki_tracker_item_fields` ttif ";
		$query .= " where tti.`trackerId`=ttf.`trackerId` and ttif.`fieldId`=ttf.`fieldId` and ttf.`trackerId`=? and ttf.`fieldId`=? ";
		if ($partial) {
			$value = "%$value%";
			$query .= " and ttif.`value` LIKE ?";
		} else {
			$query .= " and ttif.`value`=?";
		}
		return $this->getOne($query, [(int) $trackerId, (int) $fieldId, $value]);
	}

	public function get_item($trackerId, $fieldId, $value)
	{
		$itemId = $this->get_item_id($trackerId, $fieldId, $value);
		return $this->get_tracker_item($itemId);
	}

	/* experimental shared */
	/* trackerId is useless */
	public function get_item_value($trackerId, $itemId, $fieldId)
	{
		global $prefs;

		static $cache = [];
		$cacheKey = "$fieldId.$itemId";
		if (isset($cache[$cacheKey])) {
			return $cache[$cacheKey];
		}

		$value = $this->itemFields()->fetchOne('value', ['fieldId' => (int) $fieldId, 'itemId' => (int) $itemId]);

		if ($this->is_multilingual($fieldId) == 'y') {
			$list = json_decode($value, true);
			if (isset($list[$prefs['language']])) {
				return $list[$prefs['language']];
			}
		}

		if (TikiLib::lib('tiki')->get_memory_avail() < 1048576 * 10) {
			$cache = [];
		}
		$cache[$cacheKey] = $value;

		return $value;
	}

	public function get_item_status($itemId)
	{
		$status = $this->items()->fetchOne('status', ['itemId' => (int) $itemId]);
		return $status;
	}

	/*shared*/
	public function list_tracker_items($trackerId, $offset, $maxRecords, $sort_mode, $fields, $status = '', $initial = '')
	{

		$filters = [];
		if ($fields) {
			$temp_max = count($fields["data"]);
			for ($i = 0; $i < $temp_max; $i++) {
				$fieldId = $fields["data"][$i]["fieldId"];
				$filters[$fieldId] = $fields["data"][$i];
			}
		}
		$csort_mode = '';
		if (substr($sort_mode, 0, 2) == "f_") {
			list($a,$csort_mode,$corder) = explode('_', $sort_mode, 3);
		}
		$trackerId = (int) $trackerId;
		if ($trackerId == -1) {
			$mid = " where 1=1 ";
			$bindvars = [];
		} else {
			$mid = " where tti.`trackerId`=? ";
			$bindvars = [$trackerId];
		}
		if ($status) {
			$mid .= " and tti.`status`=? ";
			$bindvars[] = $status;
		}
		if ($initial) {
			$mid .= "and ttif.`value` like ?";
			$bindvars[] = $initial . '%';
		}
		if (! $sort_mode) {
			$temp_max = count($fields["data"]);
			for ($i = 0; $i < $temp_max; $i++) {
				if ($fields['data'][$i]['isMain'] == 'y') {
					$csort_mode = $fields['data'][$i]['name'];
					break;
				}
			}
		}
		if ($csort_mode) {
			$sort_mode = $csort_mode . "_desc";
			$bindvars[] = $csort_mode;
			$query = "select tti.*, ttif.`value` from `tiki_tracker_items` tti, `tiki_tracker_item_fields` ttif, `tiki_tracker_fields` ttf ";
			$query .= " $mid and tti.`itemId`=ttif.`itemId` and ttf.`fieldId`=ttif.`fieldId` and ttf.`name`=? order by ttif.`value`";
			$query_cant = "select count(*) from `tiki_tracker_items` tti, `tiki_tracker_item_fields` ttif, `tiki_tracker_fields` ttf ";
			$query_cant .= " $mid and tti.`itemId`=ttif.`itemId` and ttf.`fieldId`=ttif.`fieldId` and ttf.`name`=? ";
		} else {
			if (! $sort_mode) {
				$sort_mode = "lastModif_desc";
			}
			$query = "select * from `tiki_tracker_items` tti $mid order by " . $this->convertSortMode($sort_mode);
			$query_cant = "select count(*) from `tiki_tracker_items` tti $mid ";
		}
		$result = $this->fetchAll($query, $bindvars, $maxRecords, $offset);
		$cant = $this->getOne($query_cant, $bindvars);
		$ret = [];
		foreach ($result as $res) {
			$fields = [];
			$itid = $res["itemId"];
			$query2 = "select ttif.`fieldId`,`name`,`value`,`type`,`isTblVisible`,`isMain`,`position`
				from `tiki_tracker_item_fields` ttif, `tiki_tracker_fields` ttf
				where ttif.`fieldId`=ttf.`fieldId` and `itemId`=? order by `position` asc";
			$result2 = $this->fetchAll($query2, [(int) $res["itemId"]]);
			$pass = true;
			$kx = "";
			foreach ($result2 as $res2) {
				// Check if the field is visible!
				$fieldId = $res2["fieldId"];
				if (count($filters) > 0) {
					if (isset($filters[$fieldId]["value"]) and $filters[$fieldId]["value"]) {
						if (in_array($filters[$fieldId]["type"], ['a', 't'])) {
							if (! stristr($res2["value"], $filters[$fieldId]["value"])) {
								$pass = false;
							}
						} else {
							if (strtolower($res2["value"]) != strtolower($filters[$fieldId]["value"])) {
								$pass = false;
							}
						}
					}
					if (preg_replace("/[^a-zA-Z0-9]/", "", $res2["name"]) == $csort_mode) {
						$kx = $res2["value"] . $itid;
					}
				}
				$fields[] = $res2;
			}
			$res["field_values"] = $fields;
			$res["comments"] = $this->table('tiki_comments')->fetchCount(['object' => (int) $itid, 'objectType' => 'trackeritem']);
			if ($pass) {
				$kl = $kx . $itid;
				$ret["$kl"] = $res;
			}
		}
		ksort($ret);
		//$ret=$this->sort_items_by_condition($ret,$sort_mode);
		$retval = [];
		$retval["data"] = array_values($ret);
		$retval["cant"] = $cant;
		return $retval;
	}

	/*shared*/
	public function get_user_items($auser, $with_groups = true)
	{
		global $user;
		$items = [];

		$query = "select ttf.`trackerId`, tti.`itemId` from `tiki_tracker_fields` ttf, `tiki_tracker_items` tti, `tiki_tracker_item_fields` ttif";
		$query .= " where ttf.`fieldId`=ttif.`fieldId` and ttif.`itemId`=tti.`itemId` and `type`=? and tti.`status`=? and `value`=?";
		$result = $this->fetchAll($query, ['u','o',$auser]);
		$ret = [];

		$trackers = $this->table('tiki_trackers');
		$trackerFields = $this->table('tiki_tracker_fields');
		$trackerItemFields = $this->table('tiki_tracker_item_fields');
		//FIXME Perm:filter ?
		foreach ($result as $res) {
			if (! $this->user_has_perm_on_object($user, $res['trackerId'], 'tracker', 'tiki_p_view_trackers')) {
				continue;
			}
			$itemId = $res["itemId"];

			$trackerId = $res["trackerId"];
			// Now get the isMain field for this tracker
			$fieldId = $trackerFields->fetchOne('fieldId', ['isMain' => 'y', 'trackerId' => (int) $trackerId]);
			// Now get the field value
			$value = $trackerItemFields->fetchOne('value', ['fieldId' => (int) $fieldId, 'itemId' => (int) $itemId]);
			$tracker = $trackers->fetchOne('name', ['trackerId' => (int) $trackerId]);

			$aux["trackerId"] = $trackerId;
			$aux["itemId"] = $itemId;
			$aux["value"] = $value;
			$aux["name"] = $tracker;

			if (! in_array($itemId, $items)) {
				$ret[] = $aux;
				$items[] = $itemId;
			}
		}

		if ($with_groups) {
			$groups = $this->get_user_groups($auser);

			foreach ($groups as $group) {
				$query = "select ttf.`trackerId`, tti.`itemId` from `tiki_tracker_fields` ttf, `tiki_tracker_items` tti, `tiki_tracker_item_fields` ttif ";
				$query .= " where ttf.`fieldId`=ttif.`fieldId` and ttif.`itemId`=tti.`itemId` and `type`=? and tti.`status`=? and `value`=?";
				$result = $this->fetchAll($query, ['g', 'o', $group]);

				foreach ($result as $res) {
					$itemId = $res["itemId"];

					$trackerId = $res["trackerId"];
					// Now get the isMain field for this tracker
					$fieldId = $trackerFields->fetchOne('fieldId', ['isMain' => 'y', 'trackerId' => (int)$trackerId]);
					// Now get the field value
					$value = $trackerItemFields->fetchOne('value', ['fieldId' => (int)$fieldId, 'itemId' => (int)$itemId]);
					$tracker = $trackers->fetchOne('name', ['trackerId' => (int)$trackerId]);

					$aux["trackerId"] = $trackerId;
					$aux["itemId"] = $itemId;
					$aux["value"] = $value;
					$aux["name"] = $tracker;

					if (! in_array($itemId, $items)) {
						$ret[] = $aux;
						$items[] = $itemId;
					}
				}
			}
		}
		return $ret;
	}

	/* experimental shared */
	public function get_items_list($trackerId, $fieldId, $value, $status = 'o', $multiple = false, $sortFieldIds = null)
	{
		static $cache = [];
		$cacheKey = implode('.', [
			$trackerId, $fieldId, $value, $status, $multiple,
			is_array($sortFieldIds) ? implode($sortFieldIds) : $sortFieldIds
		]);
		if (isset($cache[$cacheKey])) {
			return $cache[$cacheKey];
		}
		$query = "select distinct tti.`itemId`, tti.`itemId` i from `tiki_tracker_items` tti, `tiki_tracker_item_fields` ttif ";
		$bindvars = [];
		if (is_string($sortFieldIds)) {
			$sortFieldIds = preg_split('/\|/', $sortFieldIds, -1, PREG_SPLIT_NO_EMPTY);
		}
		if (! empty($sortFieldIds)) {
			foreach ($sortFieldIds as $i => $sortFieldId) {
				$query .= " left join `tiki_tracker_item_fields` ttif$i on ttif.`itemId` = ttif$i.`itemId` and ttif$i.`fieldId` = ?";
				$bindvars[] = intval($sortFieldId);
			}
		}
		$query .= " where tti.`itemId`=ttif.`itemId` and ttif.`fieldId`=?";
		$bindvars[] = intval($fieldId);
		if ($multiple) {
			$query .= " and ttif.`value` REGEXP CONCAT('[[:<:]]', ?, '[[:>:]]')";
		} else {
			$query .= " and ttif.`value`=?";
		}
		$bindvars[] = $value;
		if (! empty($status)) {
			$query .= ' and ' . $this->in('tti.status', str_split($status, 1), $bindvars);
		}
		if (! empty($sortFieldIds)) {
			$query .= " order by " . implode(
				',',
				array_map(
					function ($i) {
						return "ttif$i.value";
					},
					array_keys($sortFieldIds)
				)
			);
		}
		$items = $this->fetchAll($query, $bindvars);
		$items = array_map(
			function ($row) {
				return $row['itemId'];
			},
			$items
		);
		if (TikiLib::lib('tiki')->get_memory_avail() < 1048576 * 10) {
			$cache = [];
		}
		$cache[$cacheKey] = $items;
		return $items;
	}

	public function get_tracker($trackerId)
	{
		return $this->table('tiki_trackers')->fetchFullRow(['trackerId' => (int) $trackerId]);
	}

	public function get_field_info($fieldId)
	{
		return $this->table('tiki_tracker_fields')->fetchFullRow(['fieldId' => (int) $fieldId]);
	}

	/**
	 * Marks fields as empty
	 * @param array $fields
	 * @return array
	 */
	public function mark_fields_as_empty($fields)
	{
		$lastHeader = -1;
		$elemSinceLastHeader = 0;
		foreach ($fields as $key => $trac) {
			if (! (empty($trac['value']) && empty($trac['cat'])
					&& empty($trac['links']) && $trac['type'] != 's'
					&& $trac['type'] != 'STARS' && $trac['type'] != 'h'
					&& $trac['type'] != 'l' && $trac['type'] != 'W')
					&& ! ($trac['options_array'][0] == 'password' && $trac['type'] == 'p')) {
				if ($trac['type'] == 'h') {
					if ($lastHeader > 0 && $elemSinceLastHeader == 0) {
						$fields[$lastHeader]['field_is_empty'] = true;
					}
					$lastHeader = $key;
					$elemSinceLastHeader = 0;
				} else {
					$elemSinceLastHeader++;
				}
				// this has a value
				continue;
			}
			$fields[$key]['field_is_empty'] = true;
		}
		if ($lastHeader > 0 && $elemSinceLastHeader == 0) {
			$fields[$lastHeader]['field_is_empty'] = true;
		}
		return $fields;
	}

	// includePermissions: Include the permissions of each tracker in its element's "permissions" subelement
	public function list_trackers($offset = 0, $maxRecords = -1, $sort_mode = 'name_asc', $find = '', $includePermissions = false)
	{
		$categlib = TikiLib::lib('categ');
		$join = '';
		$where = '';
		$bindvars = [];
		if ($jail = $categlib->get_jail()) {
			$categlib->getSqlJoin($jail, 'tracker', '`tiki_trackers`.`trackerId`', $join, $where, $bindvars);
		}
		if ($find) {
			$findesc = '%' . $find . '%';
			$where .= ' and (`tiki_trackers`.`name` like ? or `tiki_trackers`.`description` like ?)';
			$bindvars = array_merge($bindvars, [$findesc, $findesc]);
		}
		$query = "select * from `tiki_trackers` $join where 1=1 $where order by `tiki_trackers`." . $this->convertSortMode($sort_mode);
		$query_cant = "select count(*) from `tiki_trackers` $join where 1=1 $where";
		$result = $this->fetchAll($query, $bindvars, $maxRecords, $offset);
		$cant = $this->getOne($query_cant, $bindvars);
		$ret = [];
		$list = [];
		//FIXME Perm:filter ?
		foreach ($result as $res) {
			global $user;
			$add = $this->user_has_perm_on_object($user, $res['trackerId'], 'tracker', 'tiki_p_view_trackers');
			if ($add) {
				if ($includePermissions) {
					$res['permissions'] = Perms::get('tracker', $res['trackerId']);
				}
				$ret[] = $res;
				$list[$res['trackerId']] = $res['name'];
			}
		}
		$retval = [];
		$retval["list"] = $list;
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}

	// This function gets the prefix alias page name e.g. Org:230 for the pretty tracker
	// wiki page corresponding to a tracker item (230 in the example) using prefix aliases
	// Returns false if no such page is found.
	public function get_trackeritem_pagealias($itemId)
	{
		global $prefs;
		$trackerId = $this->table('tiki_tracker_items')->fetchOne('trackerId', ['itemId' => $itemId]);

		$semanticlib = TikiLib::lib('semantic');
		$t_links = $semanticlib->getLinksUsing('trackerid', ['toPage' => $trackerId]);

		if (count($t_links)) {
			if ($prefs['feature_multilingual'] == 'y' && count($t_links) > 1) {
				foreach ($t_links as $t) {
					if ($prefs['language'] == TikiLib::lib('multilingual')->getLangOfPage($t['fromPage'])) {
						$target = $t['fromPage'];
						break;
					}
				}
			} else {
				$target = $t_links[0]['fromPage'];
			}

			$p_links = $semanticlib->getLinksUsing('prefixalias', ['fromPage' => $target]);
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

	public function concat_item_from_fieldslist($trackerId, $itemId, $fieldsId, $status = 'o', $separator = ' ', $list_mode = '', $strip_tags = false, $format = '', $item = [])
	{
		$res = '';
		$values = [];
		if (is_string($fieldsId)) {
			$fieldsId = preg_split('/\|/', $fieldsId, -1, PREG_SPLIT_NO_EMPTY);
		}
		$definition = Tracker_Definition::get($trackerId);
		foreach ($fieldsId as $k => $field) {
			$myfield = $definition->getField($field);

			$myfield['value'] = $this->get_item_value($trackerId, $itemId, $field);
			$value = trim($this->field_render_value(['field' => $myfield, 'process' => 'y', 'list_mode' => $list_mode, 'item' => $item]));

			if ($format) {
				$values[] = $value;
			} else {
				if ($k > 0) {
					$res .= $separator;
				}
				$res .= $value;
			}
		}
		if ($format) {
			// use the underlying translation function to replace the %0 etc placeholders (and translate if necessary)
			$res = tra($format, '', false, $values);
		}
		if ($strip_tags) {
			$res = strip_tags($res);
		}
		return $res;
	}

	public function concat_all_items_from_fieldslist($trackerId, $fieldsId, $status = 'o', $separator = ' ', $strip_tags = false)
	{
		if (is_string($fieldsId)) {
			$fieldsId = preg_split('/\|/', $fieldsId, -1, PREG_SPLIT_NO_EMPTY);
		}
		$res = [];
		$definition = Tracker_Definition::get($trackerId);
		foreach ($fieldsId as $field) {
			if ($myfield = $definition->getField($field)) {
				$is_date = ($myfield['type'] == 'f');
				$is_trackerlink = ($myfield['type'] == 'r');
				$tmp = $this->get_all_items($trackerId, $field, $status);
				$options = $myfield['options_map'];
				foreach ($tmp as $key => $value) {
					if ($is_date) {
						$value = $this->date_format("%e/%m/%y", $value);
					}
					if ($is_trackerlink && $options['displayFieldsList'] && ! empty($options['displayFieldsList'][0])) {
						$item = $this->get_tracker_item($key);
						$itemId = $item[$field];
						$value = $this->concat_item_from_fieldslist($options['trackerId'], $itemId, $options['displayFieldsList'], $status, $separator, '', $strip_tags);
					}
					if (! empty($res[$key])) {
						$res[$key] .= $separator . $value;
					} else {
						$res[$key] = $value;
					}
				}
			}
		}
		return $res;
	}

	public function get_fields_from_fieldslist($trackerId, $fieldsId)
	{
		if (is_string($fieldsId)) {
			$fieldsId = preg_split('/\|/', $fieldsId, -1, PREG_SPLIT_NO_EMPTY);
		}
		$res = [];
		$definition = Tracker_Definition::get($trackerId);
		foreach ($fieldsId as $field) {
			if ($myfield = $definition->getField($field)) {
				$res[$field] = $myfield['permName'];
			}
		}
		return $res;
	}


	public function valid_status($status)
	{
		return in_array($status, ['o', 'c', 'p', 'op', 'oc', 'pc', 'opc']);
	}


	/**
	 * Gets an array of itemId => rendered value for a certain field for use in ItemLinks (mainly)
	 *
	 * @param int $trackerId
	 * @param int $fieldId
	 * @param string $status
	 * @return array
	 */
	public function get_all_items($trackerId, $fieldId, $status = 'o')
	{
		global $prefs, $user;
		$cachelib = TikiLib::lib('cache');

		if (! $trackerId) {
			return [tr('*** ERROR: Tracker ID not set ***', $fieldId)];
		}
		if (! $fieldId) {
			return [tr('*** ERROR: Field ID not set ***', $fieldId)];
		}

		$definition = Tracker_Definition::get($trackerId);
		if (! $definition) {
			// could be a deleted field referred to by a list type field
			return [tr('*** ERROR: Tracker %0 not found ***', $trackerId)];
		}
		$field = $definition->getField($fieldId);

		if (! $field) {
			// could be a deleted field referred to by a list type field
			return [tr('*** ERROR: Field %0 not found ***', $fieldId)];
		}

		$jail = '';
		if ($prefs['feature_categories'] == 'y') {
			$categlib = TikiLib::lib('categ');
			$jail = $categlib->get_jail();
		}

		$sort_mode = "value_asc";
		$cacheKey = 'trackerfield' . $fieldId . $status . $user;
		if ($this->is_multilingual($fieldId) == 'y') {
			$cacheKey .= $prefs['language'];
		}
		if (! empty($jail)) {
			$cacheKey .= serialize($jail);
		}

		$cacheKey = md5($cacheKey);

		if (( ! $ret = $cachelib->getSerialized($cacheKey) ) || ! $this->valid_status($status)) {
			$sts = preg_split('//', $status, -1, PREG_SPLIT_NO_EMPTY);
			$mid = " (" . implode('=? or ', array_fill(0, count($sts), 'tti.`status`')) . "=?) ";
			$fieldIdArray = preg_split('/\|/', $fieldId, -1, PREG_SPLIT_NO_EMPTY);
			$mid .= " and (" . implode('=? or ', array_fill(0, count($fieldIdArray), 'ttif.`fieldId`')) . "=?) ";
			$bindvars = array_merge($sts, $fieldIdArray);
			$join = '';
			if (! empty($jail)) {
				$categlib->getSqlJoin($jail, 'trackeritem', 'tti.`itemId`', $join, $mid, $bindvars);
			}
			$query = "select ttif.`itemId` , ttif.`value` FROM `tiki_tracker_items` tti,`tiki_tracker_item_fields` ttif $join ";
			$query .= " WHERE $mid and tti.`itemId` = ttif.`itemId` order by " . $this->convertSortMode($sort_mode);
			$items = $this->fetchAll($query, $bindvars);
			Perms::bulk(['type' => 'trackeritem', 'parentId' => $trackerId], 'object', array_map(function ($res) {
				return $res['itemId'];
			}, $items));
			$ret = [];
			foreach ($items as $res) {
				$itemId = $res['itemId'];
				$itemObject = Tracker_Item::fromId($itemId);
				if (! $itemObject) {
					Feedback::error(tr('TrackerLib::get_all_items: No item for itemId %0', $itemId), 'session');
				} elseif ($itemObject->canView()) {
					$ret[] = $res;
				}
			}
			$cachelib->cacheItem($cacheKey, serialize($ret));
		}

		$ret2 = [];
		foreach ($ret as $res) {
			$itemId = $res['itemId'];
			$field['value'] = $res['value'];
			$rendered = $this->field_render_value(['field' => $field, 'process' => 'y']);
			$ret2[$itemId] = trim(strip_tags($rendered), " \t\n\r\0\x0B\xC2\xA0");
		}
		return $ret2;
	}

	public function need_to_check_categ_perms($allfields = '')
	{
		global $prefs;
		if ($allfields === false) {
			// use for itemlink field - otherwise will be too slow
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

	public function get_all_tracker_items($trackerId)
	{
		return $this->items()->fetchColumn('itemId', ['trackerId' => (int) $trackerId]);
	}

	public function getSqlStatus($status, &$mid, &$bindvars, $trackerId, $skip_status_perm_check = false)
	{
		global $user;
		if (is_array($status)) {
			$status = implode('', $status);
		}

		// Check perms
		if (! $skip_status_perm_check && $status && ! $this->user_has_perm_on_object($user, $trackerId, 'tracker', 'tiki_p_view_trackers_pending') && ! $this->group_creator_has_perm($trackerId, 'tiki_p_view_trackers_pending')) {
			$status = str_replace('p', '', $status);
		}
		if (! $skip_status_perm_check && $status && ! $this->user_has_perm_on_object($user, $trackerId, 'tracker', 'tiki_p_view_trackers_closed') && ! $this->group_creator_has_perm($trackerId, 'tiki_p_view_trackers_closed')) {
			$status = str_replace('c', '', $status);
		}

		if (! $status) {
			return false;
		} elseif ($status == 'opc') {
				return true;
		} elseif (strlen($status) > 1) {
			$sts = preg_split('//', $status, -1, PREG_SPLIT_NO_EMPTY);
			if (count($sts)) {
				$mid .= " and (" . implode('=? or ', array_fill(0, count($sts), '`status`')) . "=?) ";
				$bindvars = array_merge($bindvars, $sts);
			}
		} else {
			$mid .= " and tti.`status`=? ";
			$bindvars[] = $status;
		}
		return true;
	}

	public function group_creator_has_perm($trackerId, $perm)
	{
		global $prefs;
		$definition = Tracker_Definition::get($trackerId);
		if ($definition && $groupCreatorFieldId = $definition->getWriterGroupField()) {
			$tracker_info = $definition->getInformation();
			$perms = $this->get_special_group_tracker_perm($tracker_info);
			return empty($perms[$perm]) ? false : true;
		} else {
			return false;
		}
	}

	/* group creator perms can only add perms,they can not take away perm
	   and they are only used if tiki_p_view_trackers is not set for the tracker and if the tracker ha a group creator field
	   must always be combined with a filter on the groups
	*/
	public function get_special_group_tracker_perm($tracker_info, $global = false)
	{
		global $prefs;
		$userlib = TikiLib::lib('user');
		$smarty = TikiLib::lib('smarty');
		$ret = [];
		$perms = $userlib->get_object_permissions($tracker_info['trackerId'], 'tracker', $prefs['trackerCreatorGroupName']);
		foreach ($perms as $perm) {
			$ret[$perm['permName']] = 'y';
			if ($global) {
				$p = $perm['permName'];
				global $$p;
				$$p = 'y';
				$smarty->assign("$p", 'y');
			}
		}
		if ($tracker_info['writerGroupCanModify'] == 'y') {
			// old configuration
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
	public function list_items($trackerId, $offset = 0, $maxRecords = -1, $sort_mode = '', $listfields = '', $filterfield = '', $filtervalue = '', $status = '', $initial = '', $exactvalue = '', $filter = '', $allfields = null, $skip_status_perm_check = false, $skip_permission_check = false)
	{
		//echo '<pre>FILTERFIELD:'; print_r($filterfield); echo '<br />FILTERVALUE:';print_r($filtervalue); echo '<br />EXACTVALUE:'; print_r($exactvalue); echo '<br />STATUS:'; print_r($status); echo '<br />FILTER:'; print_r($filter); /*echo '<br />LISTFIELDS'; print_r($listfields);*/ echo '</pre>';
		global $prefs;

		$cat_table = '';
		$sort_tables = '';
		$sort_join_clauses = '';
		$csort_mode = '';
		$corder = '';
		$trackerId = (int) $trackerId;
		$numsort = false;

		$mid = ' WHERE tti.`trackerId` = ? ';
		$bindvars = [$trackerId];
		$join = '';

		if (! empty($filter)) {
			$mid2 = [];
			if (! empty($filter['comment'])) {
				$cat_table .= ' LEFT JOIN `tiki_comments` tc ON tc.`object` = tti.`itemId` AND tc.`objectType` = "trackeritem"';
				$mid2[] = '(tc.`title` LIKE ? OR tc.`data` LIKE ?)';
				$bindvars[] = '%' . $filter['comment'] . '%';
				$bindvars[] = '%' . $filter['comment'] . '%';
				unset($filter['comment']);
			}
			$this->parse_filter($filter, $mid2, $bindvars);
			if (! empty($mid2)) {
				$mid .= ' AND ' . implode(' AND ', $mid2);
			}
		}

		if (! $this->getSqlStatus($status, $mid, $bindvars, $trackerId, $skip_status_perm_check) && ! $skip_status_perm_check && $status) {
			return ['cant' => 0, 'data' => ''];
		}
		if (substr($sort_mode, 0, 2) == 'f_') {
			list($a, $asort_mode, $corder) = preg_split('/_/', $sort_mode);
		}
		if ($initial) {
			$mid .= ' AND ttif.`value` LIKE ?';
			$bindvars[] = $initial . '%';
			if (isset($asort_mode)) {
				$mid .= ' AND ttif.`fieldId` = ?';
				$bindvars[] = $asort_mode;
			}
		}
		if (! $sort_mode) {
			$sort_mode = 'lastModif_desc';
		}

		if (substr($sort_mode, 0, 2) == 'f_' or ! empty($filterfield)) {
			if (substr($sort_mode, 0, 2) == 'f_') {
				$csort_mode = 'sttif.`value` ';
				$sort_tables = ' LEFT JOIN (`tiki_tracker_item_fields` sttif)'
					. ' ON (tti.`itemId` = sttif.`itemId`'
					. (! empty($asort_mode) ? " AND sttif.`fieldId` = $asort_mode" : '')
					. ')';
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
//						if ($field['name'] == 'Rating' || $field['name'] == tra('Rating')) { // No need to have that string, isn't it? Admins can replace for a more suited string in their use case
							$numsort = true;
//						}
						break;
				}
			} else {
				list($csort_mode, $corder) = preg_split('/_/', $sort_mode);
				$csort_mode = 'tti.`' . $csort_mode . '` ';
			}

			if (empty($filterfield)) {
				$nb_filtered_fields = 0;
			} elseif (! is_array($filterfield)) {
				$fv = $filtervalue;
				$ev = $exactvalue;
				$ff = (int) $filterfield;
				$nb_filtered_fields = 1;
			} else {
				$nb_filtered_fields = count($filterfield);
			}

			$last = 0;
			for ($i = 0; $i < $nb_filtered_fields; $i++) {
				if (is_array($filterfield)) {
					//multiple filter on an exact value or a like value - each value can be simple or an array
					$ff = (int) $filterfield[$i];
					$ff_array = $filterfield[$i]; // Need value as array used below
					$ev = ! empty($exactvalue[$i]) ? $exactvalue[$i] : null;
					$fv = ! empty($filtervalue[$i]) ? $filtervalue[$i] : null;
				}
				$filter = $this->get_tracker_field($ff);

				// Determine if field is an item list field and postpone filtering till later if so
				if ($filter["type"] == 'l' && isset($filter['options_array'][2]) && isset($filter['options_array'][2]) && isset($filter['options_array'][3])) {
					$linkfilter[] = ['filterfield' => $ff, 'exactvalue' => $ev, 'filtervalue' => $fv];
					continue;
				}

				$value = empty($fv) ? $ev : $fv;
				$search_for_blank = ( is_null($ev) && is_null($fv) )
					|| ( is_array($value) && count($value) == 1
						&& ( empty($value[0])
							|| ( is_array($value[0]) && count($value[0]) == 1 && empty($value[0][0]) )
						)
					);

				$cat_table .= ' ' . ( $search_for_blank ? 'LEFT' : 'INNER' ) . " JOIN `tiki_tracker_item_fields` ttif$i ON ttif$i.`itemId` = tti.`itemId`";
				$last++;

				if (isset($ff_array['sqlsearch']) && is_array($ff_array['sqlsearch'])) {
					$mid .= " AND ttif$i.`fieldId` in (" . implode(',', array_fill(0, count($ff_array['sqlsearch']), '?')) . ')';
					$bindvars = array_merge($bindvars, $ff_array['sqlsearch']);
				} elseif (isset($ff_array['usersearch']) && is_array($ff_array['usersearch'])) {
					$mid .= " AND ttif$i.`fieldId` in (" . implode(',', array_fill(0, count($ff_array['usersearch']), '?')) . ')';
					$bindvars = array_merge($bindvars, $ff_array['usersearch']);
				} elseif ($ff) {
					if ($search_for_blank) {
						$cat_table .= " AND ttif$i.`fieldId` = " . intval($ff);
					} else {
						$mid .= " AND ttif$i.`fieldId`=? ";
						$bindvars[] = $ff;
					}
				}

				if ($filter['type'] == 'e' && $prefs['feature_categories'] == 'y' && (! empty($ev) || ! empty($fv))) {
					//category

					$value = empty($fv) ? $ev : $fv;
					if (! is_array($value) && $value != '') {
						$value = [$value];
						$not = '';
					} elseif (is_array($value) && array_key_exists('not', $value)) {
						$value = [$value['not']];
						$not = 'not';
					}
					if (empty($not) && count($value) == 1 && ( empty($value[0]) || ( is_array($value[0]) && count($value[0]) == 1 && empty($value[0][0]) ) )) {
						$cat_table .= " left JOIN `tiki_objects` tob$ff ON (tob$ff.`itemId` = tti.`itemId` AND tob$ff.`type` = 'trackeritem')"
							. " left JOIN `tiki_category_objects` tco$ff ON (tob$ff.`objectId` = tco$ff.`catObjectId`)";
						$mid .= " AND tco$ff.`categId` IS NULL ";
						continue;
					}
					if (empty($not)) {
						$cat_table .= " INNER JOIN `tiki_objects` tob$ff ON (tob$ff.`itemId` = tti.`itemId`)"
							. " INNER JOIN `tiki_category_objects` tco$ff ON (tob$ff.`objectId` = tco$ff.`catObjectId`)";
						$mid .= " AND tob$ff.`type` = 'trackeritem' AND tco$ff.`categId` IN ( ";
					} else {
						$cat_table .= " left JOIN `tiki_objects` tob$ff ON (tob$ff.`itemId` = tti.`itemId`)"
							. " left JOIN `tiki_category_objects` tco$ff ON (tob$ff.`objectId` = tco$ff.`catObjectId`)";
						$mid .= " AND tob$ff.`type` = 'trackeritem' AND tco$ff.`categId` NOT IN ( ";
					}
					$first = true;
					foreach ($value as $k => $catId) {
						if (is_array($catId)) {
							// this is a grouped AND logic for optimization indicated by the value being array
							$innerfirst = true;
							foreach ($catId as $c) {
								if (is_array($c)) {
									$innerfirst = true;
									foreach ($c as $d) {
										$bindvars[] = $d;
										if ($innerfirst) {
											$innerfirst = false;
										} else {
											$mid .= ',';
										}
										$mid .= '?';
									}
								} else {
									$bindvars[] = $c;
									$mid .= '?';
								}
							}
							if ($k < count($value) - 1) {
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
							if ($first) {
								$first = false;
							} else {
								$mid .= ',';
							}
							$mid .= '?';
						}
					}
					$mid .= " ) ";
					if (! empty($not)) {
						$mid .= " OR tco$ff.`categId` IS NULL ";
					}
				} elseif ($filter['type'] == 'usergroups') {
					$definition = Tracker_Definition::get($trackerId);
					$userFieldId = $definition->getUserField();
					$cat_table .= " INNER JOIN `tiki_tracker_item_fields` ttifu ON (tti.`itemId`=ttifu.`itemId`) INNER JOIN `users_users` uu ON ttifu.`value` REGEXP CONCAT('[[:<:]]', uu.`login`, '[[:>:]]') INNER JOIN `users_usergroups` uug ON (uug.`userId`=uu.`userId`)";
					$mid .= ' AND ttifu.`fieldId`=? AND uug.`groupName`=? ';
					$bindvars[] = $userFieldId;
					$bindvars[] = empty($ev) ? $fv : $ev;
				} elseif ($filter['type'] == 'u' && $ev > '') { // user selector and exact value
					if (is_array($ev)) {
						$keys = array_keys($ev);
						if ($keys[0] === 'not') {
							$mid .= " AND ( ttif$i.`value` NOT REGEXP " . implode(' OR ttif$i.`value` NOT REGEXP ', array_fill(0, count($ev), '?')) . " OR ttif$i.`value` IS NULL )";
						} else {
							$mid .= " AND ( ttif$i.`value` REGEXP " . implode(' OR ttif$i.`value` REGEXP ', array_fill(0, count($ev), '?')) . " )";
						}
						$bindvars = array_merge(
							$bindvars,
							array_values(array_map(function ($ev) {
								return "[[:<:]]{$ev}[[:>:]]";
							}, $ev))
						);
					} else {
						$mid .= " AND ttif$i.`value` REGEXP ? ";
						$bindvars[] = "[[:<:]]{$ev}[[:>:]]";
					}
				} elseif ($filter['type'] == '*') { // star
					$mid .= " AND ttif$i.`value`*1>=? ";
					$bindvars[] = $ev;
					if (($j = array_search($ev, $filter['options_array'])) !== false && $j + 1 < count($filter['options_array'])) {
						$mid .= " AND ttif$i.`value`*1<? ";
						$bindvars[] = $filter['options_array'][$j + 1];
					}
				} elseif ($filter['type'] == 'r' && ($fv || $ev)) {
					$cv = $fv ? $fv : $ev;

					$cat_table .= " LEFT JOIN tiki_tracker_item_fields ttif{$i}_remote ON ttif$i.`value` = ttif{$i}_remote.`itemId` AND ttif{$i}_remote.`fieldId` = " . intval($filter['options_array'][1]) . ' ';
					if (is_numeric($cv)) {
						$mid .= " AND ( ttif{$i}_remote.`value` LIKE ? OR ttif$i.`value` = ? ) ";
						$bindvars[] = $ev ? $ev : "%$fv%";
						$bindvars[] = $cv;
					} else {
						$mid .= " AND ttif{$i}_remote.`value` LIKE ? ";
						$bindvars[] = $ev ? $ev : "%$fv%";
					}
				} elseif ($ev > '') {
					if (is_array($ev)) {
						$keys = array_keys($ev);
						if (in_array((string) $keys[0], ['<', '>'])) {
							$mid .= " AND ttif$i.`value`" . $keys[0] . "? + 0";
							$bindvars[] = $ev[$keys[0]];
						} elseif (in_array((string) $keys[0], ['<=', '>='])) {
							$mid .= " AND (ttif$i.`value`" . $keys[0] . "? + 0 OR ttif$i.`value` = ?)";
							$bindvars[] = $ev[$keys[0]];
							$bindvars[] = $ev[$keys[0]];
						} elseif ($keys[0] === 'not') {
							$mid .= " AND ( ttif$i.`value` not in (" . implode(',', array_fill(0, count($ev), '?')) . ") OR ttif$i.`value` IS NULL )";
							$bindvars = array_merge($bindvars, array_values($ev));
						} else {
							$mid .= " AND ttif$i.`value` in (" . implode(',', array_fill(0, count($ev), '?')) . ")";
							$bindvars = array_merge($bindvars, array_values($ev));
						}
					} elseif (isset($ff_array['sqlsearch']) && is_array($ff_array['sqlsearch'])) {
						$mid .= " AND MATCH(ttif$i.`value`) AGAINST(? IN BOOLEAN MODE)";
						$bindvars[] = $ev;
					} elseif (isset($ff_array['usersearch']) && is_array($ff_array['usersearch'])) {
						$mid .= " AND ttif$i.`value` REGEXP ? ";
						$bindvars[] = "[[:<:]]{$ev}[[:>:]]";
					} else {
						$mid .= " AND ttif$i.`value`=? ";
						$bindvars[] = $ev == '' ? $fv : $ev;
					}
				} elseif ($fv > '') {
					if (! is_array($fv)) {
						$value = [$fv];
					} else {
						$value = $fv;
					}
					$mid .= ' AND(';
					$cpt = 0;
					foreach ($value as $v) {
						if ($cpt++) {
							$mid .= ' OR ';
						}
						$mid .= " upper(ttif$i.`value`) like upper(?) ";
						if (substr($v, 0, 1) == '*' || substr($v, 0, 1) == '%') {
							$bindvars[] = '%' . substr($v, 1);
						} elseif (substr($v, -1, 1) == '*' || substr($v, -1, 1) == '%') {
							$bindvars[] = substr($v, 0, strlen($v) - 1) . '%';
						} else {
							$bindvars[] = '%' . $v . '%';
						}
					}
					$mid .= ')';
				} elseif (is_null($ev) && is_null($fv)) { // test null value
					$mid .= " AND ( ttif$i.`value`=? OR ttif$i.`value` IS NULL )";
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
			if ($csort_mode == '`itemId`') {
				$csort_mode = 'tti.`itemId`';
			}
			$sort_tables = '';
			$cat_tables = '';
		}

		$categlib = TikiLib::lib('categ');
		if ($jail = $categlib->get_jail()) {
			$categlib->getSqlJoin($jail, 'trackeritem', 'tti.`itemId`', $join, $mid, $bindvars);
		}

		$base_tables = '('
			. ' `tiki_tracker_items` tti'
			. ' INNER JOIN `tiki_tracker_item_fields` ttif ON tti.`itemId` = ttif.`itemId`'
			. ' INNER JOIN `tiki_tracker_fields` ttf ON ttf.`fieldId` = ttif.`fieldId`'
			. ')' . $join;

		$fieldIds = [];
		foreach ($listfields as $k => $f) {
			if (isset($f['fieldId'])) {
				$fieldIds[] = $f['fieldId'];
			} else {
				$fieldIds[] = $k;	// sometimes filterfields are provided with the fieldId only on the array keys
			}
		}
		if (! empty($filterfield)) {
			// fix: could be that there is just one field. in this case it might be a scalar,
			// not an array due to not handle $filterfield proper somewhere else in the code
			if (! is_array($filterfield)) {
				$filterfield = [$filterfield];
			}
			foreach ($filterfield as $f) {
				if (! empty($f['sqlsearch'])) {
					foreach ($f['sqlsearch'] as $subf) {
						if (! in_array($subf, $fieldIds)) {
							$fieldIds[] = $subf;
						}
					}
				} elseif (! empty($f['usersearch'])) {
					foreach ($f['usersearch'] as $subf) {
						if (! in_array($subf, $fieldIds)) {
							$fieldIds[] = $subf;
						}
					}
				} else {
					if (! in_array($f, $fieldIds)) {
						$fieldIds[] = $f;
					}
				}
			}
		}

		if (! empty($fieldIds)) {
			$mid .= ' AND ' . $this->in('ttif.fieldId', $fieldIds, $bindvars);
		}

		if ($csort_mode == '`created`') {
			$csort_mode = 'tti.created';
		}
		$query = 'SELECT tti.*'
				. ', ' . ( ($numsort) ? "cast(max($csort_mode) as decimal)" : "max($csort_mode)") . ' as `sortvalue`'
			. ' FROM ' . $base_tables . $sort_tables . $cat_table
			. $mid
			. ' GROUP BY tti.`itemId`, tti.`trackerId`, tti.`created`, tti.`createdBy`, tti.`status`, tti.`lastModif`, tti.`lastModifBy`, ' . $csort_mode
			. ' ORDER BY ' . $this->convertSortMode('sortvalue_' . $corder);
		if ($numsort) {
			$query .= ',' . $this->convertSortMode($csort_mode);
		}
		//echo htmlentities($query); print_r($bindvars);
		$query_cant = 'SELECT count(DISTINCT ttif.`itemId`) FROM ' . $base_tables . $sort_tables . $cat_table . $mid;

		// save the result
		$ret = [];

		// Start loop to get the required number of items if permissions / filters are in use.
		// The problem: If $maxItems and $offset are given,
		// but the sql query returns items the user has no permissions or the filter criteria does not match,
		// then only a subset of what is available  would be returned.


		// Due to performance issues with trackers having more than 5k items, we make it optional
		// $exactPaging true : slow on large tracker, check each item for permission and filtering
		//              false: pass offset directly to sql, could lead to wrong pagination if perms / filter are used on items

		// Need to get this into tracker setup, so one can decide for each tracker how $exactPaging should work.
		// $definition = Tracker_Definition::get($trackerId);


		// default is old behaviour as of tiki14 - get offset directly from sql without taking permissions or filter into account.
		$exactPaging = false;

		// defaults for $exactPaging == false
		// original requested number of items
		$maxRecordsRequested = $maxRecords;
		// original page (from pagination)
		$offsetRequested = $offset;
		// offset calculated on  $offsetRequested
		$currentOffset = 0;
		// set to true when we have enough records or no records left.
		$finished = false;
		// used internaly - one time query that returns the total number of records without taking into account filter or permissions
		$cant = $this->getOne($query_cant, $bindvars);
		// $cant will be modified bc its used otherwise. so save the totalCount value
		$totalCount = $cant;
		// total number of records read so far
		$currentCount = 0;
		// number of records in the result set
		$resultCount = 0;

		// settings for $exactPaging == true
		if ($exactPaging == true) {
			// outer loop - grab more records bc it might be we must filter out records.
			// 300 seems to be ok, bc paganination offers this as well as the size of the resultset
			// NOTE: This value is important with respect to memory usage and performance - especially when lots of items (like 10k+) are in use.
			$maxRecords = 300;
			// offset used for sql query
			$offset = 0;
		}


		while (! $finished) {
			$ret1 = $this->fetchAll($query, $bindvars, $maxRecords, $offset);
			// add. security - should not be necessary bc of check at the end. no records left - end outer loop
			if (count($ret1) == 0) {
				$finished = true;
			}

			foreach ($ret1 as $res) {
				$mem = TikiLib::lib('tiki')->get_memory_avail();
				if ($mem < 1048576 * 10) {	// Less than 10MB left?
					// post an error even though it doesn't get displayed when using export as the output goes into the output file
					Feedback::error(tr('Tracker list_items ran out of memory after %0 items.', count($ret)), 'session');
					break;
				}

				$res['itemUsers'] = [];
				if ($listfields !== null) {
					$res['field_values'] = $this->get_item_fields($trackerId, $res['itemId'], $listfields, $res['itemUsers']);
				}

				if (! $skip_permission_check) {
					$itemObject = Tracker_Item::fromInfo($res);
					if (! $itemObject->canView()) {
						$cant--;
						// skipped record bc of permissions - need to count for outer loop
						$currentCount++;
						continue;
					}
				}

				if (! empty($asort_mode)) {
					foreach ($res['field_values'] as $i => $field) {
						if ($field['fieldId'] == $asort_mode) {
							$kx = $field['value'] . '.' . $res['itemId'];
						}
					}
				}
				if (isset($linkfilter) && $linkfilter) {
					$filterout = false;
					// NOTE: This implies filterfield if is link field has to be in fields set
					foreach ($res['field_values'] as $i => $field) {
						foreach ($linkfilter as $lf) {
							if ($field['fieldId'] == $lf["filterfield"]) {
								// extra comma at the front and back of filtervalue to avoid ambiguity in partial match
								if ($lf["filtervalue"] && strpos(',' . implode(',', $field['items']) . ',', $lf["filtervalue"]) === false) {
									$filterout = true;
									break 2;
								} elseif ($lf["exactvalue"] && ! in_array($lf['exactvalue'], $field['items'])) {
									$filterout = true;
									break 2;
								}
							}
						}
					}
					if ($filterout) {
						$cant--;
						// skipped record bc of filter criteria - need to count for outer loop
						$currentCount++;
						continue;
					}
				}

				$res['geolocation'] = TikiLib::lib('geo')->get_coordinates('trackeritem', $res['itemId']);

				// have a field, adjust counter and check if we have enough items
				$currentCount++;
				$currentOffset++;

				// field is stored in $res. See wether we can add it to the resultset, based on the requested offset
				// if clause logic mainly for $exactPaging == true
				if (($currentOffset > $offsetRequested) || ($exactPaging == false)) {
					$resultCount++;
					if (empty($kx)) {
						// ex: if the sort field is non visible, $kx is null
						$ret[] = $res;
					} else {
						$ret[$kx] = $res;
					}
				}

				// logic for $exactPaging == true. enough items - need to leave the foreach loop
				if ($resultCount == $maxRecordsRequested) {
					$finished = true;
					break;
				}
			} // foreach

			// foreach loop done - depending on $exactPaging we finish or might need to go ahead
			if ($exactPaging == false) {
				$finished = true;
			}

			// are items left? - this part is only relevant when $exactPaging == true
			if ($currentCount == $totalCount) {
				$finished = true;
			} else {
				$offset += $maxRecords;
			}
		} // while

// End loop to get the required number of items if permissions / filters are in use
		$retval = [];
		$retval['data'] = array_values($ret);
		$retval['cant'] = $cant;
		return $retval;
	}

	/* listfields fieldId=>fielddefinition */
	public function get_item_fields($trackerId, $itemId, $listfields, &$itemUsers, $alllang = false)
	{
		global $prefs, $user, $tiki_p_admin_trackers;

		$definition = Tracker_Definition::get($trackerId);
		$info = $this->get_tracker_item((int) $itemId);
		$factory = $definition->getFieldFactory();

		$itemUsers = array_map(function ($userField) use ($info) {
			return isset($info[$userField]) ? $this->parse_user_field($info[$userField]) : [];
		}, $definition->getItemOwnerFields());

		if ($itemUsers) {
			$itemUsers = call_user_func_array('array_merge', $itemUsers);
		}

		$fields = [];
		foreach ($listfields as $fieldId => $fopt) {
			if (empty($fopt['fieldId'])) {
				// to accept listfield as a simple table
				$fopt['fieldId'] = $fieldId;
			}

			$fopt['trackerId'] = $trackerId;
			$fopt['itemId'] = (int)$itemId;

			$handler = $factory->getHandler($fopt, $info);
			if ($handler) {
				$get = $this->extend_GET($fopt); // extend context
				$fopt = array_merge($fopt, $handler->getFieldData());
				$fields[] = $fopt;
				$this->restore_GET($get); // restore context
			}
		}

		return($fields);
	}

	/**
	 * Make sure $_GET is extended with the $fopt (in get_item_fields) before calling $handler->getFieldData()
	 * Some trackers use tiki syntax replacement, that uses $_GET in ParserLib::parse_wiki_argvariable, extending
	 * with $fopt makes sure that that the wiki syntax parser gets the right context variables
	 *
	 * @param Array $array Values to add to $_GET
	 * @return Array a copy of the original $_GET array
	 */
	protected function extend_GET($array)
	{
		$get = $_GET;
		foreach ($array as $key => $value) {
			$_GET[$key] = $value;
		}
		return $get;
	}

	/**
	 * Use to restore the $_GET context with the copy of $_GET returned by self::extend_GET
	 *
	 * @param Array $get the array to restore as $_GET
	 */
	protected function restore_GET($get)
	{
		$_GET = $get;
	}

	public function replace_item($trackerId, $itemId, $ins_fields, $status = '', $ins_categs = 0, $bulk_import = false)
	{
		global $user, $prefs, $tiki_p_admin_trackers, $tiki_p_admin_users;
		$final_event = 'tiki.trackeritem.update';

		$transaction = $this->begin();

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

		if (! empty($itemId)) {	// check the item really exists
			$itemId = (int) $this->items()->fetchOne('itemId', [ 'itemId' => $itemId]);
		}

		$fil = [];
		if (! empty($itemId)) {
			$fil = $itemFields->fetchMap('fieldId', 'value', ['itemId' => $itemId]);
		}

		$old_values = $fil;

		$tracker_definition = Tracker_Definition::get($trackerId);

		if (method_exists($tracker_definition, 'getInformation') == false) {
			return -1;
		}

		$tracker_info = $tracker_definition->getInformation();

		if (! empty($itemId)) {
			$new_itemId = 0;
			$oldStatus = $this->items()->fetchOne('status', ['itemId' => $itemId]);

			$status = $status ? $status : $oldStatus;
			$fil['status'] = $status;
			$old_values['status'] = $oldStatus;

			if ($status != $oldStatus) {
				$this->change_status([$itemId], $status);
			} else {
				$this->update_items(
					[$itemId],
					[
						'lastModif' => $tikilib->now,
						'lastModifBy' => $user,
					],
					false
				);
			}

			$version = $this->last_log_version($itemId) + 1;
		} else {
			if (empty($status) && isset($tracker_info['newItemStatus'])) {
				// set status based on tracker setting of status not explicitly requested
				$status = $tracker_info['newItemStatus'];
			}
			if (empty($status)) {
				$status = 'o';
			}
			$fil['status'] = $status;
			$old_values['status'] = '';
			$oldStatus = '';

			$new_itemId = $items->insert(
				[
					'trackerId' => (int) $trackerId,
					'created' => $this->now,
					'createdBy' => $user,
					'lastModif' => $this->now,
					'lastModifBy' => $user,
					'status' => $status,
				]
			);

			$logslib->add_action('Created', $new_itemId, 'trackeritem');
			$version = 0;

			$final_event = 'tiki.trackeritem.create';
		}

		$currentItemId = $itemId ? $itemId : $new_itemId;
		$item_info = $this->get_item_info($currentItemId);

		if (! empty($oldStatus) || ! empty($status)) {
			if (! empty($itemId) && $oldStatus != $status) {
				 $this->log($version, $itemId, -1, $oldStatus);
			}
		}

		// If this is a user tracker it needs to be detected right here before actual looping of fields happen
		$trackersync_user = $user;
		foreach ($ins_fields["data"] as $i => $array) {
			if ($array['type'] == 'u' && isset($array['options_array'][0]) && $array['options_array'][0] == '1') {
				if ($prefs['user_selector_realnames_tracker'] == 'y' && $array['type'] == 'u') {
					if (! $userlib->user_exists($array['value'])) {
						$finalusers = $userlib->find_best_user([$array['value']], '', 'login');
						if (! empty($finalusers[0]) && ! (isset($_REQUEST['register']) && isset($_REQUEST['name']) && $_REQUEST['name'] == $array['value'])) {
							// It could be in fact that a new user is required (when no match is found or during registration even if match is found)
							$ins_fields['data'][$i]['value'] = $finalusers[0];
						}
					}
				}
				$trackersync_user = $array['value'];
			}
		}

		$final = [];
		$postSave = [];
		$suppliedFields = [];

		foreach ($ins_fields["data"] as $i => $array) {
			// Old values were prefilled at the begining of the function and only replaced at the end of the iteration
			$fieldId = $array['fieldId'];
			$suppliedFields[] = $fieldId;
			$old_value = isset($fil[$fieldId]) ? $fil[$fieldId] : null;

			$handler = $this->get_field_handler($array, array_merge($item_info, $fil));

			if (method_exists($handler, 'postSaveHook')) {
				// postSaveHook will be called with final value saved
				// after saving all item fields
				$postSave[] = [
					'fieldId' => $fieldId,
					'handler' => $handler,
				];
			}

			if (method_exists($handler, 'handleFinalSave')) {
				// handleFinalSave will be called after all other fields are saved, and
				// will get as parameter all other field data (other than ones that also
				// use finalSave).
				$final[] = [
					'field' => $array,
					'handler' => $handler,
				];
				continue;
			}

			if (method_exists($handler, 'handleSave')) {
				$array = array_merge($array, $handler->handleSave(! isset($array['value']) ? null : $array['value'], $old_value));
				$value = ! isset($array['value']) ? null : $array['value'];

				if ($value !== false) {
					$this->modify_field($currentItemId, $array['fieldId'], $value);

					if ($itemId && $old_value != $value) {
						// On update, save old value
						$this->log($version, $itemId, $array['fieldId'], $old_value);
					}
					$fil[$fieldId] = $value;
				}
				continue;
			}

			$value = isset($array["value"]) ? $array["value"] : null;

			if (isset($array['type']) && $array['type'] == 'p' && ($user == $trackersync_user || $tiki_p_admin_users == 'y')) {
				if ($array['options_array'][0] == 'password') {
					if (! empty($array['value']) && $prefs['change_password'] == 'y' && ($e = $userlib->check_password_policy($array['value'])) == '') {
						$userlib->change_user_password($trackersync_user, $array['value']);
					}
					if (! empty($itemId)) {
						$this->log($version, $itemId, $array['fieldId'], '?');
					}
				} elseif ($array['options_array'][0] == 'email') {
					if (! empty($array['value']) && validate_email($array['value']) && ($prefs['user_unique_email'] != 'y' || ! $userlib->other_user_has_email($trackersync_user, $array['value']))) {
						$old_value = $userlib->get_user_email($trackersync_user);
						$userlib->change_user_email($trackersync_user, $array['value']);
					}
					if (! empty($itemId) && $old_value != $array['value']) {
						$this->log($version, $itemId, $array['fieldId'], $old_value);
					}
				} else {
					$old_value = $tikilib->get_user_preference($trackersync_user, $array['options_array'][0]);
					$tikilib->set_user_preference($trackersync_user, $array['options_array'][0], $array['value']);
					if (! empty($itemId) && $old_value != $array['value']) {
						$this->log($version, $itemId, $array['fieldId'], $array['value']);
					}
				}
				// Should not store value in tracker database as it won't be reliable (what if pref is changed afterwards?)
				$value = '';
				$fil[$fieldId] = $value;
				$this->modify_field($currentItemId, $array['fieldId'], $value);
			} elseif (isset($array['type']) && $array['type'] == 'k') { //page selector
				if ($array['value'] != '') {
					$this->modify_field($currentItemId, $array['fieldId'], $value);
					if ($itemId) {
						// On update, save old value
						$this->log($version, $itemId, $array['fieldId'], $old_value);
					}
					$fil[$fieldId] = $value;
					if (! $this->page_exists($array['value'])) {
						$opts = $array['options_array'];
						if (! empty($opts[2])) {
							$IP = $this->get_ip_address();
							$info = $this->get_page_info($opts[2]);
							$this->create_page($array['value'], 0, $info['data'], $this->now, '', $user, $IP, $info['description'], $info['lang'], $info['is_html'], [], $info['wysiwyg'], $info['wiki_authors_style']);
						}
					}
				}
			} else {
				$is_date = isset($array['type']) ? in_array($array["type"], ['f', 'j']) : false;

				if ($currentItemId || $array['type'] !== 'q') {	// autoincrement
					$this->modify_field($currentItemId, $fieldId, $value);
					if ($old_value != $value) {
						if ($is_date) {
							$dformat = $prefs['short_date_format'] . ' ' . $prefs['short_time_format'];
							$old_value = $this->date_format($dformat, (int) $old_value);
							$new_value = $this->date_format($dformat, (int) $value);
						} else {
							$new_value = $value;
						}
						if ($old_value != $new_value && ! empty($itemId) &&
								$array['type'] !== 'W' // not for webservices
								) {
							$this->log($version, $itemId, $array['fieldId'], $old_value);
						}
					}
				}

				$fil[$fieldId] = $value;
			}
		}

		// get permnames
		$permNames = [];
		foreach ($fil as $fieldId => $value) {
			$field = $tracker_definition->getField($fieldId);
			if ($field['type'] !== 'W') {    // not for webservices
				$permNames[$fieldId] = $field['permName'];
			} else {
				unset($fil[$fieldId], $old_values[$fieldId]);	// webservice values are just a cache and not useful for diffs etc
			}
		}

		if (count($final)) {
			$data = [];
			foreach ($fil as $fieldId => $value) {
				$data[$permNames[$fieldId]] = $value;
			}

			foreach ($final as $job) {
				$value = $job['handler']->handleFinalSave($data);
				$data[$job['field']['permName']] = $value;
				$this->modify_field($currentItemId, $job['field']['fieldId'], $value);
			}
		}

		foreach ($postSave as $job) {
			$value = $fil[$job['fieldId']];
			$job['handler']->postSaveHook($value);
		}

		$values_by_permname = [];
		$old_values_by_permname = [];
		foreach ($fil as $fieldId => $value) {
			$values_by_permname[$permNames[$fieldId]] = $value;
		}
		foreach ($old_values as $fieldId => $value) {
			$old_values_by_permname[$permNames[$fieldId]] = $value;
		}

		$arguments = [
			'type' => 'trackeritem',
			'object' => $currentItemId,
			'user' => $GLOBALS['user'],
			'version' => $version,
			'trackerId' => $trackerId,
			'supplied' => $suppliedFields,
			'values' => $fil,
			'old_values' => $old_values,
			'values_by_permname' => $values_by_permname,
			'old_values_by_permname' => $old_values_by_permname,
			'bulk_import' => $bulk_import,
			'aggregate' => sha1("trackeritem/$currentItemId"),
		];

		$encoded = json_encode($arguments);

		// each field can be 64KB but \ActivityLib::recordEvent tries to store all these args in a BLOB
		if (strlen($encoded) >= 65535) {
			unset($arguments['values'], $arguments['old_values']);
			$encoded = json_encode($arguments);
		}

		if (strlen($encoded) >= 65535) {
			unset($arguments['values_by_permname'], $arguments['old_values_by_permname']);	// fields are duplicated sadly
		}

		TikiLib::events()->trigger(
			$final_event,
			$arguments
		);

		$transaction->commit();

		return $currentItemId;
	}

	public function modify_field($itemId, $fieldId, $value)
	{
		$conditions = [
			'itemId' => (int) $itemId,
			'fieldId' => (int) $fieldId,
		];

		$this->itemFields()->insertOrUpdate(['value' => $value], $conditions);
	}

	public function groupName($tracker_info, $itemId)
	{
		if (empty($tracker_info['autoCreateGroupInc'])) {
			$groupName = $tracker_info['name'];
		} else {
			$userlib = TikiLib::lib('user');
			$group_info = $userlib->get_groupId_info($tracker_info['autoCreateGroupInc']);
			$groupName = $group_info['groupName'];
		}
		return "$groupName $itemId";
	}

	public function _format_data($field, $data)
	{
		$data = trim($data);
		if ($field['type'] == 'a') {
			if (isset($field["options_array"][3]) and $field["options_array"][3] > 0 and strlen($data) > $field["options_array"][3]) {
				$data = substr($data, 0, $field["options_array"][3]) . " (...)";
			}
		} elseif ($field['type'] == 'c') {
			if ($data != 'y') {
				$data = 'n';
			}
		}
		return $data;
	}

	/**
	 * Called from tiki-list_trackers.php import button
	 *
	 * @param int $trackerId
	 * @param resource $csvHandle file handle to import
	 * @param bool $replace_rows make new items for those with existing itemId
	 * @param string $dateFormat used for item fields of type date
	 * @param string $encoding defaults "UTF8"
	 * @param string $csvDelimiter defaults to ","
	 * @param bool $updateLastModif default true
	 * @param bool $convertItemLinkValues default false		attempts to find a linked or related item for ItemLink and Relations fields
	 * @return number items imported
	 */
	public function import_csv($trackerId, $csvHandle, $replace_rows = true, $dateFormat = '', $encoding = 'UTF8', $csvDelimiter = ',', $updateLastModif = true, $convertItemLinkValues = false)
	{
		$tikilib = TikiLib::lib('tiki');
		$unifiedsearchlib = TikiLib::lib('unifiedsearch');

		$items = $this->items();
		$itemFields = $this->itemFields();

		$tracker_info = $this->get_tracker_options($trackerId);
		if (($header = fgetcsv($csvHandle, 100000, $csvDelimiter)) === false) {
			return 'Illegal first line';
		}
		if ($encoding == 'UTF-8') {
			// See en.wikipedia.org/wiki/Byte_order_mark
			if (substr($header[0], 0, 3) == "\xef\xbb\xbf") {
				$header[0] = substr($header[0], 3);
			}
		}
		$max = count($header);
		if ($max === 1 and strpos($header, "\t") !== false) {
			Feedback::error(tr('No fields found in header, not a comma separated values file?'), 'session');
			return 0;
		}
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
		$need_reindex = [];
		$fields = $this->list_tracker_fields($trackerId, 0, -1, 'position_asc', '');

		// prepare autoincrement fields
		$auto_fields = [];
		foreach ($fields['data'] as $field) {
			if ($field['type'] === 'q') {
				$auto_fields[(int) $field['fieldId']] = $field;
			}
		}

		// prepare ItemLink fields
		if ($convertItemLinkValues) {
			$itemlink_options = [];
			foreach ($fields['data'] as $field) {
				if ($field['type'] === 'r') {
					$itemlink_options[(int) $field['fieldId']] = $field['options_array'];
				}
			}
		}

		// mandatory fields check
		$utilities = new \Services_Tracker_Utilities;
		$definition = Tracker_Definition::get($trackerId);
		$line = 0;
		$errors = [];
		while (($data = fgetcsv($csvHandle, 100000, $csvDelimiter)) !== false) {
			$line++;
			if ($encoding == 'ISO-8859-1') {
				for ($i = 0; $i < $max; $i++) {
					$data[$i] = utf8_encode($data[$i]);
				}
			}
			$itemId = 0;
			$datafields = [];
			for ($i = 0; $i < $max; ++$i) {
				if ($header[$i] == 'itemId') {
					$itemId = $data[$i];
				}
				if (! preg_match('/ -- $/', $header[$i])) {
					continue;
				}
				$h = preg_replace('/ -- $/', '', $header[$i]);
				foreach ($fields['data'] as $field) {
					if ($field['name'] == $h) {
						$datafields[$field['permName']] = $data[$i];
					}
				}
			}
			$lineErrors = $utilities->validateItem($definition, ['itemId' => $itemId, 'fields' => $datafields]);
			foreach ($lineErrors as $error) {
				$errors[] = tr('Line %0:', $line) . ' ' . $error;
			}
		}

		if (count($errors) > 0) {
			Feedback::error([
				'title' => tr('Import file contains errors. Please review and fix before importing.'),
				'mes' => $errors
			]);
			return 0;
		}

		// back to first row excluding header
		fseek($csvHandle, 0);
		fgetcsv($csvHandle, 100000, $csvDelimiter);

		while (($data = fgetcsv($csvHandle, 100000, $csvDelimiter)) !== false) {
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
					if ($data[$i] == 'o' || $data[$i] == 'p' || $data[$i] == 'c') {
						$status = $data[$i];
					}
				} elseif ($header[$i] == 'itemId') {
					$itemId = $data[$i];
				} elseif ($header[$i] == 'created') {
					$created = $this->parse_imported_date($data[$i], $dateFormat);
					;
				} elseif ($header[$i] == 'lastModif') {
					$lastModif = $this->parse_imported_date($data[$i], $dateFormat);
				} elseif ($header[$i] == 'categs') { // for old compatibility
					$cats = preg_split('/,/', trim($data[$i]));
				}
			}
			$t = $this->get_tracker_for_item($itemId);
			if ($itemId && $t && $t == $trackerId && $replace_rows) {
				if (in_array('status', $header)) {
					$update['status'] = $status;
				}
				if (in_array('created', $header)) {
					$update['created'] = (int) $created;
				}
				if ($updateLastModif) {
					$update['lastModif'] = (int) $lastModif;
				}
				if (! empty($update)) {
					$items->update($update, ['itemId' => (int) $itemId]);
				}
			} else {
				$itemId = $items->insert(
					[
						'trackerId' => (int) $trackerId,
						'created' => (int) $created,
						'lastModif' => (int) $lastModif,
						'status' => $status,
					]
				);
				if (empty($itemId) || $itemId < 1) {
					Feedback::error(tr(
						'Problem inserting tracker item: trackerId=%0, created=%1, lastModif=%2, status=%3',
						$trackerId,
						$created,
						$lastModif,
						$status
					), 'session');
				} else {
					// deal with autoincrement fields
					foreach ($auto_fields as $afield) {
						$auto_handler = $this->get_field_handler($afield, $this->get_item_info($itemId));
						if (! empty($auto_handler)) {
							$auto_val = $auto_handler->handleSave(null, null);
							$itemFields->insert(['itemId' => (int) $itemId, 'fieldId' => (int) $afield['fieldId'], 'value' => $auto_val['value']]);
						}
					}
				}
			}
			$need_reindex[] = $itemId;
			if (! empty($cats)) {
				$this->categorized_item($trackerId, $itemId, "item $itemId", $cats);
			}
			for ($i = 0; $i < $max; ++$i) {
				if (! preg_match('/ -- $/', $header[$i])) {
					continue;
				}
				$h = preg_replace('/ -- $/', '', $header[$i]);
				foreach ($fields['data'] as $field) {
					if ($field['name'] == $h) {
						if ($field['type'] == 'p' && $field['options_array'][0] == 'password') {
							//$userlib->change_user_password($user, $ins_fields['data'][$i]['value']);
							continue;
						}

						if ($data[$i] === 'NULL') {
							$data[$i] = '';
						}
						// remove escaped quotes \" etc
						$data[$i] = stripslashes($data[$i]);

						switch ($field['type']) {
							case 'e':
								$cats = preg_split('/%%%/', trim($data[$i]));
								$catIds = [];
								if (! empty($cats)) {
									foreach ($cats as $c) {
										$categlib = TikiLib::lib('categ');
										if ($cId = $categlib->get_category_id(trim($c))) {
											$catIds[] = $cId;
										}
									}
									if (! empty($catIds)) {
										$this->categorized_item($trackerId, $itemId, "item $itemId", $catIds);
									}
								}
								$data[$i] = '';
								break;
							case 's':
								$data[$i] = '';
								break;
							case 'y':	// Country selector
								$data[$i] = preg_replace('/ /', "_", $data[$i]);
								break;
							case 'a':
								$data[$i] = preg_replace('/\%\%\%/', "\r\n", $data[$i]);
								break;
							case 'c':
								if (strtolower($data[$i]) == 'yes' || strtolower($data[$i]) == 'on' || $data[$i] == 1 || strtolower($data[$i]) == 'y') {
									$data[$i] = 'y';
								} else {
									$data[$i] = 'n';
								}
								break;
							case 'f':
							case 'j':
								$data[$i] = $this->parse_imported_date($data[$i], $dateFormat);
								break;
							case 'r':
								if ($convertItemLinkValues && $data[$i]) {
									$val = $this->get_item_id(
										$itemlink_options[$field['fieldId']][0], // other trackerId (option 0)
										$itemlink_options[$field['fieldId']][1], // other fieldId (option 1)
										$data[$i]									// value
									);
									if ($val !== null) {
										$data[$i] = $val;
									} else {
										Feedback::error(
											tr(
												'Problem converting tracker item link field: trackerId=%0, fieldId=%1, itemId=%2',
												$trackerId,
												$field['fieldId'],
												$itemId
											),
											'session'
										);
									}
								}
								break;
							case 'REL':	// Relations
								if ($convertItemLinkValues && $data[$i] && ! $field['options_map']['readonly']) {
									$filter = [];
									$results = [];

									parse_str($field['options_map']['filter'], $filter);
									$filter['title'] = $data[$i];

									$query = $unifiedsearchlib->buildQuery($filter);
									$query->setRange(0, 1);

									try {
										$results = $query->search($unifiedsearchlib->getIndex());
									} catch (Search_Elastic_TransportException $e) {
										Feedback::error(tr('Search functionality currently unavailable.'), 'session');
									} catch (Exception $e) {
										Feedback::error($e->getMessage(), 'session');
									}

									if (count($results)) {
										$data[$i] = $results[0]['object_id'];
										TikiLib::lib('relation')->add_relation($field['options_map']['relation'], 'trackeritem', $itemId, $results[0]['object_type'], $data[$i]);
									} else {
										Feedback::error(
											tr(
												'Problem converting tracker relation field: trackerId=%0, fieldId=%1, itemId=%2 from value "%3"',
												$trackerId,
												$field['fieldId'],
												$itemId,
												$data[$i]
											),
											'session'
										);
									}
								}
								break;
						}

						if ($this->get_item_value($trackerId, $itemId, $field['fieldId']) !== false) {
							$itemFields->update(['value' => $data[$i]], ['itemId' => (int) $itemId, 'fieldId' => (int) $field['fieldId']]);
						} else {
							$itemFields->insert(['itemId' => (int) $itemId, 'fieldId' => (int) $field['fieldId'], 'value' => $data[$i]]);
						}
						break;
					}
				}
			}
			$total++;
		}

		$cant_items = $items->fetchCount(['trackerId' => (int) $trackerId]);
		$this->trackers()->update(['items' => (int) $cant_items, 'lastModif' => $this->now], ['trackerId' => (int) $trackerId]);

		global $prefs;
		if ($prefs['feature_search'] === 'y' && $prefs['unified_incremental_update'] === 'y') {
			$unifiedsearchlib = TikiLib::lib('unifiedsearch');

			foreach ($need_reindex as $id) {
				$unifiedsearchlib->invalidateObject('trackeritem', $id);
			}
			$unifiedsearchlib->processUpdateQueue();
		}

		return $total;
	}

	function parse_imported_date($dateString, $dateFormat)
	{

		$tikilib = TikiLib::lib('tiki');
		$date = 0;

		if (is_numeric($dateString)) {
			$date = (int)$dateString;
		} elseif ($dateFormat == 'mm/dd/yyyy') {
			list($m, $d, $y) = preg_split('#/#', $dateString);
			if ($y && $m && $d) {
				$date = $tikilib->make_time(0, 0, 0, $m, $d, $y);
			}
		} elseif ($dateFormat == 'dd/mm/yyyy') {
			list($d, $m, $y) = preg_split('#/#', $dateString);
			if ($y && $m && $d) {
				$date = $tikilib->make_time(0, 0, 0, $m, $d, $y);
			}
		} elseif ($dateFormat == 'yyyy-mm-dd') {
			list($y, $m, $d) = preg_split('#-#', $dateString);
			if ($y && $m && $d) {
				$date = $tikilib->make_time(0, 0, 0, $m, $d, $y);
			}
		}

		if (! $date) {    // previous attempts failed, try a more flexible approach
			$date = strtotime($dateString);
		}

		return $date;
	}

	public function dump_tracker_csv($trackerId)
	{
		$tikilib = TikiLib::lib('tiki');
		$tracker_info = $this->get_tracker_options($trackerId);
		$fields = $this->list_tracker_fields($trackerId, 0, -1, 'position_asc', '');

		$trackerId = (int) $trackerId;

		// write out file header
		session_write_close();
		$this->write_export_header('UTF-8', $trackerId);

		// then "field names -- index" as first line
		$str = '';
		$str .= 'itemId,status,created,lastModif,';	// these headings weren't quoted in the previous export function
		if (count($fields['data']) > 0) {
			foreach ($fields['data'] as $field) {
				$str .= '"' . $field['name'] . ' -- ' . $field['fieldId'] . '",';
			}
		}
		echo $str;

		// prepare queries
		$mid = ' WHERE tti.`trackerId` = ? ';
		$bindvars = [$trackerId];
		$join = '';

		$query_items = 'SELECT tti.itemId, tti.status, tti.created, tti.lastModif'
						. ' FROM `tiki_tracker_items` tti'
						. $mid
						. ' ORDER BY tti.`itemId` ASC';
		$query_fields = 'SELECT tti.itemId, ttif.`value`, ttf.`type`'
						. ' FROM ('
						. ' `tiki_tracker_items` tti'
						. ' INNER JOIN `tiki_tracker_item_fields` ttif ON tti.`itemId` = ttif.`itemId`'
						. ' INNER JOIN `tiki_tracker_fields` ttf ON ttf.`fieldId` = ttif.`fieldId`'
						. ')'
						. $mid
						. ' ORDER BY tti.`itemId` ASC, ttf.`position` ASC';
		$base_tables = '('
			. ' `tiki_tracker_items` tti'
			. ' INNER JOIN `tiki_tracker_item_fields` ttif ON tti.`itemId` = ttif.`itemId`'
			. ' INNER JOIN `tiki_tracker_fields` ttf ON ttf.`fieldId` = ttif.`fieldId`'
			. ')' . $join;


		$query_cant = 'SELECT count(DISTINCT ttif.`itemId`) FROM ' . $base_tables . $mid;
		$cant = $this->getOne($query_cant, $bindvars);

		$avail_mem = $tikilib->get_memory_avail();
		$maxrecords_items = intval(($avail_mem - 10 * 1024 * 1025) / 5000);		// depends on size of items table (fixed)
		if ($maxrecords_items < 0) {
			// cope with memory_limit = -1
			$maxrecords_items = -1;
		}
		$offset_items = 0;

		$items = $this->get_dump_items_array($query_items, $bindvars, $maxrecords_items, $offset_items);

		$avail_mem = $tikilib->get_memory_avail();							// update avail after getting first batch of items
		$maxrecords = (int) ($avail_mem / 40000) * count($fields['data']);	// depends on number of fields
		if ($maxrecords < 0) {
			// cope with memory_limit = -1
			$maxrecords = $cant * count($fields['data']);
		}
		$canto = $cant * count($fields['data']);
		$offset = 0;
		$lastItem = -1;
		$count = 0;
		$icount = 0;
		$field_values = [];

		// write out rows
		for ($offset = 0; $offset < $canto; $offset = $offset + $maxrecords) {
			$field_values = $this->fetchAll($query_fields, $bindvars, $maxrecords, $offset);
			$mem = memory_get_usage(true);

			foreach ($field_values as $res) {
				if ($lastItem != $res['itemId']) {
					$lastItem = $res['itemId'];
					echo "\n" . $items[$lastItem]['itemId'] . ',' . $items[$lastItem]['status'] . ',' . $items[$lastItem]['created'] . ',' . $items[$lastItem]['lastModif'] . ',';	// also these fields weren't traditionally escaped
					$count++;
					$icount++;
					if ($icount > $maxrecords_items && $maxrecords_items > 0) {
						$offset_items += $maxrecords_items;
						$items = $this->get_dump_items_array($query_items, $bindvars, $maxrecords_items, $offset_items);
						$icount = 0;
					}
				}
				echo '"' . str_replace(['"', "\r\n", "\n"], ['\\"', '%%%', '%%%'], $res['value']) . '",';
			}
			ob_flush();
			flush();
			//if ($offset == 0) { $maxrecords = 1000 * count($fields['data']); }
		}
		echo "\n";
		ob_end_flush();
	}

	public function get_dump_items_array($query, $bindvars, $maxrecords, $offset)
	{
		$items_array = $this->fetchAll($query, $bindvars, $maxrecords, $offset);
		$items = [];
		foreach ($items_array as $item) {
			$items[$item['itemId']] = $item;
		}
		unset($items_array);
		return $items;
	}

	public function write_export_header($encoding = null, $trackerId = null)
	{
		if (! $encoding) {
			$encoding = $_REQUEST['encoding'];
		}
		if (! $trackerId) {
			$trackerId = $_REQUEST['trackerId'];
		}
		if (! empty($_REQUEST['file'])) {
			if (preg_match('/.csv$/', $_REQUEST['file'])) {
				$file = $_REQUEST['file'];
			} else {
				$file = $_REQUEST['file'] . '.csv';
			}
		} else {
			$file = tra('tracker') . '_' . $trackerId . '.csv';
		}
		header("Content-type: text/comma-separated-values; charset:" . $encoding);
		header("Content-Disposition: attachment; filename=$file");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
		header("Pragma: public");
	}

	// check the validity of each field values of a tracker item
	// and the presence of mandatory fields
	public function check_field_values($ins_fields, $categorized_fields = '', $trackerId = '', $itemId = '')
	{
		global $prefs;
		$mandatory_fields = [];
		$erroneous_values = [];
		if (isset($ins_fields)&&isset($ins_fields['data'])) {
			foreach ($ins_fields['data'] as $f) {
				if ($f['type'] == 'f' && $f['isMandatory'] != 'y' && empty($f['value'])) {
					$ins_id = 'ins_' . $f['fieldId'];
					if (! empty($_REQUEST[$ins_id . 'Month']) || ! empty($_REQUEST[$ins_id . 'Day']) || ! empty($_REQUEST[$ins_id . 'Year']) ||
								! empty($_REQUEST[$ins_id . 'Hour']) || ! empty($_REQUEST[$ins_id . 'Minute'])) {
						$erroneous_values[] = $f;
					}
				}
				if ($f['type'] != 'q' and isset($f['isMandatory']) && $f['isMandatory'] == 'y') {
					if (($f['type'] == 'e' || in_array($f['fieldId'], $categorized_fields)) && empty($f['value'])) {	// category: value is now categ id's

						$mandatory_fields[] = $f;
					} elseif (in_array($f['type'], ['a', 't']) && ($this->is_multilingual($f['fieldId']) == 'y')) {
						if (! isset($multi_languages)) {
							$multi_languages = $prefs['available_languages'];
						}
						//Check recipient
						if (isset($f['lingualvalue'])) {
							foreach ($f['lingualvalue'] as $val) {
								foreach ($multi_languages as $num => $tmplang) {
									//Check if trad is empty
									if (! isset($val['lang']) ||! isset($val['value']) ||(($val['lang'] == $tmplang) && strlen($val['value']) == 0)) {
										$mandatory_fields[] = $f;
									}
								}
							}
						} elseif (is_array($f['value'])) {
							foreach ($f['value'] as $key => $val) {
								foreach ($multi_languages as $num => $tmplang) {
									if ($key == $tmplang && empty($val)) {
										$mandatory_fields[] = $f;
									}
								}
							}
						} else {
							$mandatory_fields[] = $f;
						}
					} elseif (in_array($f['type'], ['u', 'g']) && $f['options_array'][0] == 1) {
						;
					} elseif ($f['type'] == 'c' && (empty($f['value']) || $f['value'] == 'n')) {
						$mandatory_fields[] = $f;
					} elseif ($f['type'] == 'A' && ! empty($itemId) && empty($f['value'])) {
						$val = $this->get_item_value($trackerId, $itemId, $f['fieldId']);
						if (empty($val)) {
							$mandatory_fields[] = $f;
						}
					} elseif (! isset($f['value']) || ! is_array($f['value']) && strlen($f['value']) == 0 || is_array($f['value']) && empty($f['value'])) {
						$mandatory_fields[] = $f;
					}
				}
				if (! empty($f['value'])) {
					switch ($f['type']) {
						// IP address (only for IPv4)
						case 'I':
							$validator = new Zend\Validator\Ip;
							if (! $validator->isValid($f['value'])) {
								$erroneous_values[] = $f;
							}
							break;
						// numeric
						case 'n':
							if (! is_numeric($f['value'])) {
								$f['error'] = tra('Field is not numeric');
								$erroneous_values[] = $f;
							}
							break;

						// email
						case 'm':
							if (! validate_email($f['value'], $prefs['validateEmail'])) {
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
								if (! validate_email($f['value'])) {
									$erroneous_values[] = $f;
								}
							}
							break;
						case 'a':
							if (isset($f['options_array'][5]) && $f['options_array'][5] > 0) {
								if (count(preg_split('/\s+/', trim($f['value']))) > $f['options_array'][5]) {
									$erroneous_values[] = $f;
								}
							}
							if (isset($f['options_array'][6]) && $f['options_array'][6] == 'y') {
								if (in_array($f['value'], $this->list_tracker_field_values($trackerId, $f['fieldId'], 'opc', 'y', '', $itemId))) {
									$erroneous_values[] = $f;
								}
							}
							break;
					}

					$handler = $this->get_field_handler($f, $this->get_item_info($itemId));
					if (method_exists($handler, 'isValid')) {
						$validationResponse = $handler->isValid($ins_fields['data']);
						if ($validationResponse !== true) {
							if (! empty($f['validationMessage'])) {
								$f['errorMsg'] = $f['validationMessage'];
							} elseif (! empty($validationResponse)) {
								$f['errorMsg'] = $validationResponse;
							} else {
								$f['errorMsg'] = tr('Unknown error');
							}
							$erroneous_values[] = $f;
						}
					}
				}
			}
		}

		$res = [];
		$res['err_mandatory'] = $mandatory_fields;
		$res['err_value'] = $erroneous_values;
		return $res;
	}

	public function remove_tracker_item($itemId, $bulk_mode = false)
	{
		global $user, $prefs;
		$res = $this->items()->fetchFullRow(['itemId' => (int) $itemId]);
		$trackerId = $res['trackerId'];
		$status = $res['status'];

		// keep copy of item for putting info into final event
		$itemInfo = $this->get_tracker_item($itemId);

		// ---- save image list before sql query ---------------------------------
		$fieldList = $this->list_tracker_fields($trackerId, 0, -1, 'name_asc', '');

		$statusTypes = $this->status_types();
		$statusString = isset($statusTypes[$status]['label']) ? $statusTypes[$status]['label'] : '';

		$imgList = [];
		foreach ($fieldList['data'] as $f) {
			$data_field[] = ['name' => tr($f['name']),'value' => $this->get_item_value($trackerId, $itemId, $f['fieldId'])];
			if ($f['type'] == 'i') {
				$imgList[] = $this->get_item_value($trackerId, $itemId, $f['fieldId']);
			}
		}

		if (! $bulk_mode) {
			$watchers = $this->get_notification_emails($trackerId, $itemId, $this->get_tracker_options($trackerId));

			if (count($watchers > 0)) {
				$smarty = TikiLib::lib('smarty');
				$trackerName = $this->trackers()->fetchOne('name', ['trackerId' => (int) $trackerId]);
				$smarty->assign('mail_date', $this->now);
				$smarty->assign('mail_user', $user);
				$smarty->assign('mail_action', 'deleted');
				$smarty->assign('mail_itemId', $itemId);
				$smarty->assign('mail_item_desc', $itemId);
				$smarty->assign('mail_fields', $data_field);
				$smarty->assign('mail_field_status', $statusString);
				$smarty->assign('mail_trackerId', $trackerId);
				$smarty->assign('mail_trackerName', $trackerName);
				$smarty->assign('mail_data', '');
				$foo = parse_url($_SERVER["REQUEST_URI"]);
				$machine = $this->httpPrefix(true) . $foo["path"];
				$smarty->assign('mail_machine', $machine);
				$parts = explode('/', $foo['path']);
				if (count($parts) > 1) {
					unset($parts[count($parts) - 1]);
				}
				$smarty->assign('mail_machine_raw', $this->httpPrefix(true) . implode('/', $parts));
				if (! isset($_SERVER["SERVER_NAME"])) {
					$_SERVER["SERVER_NAME"] = $_SERVER["HTTP_HOST"];
				}
				include_once('lib/webmail/tikimaillib.php');
				$smarty->assign('server_name', $_SERVER['SERVER_NAME']);
				foreach ($watchers as $w) {
					$mail = new TikiMail($w['user']);

					if (! isset($w['template'])) {
						$w['template'] = '';
					}
					$content = $this->parse_notification_template($w['template']);

					$mail->setSubject($smarty->fetchLang($w['language'], $content['subject']));
					$mail_data = $smarty->fetchLang($w['language'], $content['template']);
					if (isset($w['templateFormat']) && $w['templateFormat'] == 'html') {
						$mail->setHtml($mail_data, str_replace('&nbsp;', ' ', strip_tags($mail_data)));
					} else {
						$mail->setText(str_replace('&nbsp;', ' ', strip_tags($mail_data)));
					}
					$mail->send([$w['email']]);
				}
			}
		}

		// remove the object and uncategorize etc while the item still exists
		$this->remove_object("trackeritem", $itemId);
		$itemFields = $this->itemFields()->fetchAll(['fieldId'], ['itemId' => $itemId]);
		foreach ($itemFields as $itemField) {
			$this->remove_object("trackeritemfield", sprintf("%d:%d", (int)$itemId, (int)$itemField['fieldId']));
		}

		$this->trackers()->update(
			['lastModif' => $this->now, 'items' => $this->trackers()->decrement(1)],
			['trackerId' => (int) $trackerId]
		);

		$this->itemFields()->deleteMultiple(['itemId' => (int) $itemId]);
		$this->comments()->deleteMultiple(['object' => (int) $itemId, 'objectType' => 'trackeritem']);
		$this->attachments()->deleteMultiple(['itemId' => (int) $itemId]);
		$this->groupWatches()->deleteMultiple(['object' => (int) $itemId, 'event' => 'tracker_item_modified']);
		$this->userWatches()->deleteMultiple(['object' => (int) $itemId, 'event' => 'tracker_item_modified']);
		$this->items()->delete(['itemId' => (int) $itemId]);

		$this->remove_stale_comment_watches();

		// ---- delete image from disk -------------------------------------
		foreach ($imgList as $img) {
			if (file_exists($img)) {
				unlink($img);
			}
		}

		// remove votes/ratings
		$userVotings = $this->table('tiki_user_votings');
		$userVotings->delete(['id' => $userVotings->like("tracker.$trackerId.$itemId.%")]);

		$cachelib = TikiLib::lib('cache');
		$cachelib->invalidate('trackerItemLabel' . $itemId);
		foreach ($fieldList['data'] as $f) {
			$this->invalidate_field_cache($f['fieldId']);
		}

		$options = $this->get_tracker_options($trackerId);
		if (isset($option) && isset($option['autoCreateCategories']) && $option['autoCreateCategories'] == 'y') {
			$categlib = TikiLib::lib('categ');
			$currentCategId = $categlib->get_category_id("Tracker Item $itemId");
			$categlib->remove_category($currentCategId);
		}

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

		$tx = TikiDb::get()->begin();

		$child = $this->findLinkedItems(
			$itemId,
			function ($field, $handler) use ($trackerId) {
				return $handler->cascadeDelete($trackerId);
			}
		);

		foreach ($child as $i) {
			$this->remove_tracker_item($i);
		}

		$tx->commit();

		TikiLib::events()->trigger(
			'tiki.trackeritem.delete',
			[
				'type' => 'trackeritem',
				'object' => $itemId,
				'trackerId' => $trackerId,
				'user' => $GLOBALS['user'],
				'values' => $itemInfo,
			]
		);

		return true;
	}

	public function findUncascadedDeletes($itemId, $trackerId)
	{
		$fields = [];
		$child = $this->findLinkedItems(
			$itemId,
			function ($field, $handler) use ($trackerId, & $fields) {
				if (! $handler->cascadeDelete($trackerId)) {
					$fields[] = $field['fieldId'];
					return true;
				}

				return false;
			}
		);

		return ['itemIds' => $child, 'fieldIds' => array_unique($fields)];
	}

	public function replaceItemReferences($replacement, $itemIds, $fieldIds)
	{
		$table = $this->itemFields();
		$table->update(['value' => $replacement], [
			'itemId' => $table->in($itemIds),
			'fieldId' => $table->in($fieldIds),
		]);

		$events = TikiLib::events();
		foreach ($itemIds as $itemId) {
			$events->trigger('tiki.trackeritem.update', [
				'type' => 'trackeritem',
				'object' => $itemId,
				'user' => $GLOBALS['user'],
			]);
		}
	}

	// filter examples: array('fieldId'=>array(1,2,3)) to look for a list of fields
	// array('or'=>array('isSearchable'=>'y', 'isTplVisible'=>'y')) for fields that are visible ou searchable
	// array('not'=>array('isHidden'=>'y')) for fields that are not hidden
	public function parse_filter($filter, &$mids, &$bindvars)
	{
		$tikilib = TikiLib::lib('tiki');
		foreach ($filter as $type => $val) {
			if ($type == 'or') {
				$midors = [];
				$this->parse_filter($val, $midors, $bindvars);
				$mids[] = '(' . implode(' or ', $midors) . ')';
			} elseif ($type == 'not') {
				$midors = [];
				$this->parse_filter($val, $midors, $bindvars);
				$mids[] = '!(' . implode(' and ', $midors) . ')';
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
				$mids[] = 'tti.`itemId` NOT IN(' . implode(",", array_fill(0, count($val), '?')) . ')';
				$bindvars = $val;
			} elseif (is_array($val)) {
				if (count($val) > 0) {
					if (! strstr($type, '`')) {
						$type = "`$type`";
					}
					$mids[] = "$type in (" . implode(",", array_fill(0, count($val), '?')) . ')';
					$bindvars = array_merge($bindvars, $val);
				}
			} else {
				if (! strstr($type, '`')) {
					$type = "`$type`";
				}
				$mids[] = "$type=?";
				$bindvars[] = $val;
			}
		}
	}

	// Lists all the fields for an existing tracker
	public function list_tracker_fields($trackerId, $offset = 0, $maxRecords = -1, $sort_mode = 'position_asc', $find = '', $tra_name = true, $filter = '', $fields = '')
	{
		global $prefs;
		$smarty = TikiLib::lib('smarty');
		$fieldsTable = $this->fields();

		if (! empty($trackerId)) {
			$conditions = ['trackerId' => (int) $trackerId];
		} else {
			return [];
		}
		if ($find) {
			$conditions['name'] = $fieldsTable->like("%$find%");
		}
		if (! empty($fields)) {
			$conditions['fieldId'] = $fieldsTable->in($fields);
		}

		if (! empty($filter)) {
			$mids = [];
			$bindvars = [];
			$this->parse_filter($filter, $mids, $bindvars);
			$conditions['filter'] = $fieldsTable->expr(implode(' AND ', $mids), $bindvars);
		}

		$result = $fieldsTable->fetchAll($fieldsTable->all(), $conditions, $maxRecords, $offset, $fieldsTable->sortMode($sort_mode));
		$cant = $fieldsTable->fetchCount($conditions);

		$factory = new Tracker_Field_Factory;
		foreach ($result as & $res) {
			$typeInfo = $factory->getFieldInfo($res['type']);
			$options = Tracker_Options::fromSerialized($res['options'], $typeInfo);
			$res['options_array'] = $options->buildOptionsArray();
			$res['options_map'] = $options->getAllParameters();
			$res['itemChoices'] = ( $res['itemChoices'] != '' ) ? unserialize($res['itemChoices']) : [];
			$res['visibleBy'] = ($res['visibleBy'] != '') ? unserialize($res['visibleBy']) : [];
			$res['editableBy'] = ($res['editableBy'] != '') ? unserialize($res['editableBy']) : [];
			if ($tra_name && $prefs['feature_multilingual'] == 'y' && $prefs['language'] != 'en') {
				$res['name'] = tra($res['name']);
			}
			if ($res['type'] == 'p' && $res['options_array'][0] == 'language') {
				$langLib = TikiLib::lib('language');
				$smarty->assign('languages', $langLib->list_languages());
			}
			$ret[] = $res;
		}

		return [
			'data' => $result,
			'cant' => $cant,
		];
	}

	// Inserts or updates a tracker
	public function replace_tracker($trackerId, $name, $description, $options, $descriptionIsParsed)
	{
		$trackers = $this->trackers();

		if ($descriptionIsParsed == 'y') {
			$parserlib = TikiLib::lib('parser');
			$description = $parserlib->process_save_plugins(
				$description,
				[
					'type' => 'tracker',
					'itemId' => $trackerId,
				]
			);
		}

				$data = [
			'name' => $name,
			'description' => $description,
			'descriptionIsParsed' => $descriptionIsParsed,
			'lastModif' => $this->now,
				];

				$logOption = 'Updated';
				if ($trackerId) {
					$finalEvent = 'tiki.tracker.update';
					$conditions = ['trackerId' => (int) $trackerId];
					if ($trackers->fetchCount($conditions)) {
						$trackers->update($data, $conditions);
					} else {
						$data['trackerId'] = (int) $trackerId;
						$data['items'] = 0;
						$data['created'] = $this->now;
						$trackers->insert($data);
						$logOption = 'Created';
					}
				} else {
					$finalEvent = 'tiki.tracker.create';
					$data['created'] = $this->now;
					$trackerId = $trackers->insert($data);
				}

				$wikiParsed = $descriptionIsParsed == 'y';
				TikiLib::lib('wiki')->update_wikicontent_relations($description, 'tracker', (int)$trackerId, $wikiParsed);

				$optionTable = $this->options();
				$optionTable->deleteMultiple(['trackerId' => (int) $trackerId]);

				foreach ($options as $kopt => $opt) {
					$this->replace_tracker_option((int) $trackerId, $kopt, $opt);
				}

				$definition = Tracker_Definition::get($trackerId);
				$ratingId = $definition->getRateField();

				if (isset($options['useRatings']) && $options['useRatings'] == 'y') {
					if (! $ratingId) {
						$ratingId = 0;
					}

					$ratingoptions = isset($options['ratingOptions']) ? $options['ratingOptions'] : '';
					$showratings = isset($options['showRatings']) ? $options['showRatings'] : 'n';
					$this->replace_tracker_field($trackerId, $ratingId, 'Rating', 's', '-', '-', $showratings, 'y', 'n', '-', 0, $ratingoptions);
				}
				$this->clear_tracker_cache($trackerId);
				$this->update_tracker_summary(['trackerId' => $trackerId]);

				if ($logOption) {
					$logslib = TikiLib::lib('logs');
					$logslib->add_action(
						$logOption,
						$trackerId,
						'tracker',
						[
						'name' => $data['name'],
						]
					);
				}

				TikiLib::events()->trigger($finalEvent, [
				'type' => 'tracker',
				'object' => $trackerId,
				'user' => $GLOBALS['user'],
				]);

		return $trackerId;
	}

	public function replace_tracker_option($trackerId, $name, $value)
	{
		$optionTable = $this->options();
		$optionTable->insertOrUpdate(['value' => $value], ['trackerId' => $trackerId, 'name' => $name]);
	}

	public function clear_tracker_cache($trackerId)
	{
		global $prefs;

		$cachelib = TikiLib::lib('cache');

		foreach ($this->get_all_tracker_items($trackerId) as $itemId) {
				$cachelib->invalidate('trackerItemLabel' . $itemId);
		}
		if (in_array('trackerrender', $prefs['unified_cached_formatters'])) {
			$cachelib->empty_type_cache('search_valueformatter');
		}
	}


	public function replace_tracker_field($trackerId, $fieldId, $name, $type, $isMain, $isSearchable, $isTblVisible, $isPublic, $isHidden, $isMandatory, $position, $options, $description = '', $isMultilingual = '', $itemChoices = null, $errorMsg = '', $visibleBy = null, $editableBy = null, $descriptionIsParsed = 'n', $validation = '', $validationParam = '', $validationMessage = '', $permName = null)
	{
		// Serialize choosed items array (items of the tracker field to be displayed in the list proposed to the user)
		if (is_array($itemChoices) && count($itemChoices) > 0 && ! empty($itemChoices[0])) {
			$itemChoices = serialize($itemChoices);
		} else {
			$itemChoices = '';
		}
		if (is_array($visibleBy) && count($visibleBy) > 0 && ! empty($visibleBy[0])) {
			$visibleBy = serialize($visibleBy);
		} else {
			$visibleBy = '';
		}
		if (is_array($editableBy) && count($editableBy) > 0 && ! empty($editableBy[0])) {
			$editableBy = serialize($editableBy);
		} else {
			$editableBy = '';
		}
		if ($descriptionIsParsed == 'y') {
			$parserlib = TikiLib::lib('parser');
			$description = $parserlib->process_save_plugins(
				$description,
				[
					'type' => 'trackerfield',
					'itemId' => $fieldId,
				]
			);
		}

		$fields = $this->fields();

		$data = [
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
		];

		$logOption = null;

		if ($fieldId) {
			// -------------------------------------
			// remove images when needed
			$old_field = $this->get_tracker_field($fieldId);
			if (! empty($old_field['fieldId'])) {
				if ($old_field['type'] == 'i' && $type != 'i') {
					$this->remove_field_images($fieldId);
				}

				$fields->update($data, ['fieldId' => (int) $fieldId]);
				$logOption = 'modify_field';

				$data['trackerId'] = (int) $old_field['trackerId'];
			} else {
				$data['trackerId'] = (int) $trackerId;
				$data['fieldId'] = (int) $fieldId;
				$fields->insert($data);
				$logOption = 'add_field';
			}
		} else {
			$data['trackerId'] = (int) $trackerId;
			$fieldId = $fields->insert($data);
			$logOption = 'add_field';

			if (! $permName) {
				// Apply a default value to perm name when not specified
				$fields->update(['permName' => 'f_' . $fieldId], ['fieldId' => $fieldId]);
			}

			$itemFields = $this->itemFields();
			foreach ($this->get_all_tracker_items($trackerId) as $itemId) {
				$itemFields->deleteMultiple(['itemId' => (int) $itemId, 'fieldId' => $fieldId]);
				$itemFields->insert(['itemId' => (int) $itemId, 'fieldId' => (int) $fieldId, 'value' => '']);
			}
		}

		$wikiParsed = $descriptionIsParsed == 'y';
		TikiLib::lib('wiki')->update_wikicontent_relations($description, 'trackerfield', (int)$fieldId, $wikiParsed);

		if ($logOption) {
			$logslib = TikiLib::lib('logs');
			$logslib->add_action(
				'Updated',
				$data['trackerId'],
				'tracker',
				[
					'operation' => $logOption,
					'fieldId' => $fieldId,
					'name' => $data['name'],
				]
			);

			TikiLib::events()->trigger(
				$logOption == 'add_field' ? 'tiki.trackerfield.create' : 'tiki.trackerfield.update',
				['type' => 'trackerfield', 'object' => $fieldId]
			);
		}

		$this->clear_tracker_cache($trackerId);
		return $fieldId;
	}

	public function replace_rating($trackerId, $itemId, $fieldId, $user, $new_rate)
	{
		global $tiki_p_tracker_vote_ratings, $tiki_p_tracker_revote_ratings;
		$itemFields = $this->itemFields();

		if ($new_rate === null) {
			$new_rate = 0;
		}

		if ($tiki_p_tracker_vote_ratings != 'y') {
			return;
		}
		$key = "tracker.$trackerId.$itemId";
		$olrate = $this->get_user_vote($key, $user) ?: 0;
		$allow_revote = $tiki_p_tracker_revote_ratings == 'y';
		$count = $itemFields->fetchCount(['itemId' => (int) $itemId, 'fieldId' => (int) $fieldId]);
		$tikilib = TikiLib::lib('tiki');
		if (! $tikilib->register_user_vote($user, $key, $new_rate, [], $allow_revote)) {
			return;
		}

		if (! $count) {
			$itemFields->insert(['value' => (int) $new_rate, 'itemId' => (int) $itemId, 'fieldId' => (int) $fieldId]);
			$outValue = $new_rate;
		} else {
			$conditions = [
				'itemId' => (int) $itemId,
				'fieldId' => (int) $fieldId,
			];

			$val = $itemFields->fetchOne('value', $conditions);
			$outValue = $val - $olrate + $new_rate;

			$itemFields->update(['value' => $outValue], $conditions);
		}

		TikiLib::events()->trigger('tiki.trackeritem.rating', [
			'type' => 'trackeritem',
			'object' => (int) $itemId,
			'trackerId' => (int) $trackerId,
			'fieldId' => (int) $fieldId,
			'user' => $user,
			'rating' => $new_rate, // User's selected value, not the stored one
		]);

		return $outValue;
	}

	public function replace_star($userValue, $trackerId, $itemId, &$field, $user, $updateField = true)
	{
		global $tiki_p_tracker_vote_ratings, $tiki_p_tracker_revote_ratings, $prefs;
		if ($field['type'] != '*' && $field['type'] != 'STARS') {
			return;
		}
		if ($userValue != 'NULL' && isset($field['rating_options']) && ! in_array($userValue, $field['rating_options'])) {
			return;
		}
		if ($userValue != 'NULL' && ! isset($field['rating_options']) && ! in_array($userValue, $field['options_array'])) {
			// backward compatibility with trackerlist rating which does not have rating options
			return;
		}
		if ($tiki_p_tracker_vote_ratings != 'y') {
			return;
		}
		$key = "tracker.$trackerId.$itemId." . $field['fieldId'];

		$allow_revote = $tiki_p_tracker_revote_ratings == 'y';
		$tikilib = TikiLib::lib('tiki');
		$result = $tikilib->register_user_vote($user, $key, $userValue, [], $allow_revote);

		$votings = $this->table('tiki_user_votings');
		$data = $votings->fetchRow(['count' => $votings->count(), 'total' => $votings->sum('optionId')], ['id' => $key]);
		$field['numvotes'] = $data['count'];
		$field['my_rate'] = $userValue;
		$field['voteavg'] = $field['value'] = $data['total'] / $field['numvotes'];

		if ($result) {
			TikiLib::events()->trigger('tiki.trackeritem.rating', [
				'type' => 'trackeritem',
				'object' => $itemId,
				'trackerId' => $trackerId,
				'fieldId' => $field['fieldId'],
				'user' => $user,
				'rating' => $userValue,
			]);
		}

		return $result;
	}

	public function remove_tracker($trackerId)
	{
		$transaction = $this->begin();

		// ---- delete image from disk -------------------------------------
		$fieldList = $this->list_tracker_fields($trackerId, 0, -1, 'name_asc', '');
		foreach ($fieldList['data'] as $f) {
			if ($f['type'] == 'i') {
				$this->remove_field_images($f['fieldId']);
			}
		}

		$option = $this->get_tracker_options($trackerId);
		if (isset($option) && isset($option['autoCreateCategories']) && $option['autoCreateCategories'] == 'y') {
			$categlib = TikiLib::lib('categ');
			$currentCategId = $categlib->get_category_id("Tracker $trackerId");
			$categlib->remove_category($currentCategId);
		}

		foreach ($this->get_all_tracker_items($trackerId) as $itemId) {
			$this->remove_tracker_item($itemId);
		}

		$fields = $this->fields()->fetchAll(['fieldId'], ['trackerId' => $trackerId]);
		foreach ($fields as $field) {
			$this->remove_object("trackerfield", $field['fieldId']);
		}

		$conditions = [
			'trackerId' => (int) $trackerId,
		];

		$this->fields()->deleteMultiple($conditions);
		$this->options()->deleteMultiple($conditions);
		$this->trackers()->delete($conditions);

		// remove votes/ratings
		$userVotings = $this->table('tiki_user_votings');
		$userVotings->delete(['id' => $userVotings->like("tracker.$trackerId.%")]);

		$this->remove_object('tracker', $trackerId);

		$logslib = TikiLib::lib('logs');
		$logslib->add_action('Removed', $trackerId, 'tracker');

		$this->clear_tracker_cache($trackerId);

		TikiLib::events()->trigger('tiki.tracker.delete', [
			'type' => 'tracker',
			'object' => $trackerId,
			'user' => $GLOBALS['user'],
		]);

		$transaction->commit();

		return true;
	}

	public function remove_tracker_field($fieldId, $trackerId)
	{
		$cachelib = TikiLib::lib('cache');
		$logslib = TikiLib::lib('logs');

		// -------------------------------------
		// remove images when needed
		$field = $this->get_tracker_field($fieldId);
		if ($field['type'] == 'i') {
			$this->remove_field_images($fieldId);
		}

		$handler = $this->get_field_handler($field);
		if ($handler && method_exists($handler, 'handleFieldRemove')) {
			$handler->handleFieldRemove();
		}

		$conditions = [
			'fieldId' => (int) $fieldId,
		];

		$this->fields()->delete($conditions);
		$this->itemFields()->deleteMultiple($conditions);

		$this->invalidate_field_cache($fieldId);

		$this->clear_tracker_cache($trackerId);

		$logslib = TikiLib::lib('logs');
		$logslib->add_action(
			'Updated',
			$trackerId,
			'tracker',
			[
				'operation' => 'remove_field',
				'fieldId' => $fieldId,
			]
		);
		$this->remove_object('trackerfield', $fieldId);
		TikiLib::events()->trigger(
			'tiki.trackerfield.delete',
			['type' => 'trackerfield', 'object' => $fieldId]
		);

		return true;
	}

	/**
	 * get_trackers_containing
	 *
	 * \brief Get tracker names containing ... (useful for auto-complete)
	 *
	 * @author luci
	 * @param mixed $name
	 * @access public
	 * @return
	 */
	function get_trackers_containing($name)
	{
		if (empty($name)) {
			return [];
		}
		//FIXME: perm filter ?
		$result = $this->fetchAll(
			'SELECT `name` FROM `tiki_trackers` WHERE `name` LIKE ?',
			[$name . '%'],
			10
		);
		$names = [];
		foreach ($result as $row) {
			$names[] = $row['name'];
		}
		return $names;
	}

	/**
	 * Returns the trackerId of the tracker possessing the item ($itemId)
	 */
	public function get_tracker_for_item($itemId)
	{
		return $this->items()->fetchOne('trackerId', ['itemId' => (int) $itemId]);
	}

	public function get_tracker_options($trackerId)
	{
		return $this->options()->fetchMap('name', 'value', ['trackerId' => (int) $trackerId]);
	}

	public function get_trackers_options($trackerId, $option = '', $find = '', $not = '')
	{
		$options = $this->options();
		$conditions = [];

		if (! empty($trackerId)) {
			$conditions['trackerId'] = (int) $trackerId;
		}

		if (! empty($option)) {
			$conditions['name'] = $option;
		}

		if ($not == 'null' || $not == 'empty') {
			$conditions['value'] = $options->not('');
		}

		if (! empty($find)) {
			$conditions['value'] = $options->like("%$find%");
		}

		return $options->fetchAll($options->all(), $conditions);
	}

	public function get_tracker_field($fieldIdOrPermName)
	{
		static $cache = [];
		if (isset($cache[$fieldIdOrPermName])) {
			return $cache[$fieldIdOrPermName];
		}
		if (intval($fieldIdOrPermName) > 0) {
			$res = $this->fields()->fetchFullRow(['fieldId' => intval($fieldIdOrPermName)]);
		} else {
			$res = $this->fields()->fetchFullRow(['permName' => $fieldIdOrPermName]);
		}
		if ($res) {
			$factory = new Tracker_Field_Factory;
			$options = Tracker_Options::fromSerialized($res['options'], $factory->getFieldInfo($res['type']));
			$res['options_array'] = $options->buildOptionsArray();
			$res['itemChoices'] = ! empty($res['itemChoices']) ? unserialize($res['itemChoices']) : [];
			$res['visibleBy'] = ! empty($res['visibleBy']) ? unserialize($res['visibleBy']) : [];
			$res['editableBy'] = ! empty($res['editableBy']) ? unserialize($res['editableBy']) : [];
			if (TikiLib::lib('tiki')->get_memory_avail() < 1048576 * 10) {
				$cache = [];
			}
			$cache[$fieldIdOrPermName] = $res;
			return $res;
		}
	}

	public function get_field_id($trackerId, $name, $lookup = 'name')
	{
		return $this->fields()->fetchOne('fieldId', ['trackerId' => (int) $trackerId, $lookup => $name]);
	}

	/**
	 * Return a tracker field id from it's type. By default
	 * it return only the first field of the searched type.
	 *
	 * @param int $trackerId tracker id
	 * @param string $type field type (in general an one letter code)
	 * @param string $option a value (or values separated by comma) that a tracker field must have in its options (it will be used inside a LIKE statement so most of the times it is a good idea to use %)
	 * @param bool $first if true return only the first field of the searched type, if false return all the fields of the searched type
	 * @param string $name filter by tracker field name
	 * @return int|array tracker field id or list of tracker fields ids
	 */
	public function get_field_id_from_type($trackerId, $type, $option = null, $first = true, $name = null)
	{
		static $memo;
		if (! is_array($type) && isset($memo[$trackerId][$type][$option])) {
			return $memo[$trackerId][$type][$option];
		}

		$conditions = [
			'trackerId' => (int) $trackerId,
		];
		$fields = $this->fields();

		if (is_array($type)) {
			$conditions['type'] = $fields->in($type, true);
		} else {
			$conditions['type'] = $fields->exactly($type);
		}

		if (! empty($option)) {
			throw new Exception("\$option parameter no longer supported. Code needs fixing.");
		}

		if (! empty($name)) {
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

	public function get_page_field($trackerId)
	{
		$definition = Tracker_Definition::get($trackerId);
		$score = 0;
		$out = null;

		foreach ($definition->getFields() as $field) {
			if ($field['type'] == 'k') {
				if ($score < 3 && $field['options_map']['autoassign'] == '1') {
					$score = 3;
					$out = $field;
				} elseif ($score < 2 && $field['options_map']['create'] == '1') {
					// Not sure about this one, old code used to say "has a 1 somewhere in the options string"
					// Create seems to be the most likely candidate
					$score = 2;
					$out = $field;
				} else {
					$score = 1;
					$out = $field;
				}
			}
		}

		return $out;
	}

	/*
	** function only used for the popup for more infos on attachements
	*  returns an array with field=>value
	*/
	public function get_moreinfo($attId)
	{
		$query = "select o.`value`, o.`trackerId` from `tiki_tracker_options` o";
		$query .= " left join `tiki_tracker_items` i on o.`trackerId`=i.`trackerId` ";
		$query .= " left join `tiki_tracker_item_attachments` a on i.`itemId`=a.`itemId` ";
		$query .= " where a.`attId`=? and o.`name`=?";
		$result = $this->query($query, [(int) $attId, 'orderAttachments']);
		$resu = $result->fetchRow();
		if ($resu) {
			$resu['orderAttachments'] = $resu['value'];
		} else {
			$query = "select `orderAttachments`, t.`trackerId` from `tiki_trackers` t ";
			$query .= " left join `tiki_tracker_items` i on t.`trackerId`=i.`trackerId` ";
			$query .= " left join `tiki_tracker_item_attachments` a on i.`itemId`=a.`itemId` ";
			$query .= " where a.`attId`=? ";
			$result = $this->query($query, [(int) $attId]);
			$resu = $result->fetchRow();
		}
		if (strstr($resu['orderAttachments'], '|')) {
			$fields = preg_split('/,/', substr($resu['orderAttachments'], strpos($resu['orderAttachments'], '|') + 1));
			$res = $this->attachments()->fetchRow($fields, ['attId' => (int) $attId]);
			$res["trackerId"] = $resu['trackerId'];
			$res["longdesc"] = isset($res['longdesc']) ? TikiLib::lib('parser')->parse_data($res['longdesc']) : '';
		} else {
			$res = [tra("Message") => tra("No extra information for that attached file. ")];
			$res['trackerId'] = 0;
		}
		return $res;
	}

	public function field_types()
	{

		$types = [];

		$factory = new Tracker_Field_Factory(false);
		foreach ($factory->getFieldTypes() as $key => $info) {
			$types[$key] = [
				'label' => $info['name'],
				'opt' => count($info['params']) === 0,
				'help' => $this->build_help_for_type($info),
			];
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

	public function status_types()
	{
		$status['o'] = ['name' => 'open', 'label' => tra('Open'),'perm' => 'tiki_p_view_trackers',
			'image' => 'img/icons/status_open.gif', 'iconname' => 'status-open'];
		$status['p'] = ['name' => 'pending', 'label' => tra('Pending'),'perm' => 'tiki_p_view_trackers_pending',
			'image' => 'img/icons/status_pending.gif', 'iconname' => 'status-pending'];
		$status['c'] = ['name' => 'closed', 'label' => tra('Closed'),'perm' => 'tiki_p_view_trackers_closed',
			'image' => 'img/icons/status_closed.gif', 'iconname' => 'status-closed'];
		return $status;
	}

	public function get_isMain_value($trackerId, $itemId)
	{
		global $prefs;

		$query = "select tif.`value` from `tiki_tracker_item_fields` tif, `tiki_tracker_items` i, `tiki_tracker_fields` tf where i.`itemId`=? and i.`itemId`=tif.`itemId` and tf.`fieldId`=tif.`fieldId` and tf.`isMain`=? ORDER BY tf.`position`";
		$result = $this->getOne($query, [ (int) $itemId, "y"]);

		$main_field_type = $this->get_main_field_type($trackerId);

		if (in_array($main_field_type, ['r','q'])) {	// for ItemLink and AutoIncrement fields use the proper output method
			$definition = Tracker_Definition::get($trackerId);
			$field = $definition->getField($this->get_main_field($trackerId));
			$item = $this->get_tracker_item($itemId);
			$handler = $this->get_field_handler($field, $item);
			$result = $handler->renderOutput(['list_mode' => 'csv']);
		}

		if (strlen($result) && $result{0} === '{') {
			$result = json_decode($result, true);
			if (isset($result[$prefs['language']])) {
				return $result[$prefs['language']];
			} elseif (is_array($result)) {
				return reset($result);
			}
		}

		return $result;
	}

	public function get_main_field_type($trackerId)
	{
		return $this->fields()->fetchOne('type', ['isMain' => 'y', 'trackerId' => $trackerId], ['position' => 'ASC']);
	}

	public function get_main_field($trackerId)
	{
		return $this->fields()->fetchOne('fieldId', ['isMain' => 'y', 'trackerId' => $trackerId], ['position' => 'ASC']);
	}

	public function categorized_item($trackerId, $itemId, $mainfield, $ins_categs, $parent_categs_only = [], $override_perms = false, $managed_fields = null)
	{
		global $prefs;

		// Collect the list of possible categories, those provided by a complete form
		// The update_object_categories function will limit changes to those
		$managed_categories = [];

		$definition = Tracker_Definition::get($trackerId);
		foreach ($definition->getCategorizedFields() as $t) {
			if ($managed_fields && ! in_array($t, $managed_fields)) {
				continue;
			}

			$this->itemFields()->insert(['itemId' => $itemId, 'fieldId' => $t,	'value' => ''], true);

			$field = $definition->getField($t);
			$handler = $this->get_field_handler($field);
			$data = $handler->getFieldData();
			$datalist = $data['list'];
			if (! empty($parent_categs_only)) {
				foreach ($datalist as $k => $entry) {
					$parentId = TikiLib::lib('categ')->get_category_parent($entry['categId']);
					if (! in_array($parentId, $parent_categs_only)) {
						unset($datalist[$k]);
					}
				}
			}

			$managed_categories = array_merge(
				$managed_categories,
				array_map(
					function ($entry) {
						return $entry['categId'];
					},
					$datalist
				)
			);
		}

		$this->update_item_categories($itemId, $managed_categories, $ins_categs, $override_perms);

		$items = $this->findLinkedItems(
			$itemId,
			function ($field, $handler) use ($trackerId) {
				return $handler->cascadeCategories($trackerId);
			}
		);

		$searchlib = TikiLib::lib('unifiedsearch');
		$index = $prefs['feature_search'] === 'y' && $prefs['unified_incremental_update'] === 'y';

		foreach ($items as $child) {
			$this->update_item_categories($child, $managed_categories, $ins_categs, $override_perms);

			if ($index) {
				$searchlib->invalidateObject('trackeritem', $child);
			}
		}
	}

	private function update_item_categories($itemId, $managed_categories, $ins_categs, $override_perms)
	{
		$categlib = TikiLib::lib('categ');
		$cat_desc = '';
		$cat_name = $this->get_isMain_value(null, $itemId);

		// The following needed to ensure category field exist for item (to be readable by list_items)
		// Update 2016: Needs to be the non-sefurl in case the feature is disabled later as this is stored in tiki_objects
		// and used in tiki-browse_categories.php and other places
		$cat_href = "tiki-view_tracker_item.php?itemId=$itemId";

		$categlib->update_object_categories($ins_categs, $itemId, 'trackeritem', $cat_desc, $cat_name, $cat_href, $managed_categories, $override_perms);
	}

	public function move_up_last_fields($trackerId, $fieldId, $delta = 1)
	{
		$type = ($delta > 0) ? 'increment' : 'decrement';

		$this->fields()->update(
			['position' => $this->fields()->$type(abs($delta))],
			['trackerId' => (int) $trackerId, 'fieldId' => (int) $fieldId]
		);
	}

	/* list all the values of a field
	 */
	public function list_tracker_field_values($trackerId, $fieldId, $status = 'o', $distinct = 'y', $lang = '', $exceptItemId = '')
	{
		$mid = '';
		$bindvars[] = (int) $fieldId;
		if (! $this->getSqlStatus($status, $mid, $bindvars, $trackerId)) {
			return null;
		}
		$sort_mode = "value_asc";
		$distinct = $distinct == 'y' ? 'distinct' : '';
		if (! empty($exceptItemId)) {
			$mid .= ' and ttif.`itemId` != ? ';
			$bindvars[] = $exceptItemId;
		}
		$query = "select $distinct(ttif.`value`) from `tiki_tracker_item_fields` ttif, `tiki_tracker_items` tti where tti.`itemId`= ttif.`itemId`and ttif.`fieldId`=? $mid order by " . $this->convertSortMode($sort_mode);
		$result = $this->query($query, $bindvars);
		$ret = [];
		while ($res = $result->fetchRow()) {
			$ret[] = $res['value'];
		}
		return $ret;
	}

	/* tests if a value exists in a field
	 */
	public function check_field_value_exists($value, $fieldId, $exceptItemId = 0)
	{
		$itemFields = $this->itemFields();

		$conditions = [
			'fieldId' => (int) $fieldId,
			'value' => $value,
		];

		if ($exceptItemId > 0) {
			$conditions['itemId'] = $itemFields->not((int) $exceptItemId);
		}

		return $itemFields->fetchCount($conditions) > 0;
	}

	public function is_multilingual($fieldId)
	{
		global $prefs;

		if ($fieldId < 1) {
			return 'n';
		}

		if ($prefs['feature_multilingual'] != 'y') {
			return 'n';
		}

		$is = $this->fields()->fetchOne('isMultilingual', ['fieldId' => (int) $fieldId]);

		return ($is == 'y') ? 'y' : 'n';
	}

	/* return the values of $fieldIdOut of an item that has a value $value for $fieldId */
	public function get_filtered_item_values($fieldId, $value, $fieldIdOut)
	{
		$query = "select ttifOut.`value` from `tiki_tracker_item_fields` ttifOut, `tiki_tracker_item_fields` ttif
			where ttifOut.`itemId`= ttif.`itemId`and ttif.`fieldId`=? and ttif.`value`=? and ttifOut.`fieldId`=?";
		$bindvars = [$fieldId, $value, $fieldIdOut];
		$result = $this->query($query, $bindvars);
		$ret = [];
		while ($res = $result->fetchRow()) {
			$ret[] = $res['value'];
		}
		return $ret;
	}

	/* look if a tracker has only one item per user and if an item has already being created for the user or the IP*/
	public function get_user_item(&$trackerId, $trackerOptions, $userparam = null, $user = null, $status = '')
	{
		global $prefs;
		$tikilib = TikiLib::lib('tiki');
		$userlib = TikiLib::lib('user');
		if (empty($user)) {
			$user = $GLOBALS['user'];
		}
		if (empty($trackerId) && $prefs['userTracker'] == 'y') {
			$utid = $userlib->get_tracker_usergroup($user);
			if (! empty($utid['usersTrackerId'])) {
				$trackerId = $utid['usersTrackerId'];
				$itemId = $this->get_item_id($trackerId, $utid['usersFieldId'], $user);
			}
			return $itemId;
		}

		$definition = Tracker_Definition::get($trackerId);
		$userreal = $userparam != null ? $userparam : $user;
		if (! empty($userreal)) {
			if ($fieldId = $definition->getUserField()) {
				// user creator field
				$value = $userreal;
				$items = $this->get_items_list($trackerId, $fieldId, $value, $status, true);
				if (! empty($items)) {
					return $items[0];
				}
			}
		}
		if ($fieldId = $definition->getAuthorIpField()) {
			// IP creator field
			$IP = $tikilib->get_ip_address();
			$items = $this->get_items_list($trackerId, $fieldId, $IP, $status);
			if (! empty($items)) {
				return $items[0];
			} else {
				return 0;
			}
		}
	}

	public function get_item_creators($trackerId, $itemId)
	{
		$definition = Tracker_Definition::get($trackerId);

		$owners = array_map(function ($fieldId) use ($trackerId, $itemId) {

			$owners = $this->get_item_value($trackerId, $itemId, $fieldId);
			return $this->parse_user_field($owners);
		}, $definition->getItemOwnerFields());

		if ($owners) {
			return call_user_func_array('array_merge', $owners);
		} else {
			return [];
		}
	}

	/* find the best fieldwhere you can do a filter on the initial
	 * 1) if sort_mode and sort_mode is a text and the field is visible
	 * 2) the first main taht is text
	 */
	public function get_initial_field($list_fields, $sort_mode)
	{
		if (preg_match('/^f_([^_]*)_?.*/', $sort_mode, $matches)) {
			if (isset($list_fields[$matches[1]])) {
				$type = $list_fields[$matches[1]]['type'];
				if (in_array($type, ['t', 'a', 'm'])) {
					return $matches[1];
				}
			}
		}
		foreach ($list_fields as $fieldId => $field) {
			if ($field['isMain'] == 'y' && in_array($field['type'], ['t', 'a', 'm'])) {
				return $fieldId;
			}
		}
	}

	public function get_nb_items($trackerId)
	{
		return $this->items()->fetchCount(['trackerId' => (int) $trackerId]);
	}

	public function duplicate_tracker($trackerId, $name, $description = '', $descriptionIsParsed = 'n')
	{
		$tracker_info = $this->get_tracker($trackerId);

		if ($options = $this->get_tracker_options($trackerId)) {
			$tracker_info = array_merge($tracker_info, $options);
		} else {
			$options = [];
		}

		$newTrackerId = $this->replace_tracker(0, $name, $description, [], $descriptionIsParsed);
		$fields = $this->list_tracker_fields($trackerId, 0, -1, 'position_asc', '');
		foreach ($fields['data'] as $field) {
			$newFieldId = $this->replace_tracker_field($newTrackerId, 0, $field['name'], $field['type'], $field['isMain'], $field['isSearchable'], $field['isTblVisible'], $field['isPublic'], $field['isHidden'], $field['isMandatory'], $field['position'], $field['options'], $field['description'], $field['isMultilingual'], $field['itemChoices']);
			if ($options['defaultOrderKey'] == $field['fieldId']) {
				$options['defaultOrderKey'] = $newFieldId;
			}
		}

		foreach ($options as $name => $val) {
			$this->options()->insert(['trackerId' => $newTrackerId, 'name' => $name, 'value' => $val]);
		}
		return $newTrackerId;
	}

	public function get_notification_emails($trackerId, $itemId, $options, $status = '', $oldStatus = '')
	{
		global $prefs, $user;
		$watchers_global = $this->get_event_watches('tracker_modified', $trackerId);
		$watchers_local = $this->get_local_notifications($itemId, $status, $oldStatus);
		$watchers_item = $itemId ? $this->get_event_watches('tracker_item_modified', $itemId, ['trackerId' => $trackerId]) : [];

		if ($this->get_user_preference($user, 'user_tracker_watch_editor') != "y") {
			for ($i = count($watchers_global) - 1; $i >= 0; --$i) {
				if ($watchers_global[$i]['user'] == $user) {
					unset($watchers_global[$i]);
					break;
				}
			}
			for ($i = count($watchers_local) - 1; $i >= 0; --$i) {
				if ($watchers_local[$i]['user'] == $user) {
					unset($watchers_local[$i]);
					break;
				}
			}
			for ($i = count($watchers_item) - 1; $i >= 0; --$i) {
				if ($watchers_item[$i]['user'] == $user) {
					unset($watchers_item[$i]);
					break;
				}
			}
		}

		// use daily reports feature if tracker item has been added or updated
		if ($prefs['feature_daily_report_watches'] == 'y' && ! empty($status)) {
			$reportsManager = Reports_Factory::build('Reports_Manager');
			$reportsManager->addToCache(
				$watchers_global,
				['event' => 'tracker_item_modified', 'itemId' => $itemId, 'trackerId' => $trackerId, 'user' => $user]
			);
			$reportsManager->addToCache(
				$watchers_item,
				['event' => 'tracker_item_modified', 'itemId' => $itemId, 'trackerId' => $trackerId, 'user' => $user]
			);
		}

		// use daily reports feature if a file was attached or removed from a tracker item
		if ($prefs['feature_daily_report_watches'] == 'y' && isset($options["attachment"])) {
			$reportsManager = Reports_Factory::build('Reports_Manager');
			$reportsManager->addToCache(
				$watchers_global,
				[
					'event' => 'tracker_file_attachment',
					'itemId' => $itemId,
					'trackerId' => $trackerId,
					'user' => $user,
					"attachment" => $options["attachment"]
				]
			);
			$reportsManager->addToCache(
				$watchers_item,
				[
					'event' => 'tracker_file_attachment',
					'itemId' => $itemId,
					'trackerId' => $trackerId,
					'user' => $user,
					'attachment' => $options['attachment']
				]
			);
		}

		$watchers_outbound = [];
		if (array_key_exists("outboundEmail", $options) && $options["outboundEmail"]) {
			$emails3 = preg_split('/,/', $options['outboundEmail']);
			foreach ($emails3 as $w) {
				global $user_preferences;
				$tikilib = TikiLib::lib('tiki');
				$userlib = TikiLib::lib('user');
				$u = $userlib->get_user_by_email($w);
				$tikilib->get_user_preferences($u, ['user', 'language', 'mailCharset']);
				if (empty($user_preferences[$u]['language'])) {
					$user_preferences[$u]['language'] = $prefs['site_language'];
				}
				if (empty($user_preferences[$u]['mailCharset'])) {
					$user_preferences[$u]['mailCharset'] = $prefs['users_prefs_mailCharset'];
				}
				$watchers_outbound[] = ['email' => $w, 'user' => $u, 'language' => $user_preferences[$u]['language'], 'mailCharset' => $user_preferences[$u]['mailCharset']];
			}
		}

		$emails = [];
		$watchers = [];
		foreach (['watchers_global', 'watchers_local', 'watchers_item', 'watchers_outbound'] as $ws) {
			if (! empty($$ws)) {
				foreach ($$ws as $w) {
					$wl = strtolower($w['email']);
					if (! in_array($wl, $emails)) {
						$emails[] = $wl;
						$watchers[] = $w;
					}
				}
			}
		}
		return $watchers;
	}

	/* sort allFileds function of a list of fields */
	public function sort_fields($allFields, $listFields)
	{
		$tmp = [];
		foreach ($listFields as $fieldId) {
			if (substr($fieldId, 0, 1) == '-') {
				$fieldId = substr($fieldId, 1);
			}
			foreach ($allFields['data'] as $i => $field) {
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

	/* return all the values+field options of an item for a type field (ex: return all the user selector value for an item) */
	public function get_item_values_by_type($itemId, $typeField)
	{
		$query = "select ttif.`value`, ttf.`options` from `tiki_tracker_fields` ttf, `tiki_tracker_item_fields` ttif";
		$query .= " where ttif.`itemId`=? and ttf.`type`=? and ttf.`fieldId`=ttif.`fieldId`";
		$ret = $this->fetchAll($query, [$itemId, $typeField]);
		$factory = new Tracker_Field_Factory;
		$typeInfo = $factory->getFieldInfo($typeField);
		foreach ($ret as &$res) {
			$options = Tracker_Options::fromSerialized($res['options'], $typeInfo);
			$res['options_map'] = $options->getAllParameters();
		}
		return $ret;
	}

	/* return all the emails that are locally watching an item */
	public function get_local_notifications($itemId, $status = '', $oldStatus = '')
	{
		global $user_preferences, $prefs, $user;
		$tikilib = TikiLib::lib('tiki');
		$userlib = TikiLib::lib('user');
		$emails = [];
		// user field watching item
		$res = $this->get_item_values_by_type($itemId, 'u');
		if (is_array($res)) {
			foreach ($res as $f) {
				if (isset($f['options_map']['notify']) && $f['options_map']['notify'] != 0 && ! empty($f['value'])) {
					$fieldUsers = $this->parse_user_field($f['value']);
					foreach ($fieldUsers as $fieldUser) {
						if ($f['options_map']['notify'] == 2 && $user == $fieldUser) {
							// Don't send email to oneself
							continue;
						}
						$email = $userlib->get_user_email($fieldUser);
						if (! empty($fieldUser) && ! empty($email)) {
							$tikilib->get_user_preferences($fieldUser, ['email', 'user', 'language', 'mailCharset']);
							$emails[] = ['email' => $email, 'user' => $fieldUser, 'language' => $user_preferences[$fieldUser]['language'],
								'mailCharset' => $user_preferences[$fieldUser]['mailCharset'], 'template' => $f['options_map']['notify_template'], 'templateFormat' => $f['options_map']['notify_template_format']];
						}
					}
				}
			}
		}
		// email field watching status change
		if ($status != $oldStatus) {
			$res = $this->get_item_values_by_type($itemId, 'm');
			if (is_array($res)) {
				foreach ($res as $f) {
					if ((isset($f['options_map']['watchopen']) && $f['options_map']['watchopen'] == 'o' && $status == 'o')
						|| (isset($f['options_map']['watchpending']) && $f['options_map']['watchpending'] == 'p' && $status == 'p')
						|| (isset($f['options_map']['watchclosed']) && $f['options_map']['watchclosed'] == 'c' && $status == 'c')) {
						$emails[] = ['email' => $f['value'], 'user' => '', 'language' => $prefs['language'], 'mailCharset' => $prefs['users_prefs_mailCharset'], 'action' => 'status'];
					}
				}
			}
		}
		return $emails;
	}

	public function get_join_values($trackerId, $itemId, $fieldIds, $finalTrackerId = '', $finalFields = '', $separator = ' ', $status = '')
	{
		$smarty = TikiLib::lib('smarty');
		$select[] = "`tiki_tracker_item_fields` t0";
		$where[] = " t0.`itemId`=?";
		$bindVars[] = $itemId;
		$mid = '';
		for ($i = 0, $tmp_count = count($fieldIds) - 1; $i < $tmp_count; $i += 2) {
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
		if (! empty($status)) {
			$this->getSqlStatus($status, $mid, $bindVars, $trackerId);
			$select[] = '`tiki_tracker_items` tti';
			$mid .= " and tti.`itemId`=t$k.`itemId`";
		}
		$query = "select t$k.* from " . implode(',', $select) . ' where ' . implode(' and ', $where) . $mid;
		$result = $this->query($query, $bindVars);
		$ret = [];
		while ($res = $result->fetchRow()) {
			$field_value['value'] = $res['value'];
			$field_value['trackerId'] = $trackerId;
			$field_value['type'] = $this->fields()->fetchOne('type', ['fieldId' => (int) $res['fieldId']]);
			if (! $field_value['type']) {
				$ret[$res['itemId']] = tra('Tracker field setup error - display field not found: ') . '#' . $res['fieldId'];
			} else {
				$ret[$res['itemId']] = $this->get_field_handler($field_value, $res)->renderOutput(['showlinks' => 'n', 'list_mode' => 'n']);
			}
			if (is_array($finalFields) && count($finalFields)) {
				$i = 0;
				foreach ($finalFields as $f) {
					if (! $i++) {
						continue;
					}
					$field_value = $this->get_tracker_field($f);
					$ff = $this->get_item_value($finalTrackerId, $res['itemId'], $f);
					;
					$field_value['value'] = $ff;
					$ret[$res['itemId']] = $this->get_field_handler($field_value, $res)->renderOutput(['showlinks' => 'n']);
				}
			}
		}
		return $ret;
	}

	public function get_left_join_sql($fieldIds)
	{
		$sql = '';
		for ($i = 0, $tmp_count = count($fieldIds); $i < $tmp_count; $i += 3) {
			$j = $i + 1;
			$k = $j + 1;
			$tti = $i ? "t$i" : 'tti';
			$sttif = $k < $tmp_count - 1 ? "t$k" : 'sttif';
			$sql .= " LEFT JOIN (`tiki_tracker_item_fields` t$i) ON ($tti.`itemId`= t$i.`itemId` and t$i.`fieldId`=" . $fieldIds[$i] . ")";
			$sql .= " LEFT JOIN (`tiki_tracker_item_fields` t$j) ON (t$i.`value`= t$j.`value` and t$j.`fieldId`=" . $fieldIds[$j] . ")";
			$sql .= " LEFT JOIN (`tiki_tracker_item_fields` $sttif) ON (t$j.`itemId`= $sttif.`itemId` and $sttif.`fieldId`=" . $fieldIds[$k] . ")";
		}
		return $sql;
	}

	public function get_item_info($itemId)
	{
		return $this->items()->fetchFullRow(['itemId' => (int) $itemId]);
	}

	public function rename_page($old, $new)
	{
		global $prefs;

		$query = "update `tiki_tracker_item_fields` ttif left join `tiki_tracker_fields` ttf on (ttif.fieldId = ttf.fieldId) set ttif.`value`=? where ttif.`value`=? and (ttf.`type` = ? or ttf.`type` = ?)";
		$this->query($query, [$new, $old, 'k', 'wiki']);

		$relationlib = TikiLib::lib('relation');
		$wikilib = TikiLib::lib('wiki');
		$relatedfields = $relationlib->get_object_ids_with_relations_from('wiki page', $new, 'tiki.wiki.linkedfield'); // $new because attributes have been changed
		$relateditems = $relationlib->get_object_ids_with_relations_from('wiki page', $new, 'tiki.wiki.linkeditem');
		foreach ($relateditems as $itemId) {
			foreach ($relatedfields as $fieldId) {
				$field = $this->get_tracker_field($fieldId);
				$toSync = false;
				$nameFieldId = 0;
				if ($field['type'] == 'wiki') {
					$trackerId = $field['trackerId'];
					$definition = Tracker_Definition::get($trackerId);
					$field = $definition->getField($fieldId);
					if ($field['options_map']['syncwikipagename'] != 'n') {
						$toSync = true;
					}
										$nameFieldId = $field['options_map']['fieldIdForPagename'];
				} elseif ($prefs['tracker_wikirelation_synctitle'] == 'y') {
					$toSync = true;
				}
				if ($toSync) {
					$value = $this->get_item_value(0, $itemId, $fieldId);
					if ($wikilib->get_namespace($value) && $value != $new) {
						$this->modify_field($itemId, $fieldId, $new);
					} elseif (! $wikilib->get_namespace($value) && $value != $wikilib->get_without_namespace($new)) {
						$this->modify_field($itemId, $fieldId, $wikilib->get_without_namespace($new));
					}
					if ($nameFieldId) {
						$this->modify_field($itemId, $nameFieldId, $wikilib->get_without_namespace($new));
					}
				}
			}
		}
	}

	/**
	 * Note that this is different from function rename_page
	 */
	public function rename_linked_page($args)
	{
		global $prefs;
		$relationlib = TikiLib::lib('relation');
		$wikilib = TikiLib::lib('wiki');
		$wikipages = $relationlib->get_object_ids_with_relations_to('trackeritem', $args['object'], 'tiki.wiki.linkeditem');
		foreach ($wikipages as $pageName) {
			// determine if field has changed
			$relatedfields = $relationlib->get_object_ids_with_relations_from('wiki page', $pageName, 'tiki.wiki.linkedfield');
			foreach ($relatedfields as $fieldId) {
				if (isset($args['values'][$fieldId]) and isset($args['old_values'][$fieldId])
					&& $args['values'][$fieldId] != $args['old_values'][$fieldId] ) {
					if ($wikilib->get_namespace($args['values'][$fieldId])) {
						$newname = $args['values'][$fieldId];
					} elseif ($namespace = $wikilib->get_namespace($pageName)) {
						$newname = $namespace . $prefs['namespace_separator'] . $wikilib->get_without_namespace($args['values'][$fieldId]);
					} else {
						$newname = $args['values'][$fieldId];
					}
					$wikilib->wiki_rename_page($pageName, $newname, false);
				}
			}
		}
	}

	public function setup_wiki_fields($args)
	{
		$definition = Tracker_Definition::get($args['trackerId']);
		$itemId = $args['object'];
		$values = $args['values'];

		if ($definition && $fieldIds = $definition->getWikiFields()) {
			foreach ($fieldIds as $fieldId) {
				if (! empty($values[$fieldId])) {
					TikiLib::lib('relation')->add_relation('tiki.wiki.linkeditem', 'wiki page', $values[$fieldId], 'trackeritem', $itemId);
					TikiLib::lib('relation')->add_relation('tiki.wiki.linkedfield', 'wiki page', $values[$fieldId], 'trackerfield', $fieldId);
				}
			}
		}
	}

	public function update_wiki_fields($args)
	{
		global $prefs;
		$wikilib = TikiLib::lib('wiki');
		$definition = Tracker_Definition::get($args['trackerId']);
		$values = $args['values'];
		$old_values = $args['old_values'];
		$itemId = $args['object'];

		if ($definition && $fieldIds = $definition->getWikiFields()) {
			foreach ($fieldIds as $fieldId) {
				$field = $definition->getField($fieldId);
				if ($field['options_map']['syncwikipagename'] != 'n') {
					$nameFieldId = $field['options_map']['fieldIdForPagename'];
					if (! empty($values[$nameFieldId]) && ! empty($old_values[$nameFieldId]) && ! empty($old_values[$fieldId])
						&& $values[$nameFieldId] != $old_values[$nameFieldId] ) {
						if ($namespace = $wikilib->get_namespace($old_values[$fieldId])) {
							$newname = $namespace . $prefs['namespace_separator'] . $wikilib->get_without_namespace($values[$nameFieldId]);
						} else {
							$newname = $values[$nameFieldId];
						}
							$args['values'][$fieldId] = $newname;
							$this->modify_field($itemId, $fieldId, $newname);
							$wikilib->wiki_rename_page($old_values[$fieldId], $newname, false);
					}
				}
			}
		}
	}

	public function delete_wiki_fields($args)
	{
		$definition = Tracker_Definition::get($args['trackerId']);
		$itemId = $args['object'];

		if ($definition && $fieldIds = $definition->getWikiFields()) {
			foreach ($fieldIds as $fieldId) {
				$field = $definition->getField($fieldId);

				if ($field['options_map']['syncwikipagedelete'] == 'y' && ! empty($args['values'][$fieldId])) {
					$pagename = $args['values'][$fieldId];
					TikiLib::lib('tiki')->remove_all_versions($pagename);
				}
			}
		}
	}

	public function build_date($input, $format, $ins_id)
	{
		if (is_array($format)) {
			$format = $format['options_array'][0];
		}

		$tikilib = TikiLib::lib('tiki');
		$value = '';
		$monthIsNull = empty($input[$ins_id . 'Month']) || $input[$ins_id . 'Month'] == null || $input[$ins_id . 'Month'] == 'null'|| $input[$ins_id . 'Month'] == '';
		$dayIsNull = empty($input[$ins_id . 'Day']) || $input[$ins_id . 'Day'] == null || $input[$ins_id . 'Day'] == 'null' || $input[$ins_id . 'Day'] == '';
		$yearIsNull = empty($input[$ins_id . 'Year']) || $input[$ins_id . 'Year'] == null || $input[$ins_id . 'Year'] == 'null' || $input[$ins_id . 'Year'] == '';
		$hourIsNull = ! isset($input[$ins_id . 'Hour']) || $input[$ins_id . 'Hour'] == null || $input[$ins_id . 'Hour'] == 'null' || $input[$ins_id . 'Hour'] == ''|| $input[$ins_id . 'Hour'] == ' ';
		$minuteIsNull = empty($input[$ins_id . 'Minute']) || $input[$ins_id . 'Minute'] == null || $input[$ins_id . 'Minute'] == 'null' || $input[$ins_id . 'Minute'] == '' || $input[$ins_id . 'Minute'] == ' ';
		if ($format == 'd') {
			if ($monthIsNull || $dayIsNull || $yearIsNull) {
				// all the values must be blank
				$value = '';
			} else {
				$value = $tikilib->make_time(0, 0, 0, $input[$ins_id . 'Month'], $input[$ins_id . 'Day'], $input[$ins_id . 'Year']);
			}
		} elseif ($format == 't') { // all the values must be blank
			if ($hourIsNull || $minuteIsNull) {
				$value = '';
			} else {
				//if (isset($input[$ins_id.'Meridian']) && $input[$ins_id.'Meridian'] == 'pm') $input[$ins_id.'Hour'] += 12;
				$now = $tikilib->now;
				//Convert 12-hour clock hours to 24-hour scale to compute time
				if (isset($input[$ins_id . 'Meridian'])) {
					$input[$ins_id . 'Hour'] = date('H', strtotime($input[$ins_id . 'Hour'] . ':00 ' . $input[$ins_id . 'Meridian']));
				}
				$value = $tikilib->make_time($input[$ins_id . 'Hour'], $input[$ins_id . 'Minute'], 0, $tikilib->date_format("%m", $now), $tikilib->date_format("%d", $now), $tikilib->date_format("%Y", $now));
			}
		} else {
			if ($monthIsNull || $dayIsNull || $yearIsNull || $hourIsNull || $minuteIsNull) {
				// all the values must be blank
				$value = '';
			} else {
				//if (isset($input[$ins_id.'Meridian']) && $input[$ins_id.'Meridian'] == 'pm') $input[$ins_id.'Hour'] += 12;
				//Convert 12-hour clock hours to 24-hour scale to compute time
				if (isset($input[$ins_id . 'Meridian'])) {
					$input[$ins_id . 'Hour'] = date('H', strtotime($input[$ins_id . 'Hour'] . ':00 ' . $input[$ins_id . 'Meridian']));
				}
				$value = $tikilib->make_time($input[$ins_id . 'Hour'], $input[$ins_id . 'Minute'], 0, $input[$ins_id . 'Month'], $input[$ins_id . 'Day'], $input[$ins_id . 'Year']);
			}
		}
		return $value;
	}

	/* get the fields from the pretty tracker template
		 * return a list of fieldIds
		 */
	public function get_pretty_fieldIds($resource, $type = 'wiki', &$prettyModifier, $trackerId = 0)
	{
		$tikilib = TikiLib::lib('tiki');
		$smarty = TikiLib::lib('smarty');
		if ($type == 'wiki') {
			$wiki_info = $tikilib->get_page_info($resource);
			if (! empty($wiki_info)) {
				$f = $wiki_info['data'];
			}
		} else {
			if (strpos($resource, 'templates/') === 0) {
				$resource = substr($resource, 10);
			}
			$resource_name = $smarty->get_filename($resource);
			$f = file_get_contents($resource_name);
		}
		if (! empty($f)) {
			//matches[1] = field name
			//matches[2] = trailing modifier text
			//matches[3] = modifier name ('output' or 'template')
			//matches[4] = modifier parameter (template name in this case)
			preg_match_all('/\$f_(\w+)(\|(output|template):?(.*))?}/', $f, $matches);
			$ret = [];
			foreach ($matches[1] as $i => $val) {
				if (ctype_digit($val)) {
					$ret[] = $val;
				} elseif ($fieldId = $this->table('tiki_tracker_fields')->fetchOne('fieldId', ['permName' => $val, 'trackerId' => $trackerId])) {
					$ret[] = $fieldId;
				}
			}

			/*
			 * Check through modifiers in the pretty tracker template.
			 * If |output, store modifier as output. In wikiplugin_tracker, this will make it such that the field is output only
			 * If |template, it will check to see if a template is specified (e.g. $f_title|template:"title.tpl"). If not, default to tracker_input_field tpl
			 */
			foreach ($matches[3] as $i => $val) {
				if ($val == 'output') {
					$v = $matches[1][$i];
					if (ctype_digit($v)) {
						$prettyModifier[$v] = "output";
					} elseif ($fieldId = $this->table('tiki_tracker_fields')->fetchOne('fieldId', ['permName' => $v, 'trackerId' => $trackerId])) {
						$prettyModifier[$fieldId] = "output";
					}
				} elseif ($val == "template") {
					$v = $matches[1][$i];
					$tpl = ! empty($matches[4][$i]) ? $matches[4][$i] : "tracker_input_field.tpl"; //fetches template from pretty tracker template. if none, set to default
					$tpl = trim($tpl, '"\''); //trim quotations from template name
					if (ctype_digit($v)) {
						$prettyModifier[$v] = $tpl;
					} elseif ($fieldId = $this->table('tiki_tracker_fields')->fetchOne('fieldId', ['permName' => $v, 'trackerId' => $trackerId])) {
						$prettyModifier[$fieldId] = $tpl;
					}
				}
			}
			return $ret;
		}
		return [];
	}

	/**
	 * @param mixed $value		string or array to process
	 */
	public function replace_pretty_tracker_refs(&$value)
	{
		$smarty = TikiLib::lib('smarty');

		if (is_array($value)) {
			foreach ($value as &$v) {
				$this->replace_pretty_tracker_refs($v);
			}
		} else {
			// array syntax for callback function needed for some versions of PHP (5.2.0?) - thanks to mariush on http://php.net/preg_replace_callback
			$value = preg_replace_callback('/\{\$(f_\w+)\}/', [ &$this, '_pretty_tracker_replace_value'], $value);
		}
	}

	public static function _pretty_tracker_replace_value($matches)
	{
		$smarty = TikiLib::lib('smarty');
		$s_var = null;
		if (! empty($matches[1])) {
			$s_var = $smarty->getTemplateVars($matches[1]);
		}
		if (! is_null($s_var)) {
			$r = $s_var;
		} else {
			$r = $matches[0];
		}
		return $r;
	}

	public function nbComments($user)
	{
		return $this->comments()->fetchCount(['userName' => $user, 'objectType' => 'trackeritem']);
	}

	public function lastModif($trackerId)
	{
		return $this->items()->fetchOne($this->items()->max('lastModif'), ['trackerId' => (int) $trackerId]);
	}

	public function get_field($fieldId, $fields)
	{
		foreach ($fields as $f) {
			if ($f['fieldId'] == $fieldId) {
				return $f;
			}
		}
		return false;
	}

	public function flaten($fields)
	{
		$new = [];
		if (empty($fields)) {
			return $new;
		}
		foreach ($fields as $field) {
			if (is_array($field)) {
				$new = array_merge($new, $this->flaten($field));
			} else {
				$new[] = $field;
			}
		}
		return $new;
	}

	public function test_field_type($fields, $types)
	{
		$new = $this->flaten($fields);
		$table = $this->fields();

		return $table->fetchCount(['fieldId' => $table->in($new),'type' => $table->in($types, true)]);
	}

	public function get_computed_info($options, $trackerId = 0, &$fields = null)
	{
		preg_match_all('/#([0-9]+)/', $options, $matches);
		$nbDates = 0;
		foreach ($matches[1] as $k => $match) {
			if (empty($fields)) {
				$allfields = $this->list_tracker_fields($trackerId, 0, -1, 'position_asc', '');
				$fields = $allfields['data'];
			}
			foreach ($fields as $k => $field) {
				if ($field['fieldId'] == $match && in_array($field['type'], ['f', 'j'])) {
					++$nbDates;
					$info = $field;
					break;
				} elseif ($field['fieldId'] == $match && $field['type'] == 'C') {
					$info = $this-> get_computed_info($field['options'], $trackerId, $fields);
					if (! empty($info) && ($info['computedtype'] == 'f' || $info['computedtype'] == 'j')) {
						++$nbDates;
						break;
					}
				}
			}
		}
		if ($nbDates == 0) {
			return null;
		} elseif ($nbDates % 2 == 0) {
			return ['computedtype' => 'duration', 'options' => $info['options'] ,'options_array' => $info['options_array']];
		} else {
			return ['computedtype' => 'f', 'options' => $info['options'] ,'options_array' => $info['options_array']];
		}
	}

	public function change_status($items, $status)
	{
		global $prefs, $user;
		$tikilib = TikiLib::lib('tiki');

		if (! count($items)) {
			return;
		}

		$toUpdate = [];

		foreach ($items as $i) {
			if (is_array($i) && isset($i['itemId'])) {
				$i = $i['itemId'];
			}

			$toUpdate[] = $i;
		}

		$table = $this->items();
		$map = $table->fetchMap(
			'itemId',
			'trackerId',
			[
				'itemId' => $table->in($toUpdate),
			]
		);

		foreach ($toUpdate as $itemId) {
			$trackerId = $map[$itemId];
			$child = $this->findLinkedItems(
				$itemId,
				function ($field, $handler) use ($trackerId) {
					return $handler->cascadeStatus($trackerId);
				}
			);

			$toUpdate = array_merge($toUpdate, $child);
		}

		$this->update_items(
			$toUpdate,
			[
				'status' => $status,
				'lastModif' => $tikilib->now,
				'lastModifBy' => $user,
			],
			true
		);
	}

	private function update_items(array $toUpdate, array $fields, $refresh_index)
	{
		global $prefs;
		$logslib = TikiLib::lib('logs');
		$table = $this->items();
		$table->updateMultiple(
			$fields,
			['itemId' => $table->in($toUpdate)]
		);

		foreach ($toUpdate as $itemId) {
			$version = $this->last_log_version($itemId) + 1;
			if (($logslib->add_action('Updated', $itemId, 'trackeritem', $version)) == 0) {
				$version = 0;
			}
		}

		if ($prefs['feature_search'] === 'y' && $prefs['unified_incremental_update'] === 'y') {
			$searchlib = TikiLib::lib('unifiedsearch');

			foreach ($toUpdate as $child) {
				$searchlib->invalidateObject('trackeritem', $child);
			}

			if ($refresh_index && $toUpdate) {
				require_once('lib/search/refresh-functions.php');
				refresh_index('trackeritem', $toUpdate[0]);
			}
		}
	}

	public function log($version, $itemId, $fieldId, $value = '')
	{
		if (empty($version)) {
			 return;
		}
		if ($value === null) {
			$value = ''; // we want to log it after all, so change is in history
		}
		$values = (array) $value;
		foreach ($values as $v) {
			$this->logs()->insert(['version' => $version, 'itemId' => $itemId, 'fieldId' => $fieldId, 'value' => $v]);
		}
	}

	public function last_log_version($itemId)
	{
		$logs = $this->logs();

		return $logs->fetchOne($logs->max('version'), ['itemId' => $itemId]);
	}

	public function remove_item_log($itemId)
	{
		$this->logs()->deleteMultiple(['itemId' => $itemId]);
	}

	public function get_item_history($item_info = null, $fieldId = 0, $filter = '', $offset = 0, $max = -1)
	{
		global $prefs;
		if (! empty($fieldId)) {
			$mid2[] = $mid[] = 'ttifl.`fieldId`=?';
			$bindvars[] = $fieldId;
		}
		if (! empty($item_info['itemId'])) {
			$mid[] = 'ttifl.`itemId`=?';
			$bindvars[] = $item_info['itemId'];
			if ($prefs['feature_categories'] == 'y') {
				$categlib = TikiLib::lib('categ');
				$item_categs = $categlib->get_object_categories('trackeritem', $item_info['itemId']);
			}
		}
		$query = 'select ttifl.*, ttf.* from `tiki_tracker_item_fields` ttifl left join `tiki_tracker_fields` ttf on (ttf.`fieldId`=ttifl.`fieldId`) where ' . implode(' and ', $mid);
		$all = $this->fetchAll($query, $bindvars, -1, 0);
		foreach ($all as $f) {
			if (! empty($item_categs) && $f['type'] == 'e') {
				//category
				$f['options_array'] = explode(',', $f['options']);
				if (ctype_digit($f['options_array'][0]) && $f['options_array'][0] > 0) {
					$type = (isset($f['options_array'][3]) && $f['options_array'][3] == 1) ? 'descendants' : 'children';
					$cfilter = ['identifier' => $f['options_array'][0], 'type' => $type];
					$field_categs = $categlib->getCategories($cfilter, true, false);
				} else {
					$field_categs = [];
				}
				$aux = [];
				foreach ($field_categs as $cat) {
					$aux[] = $cat['categId'];
				}
				$field_categs = $aux;
				$check = array_intersect($field_categs, $item_categs);
				if (! empty($check)) {
					$f['value'] = implode(',', $check);
				}
			}
			$last[$f['fieldId']] = $f['value'];
		}

		$last[-1] = $item_info['status'];
		if (! empty($filter)) {
			foreach ($filter as $key => $f) {
				switch ($key) {
					case 'version':
						$mid[] = 'ttifl.`version`=?';
						$bindvars[] = $f;
				}
			}
		}
		if (empty($item_info['itemId'])) {
			$join = 'ttifl.`itemId`';
			$bindvars = array_merge(['trackeritem'], $bindvars);
		} else {
			$join = '?';
			$bindvars = array_merge(['trackeritem', $item_info['itemId']], $bindvars);
		}
		$query = 'select ttifl.`version`, ttifl.`fieldId`, ttifl.`value`, ta.`user`, ta.`lastModif` from `tiki_tracker_item_field_logs` ttifl left join `tiki_actionlog` ta on (ta.`comment`=ttifl.`version` and ta.`objectType`=? and ta.`object`=' . $join . ') where ' . implode(' and ', $mid) . ' order by ttifl.`itemId` asc, ttifl.`version` desc, ttifl.`fieldId` asc';
		$all = $this->fetchAll($query, $bindvars, -1, 0);
		$history['data'] = [];
		$i = 0;
		foreach ($all as $hist) {
			$hist['new'] = isset($last[$hist['fieldId']]) ? $last[$hist['fieldId']] : '';
			if ($hist['new'] == $hist['value']) {
				continue;
			}
			if ($i >= $offset && ($max == -1 || $i < $offset + $max)) {
				$history['data'][] = $hist;
			}
			$last[$hist['fieldId']] = $hist['value'];
			++$i;
		}
		$history['cant'] = count($history['data']);
		return $history;
	}

	public function item_has_history($itemId)
	{
		return $this->table('tiki_tracker_item_fields')->fetchCount([ 'itemId' => $itemId ]);
	}

	public function move_item($trackerId, $itemId, $newTrackerId)
	{
		$newFields = $this->list_tracker_fields($newTrackerId, 0, -1, 'name_asc');
		foreach ($newFields['data'] as $field) {
			$translation[$field['name']] = $field;
		}
		$this->items()->update(['trackerId' => $newTrackerId], ['itemId' => $itemId]);
		$this->trackers()->update(['items' => $this->trackers()->decrement(1)], ['trackerId' => $trackerId]);
		$this->trackers()->update(['items' => $this->trackers()->increment(1)], ['trackerId' => $newTrackerId]);

		$newFields = $this->list_tracker_fields($newTrackerId, 0, -1, 'name_asc');
		$query = 'select ttif.*, ttf.`name`, ttf.`type`, ttf.`options` from `tiki_tracker_item_fields` ttif, `tiki_tracker_fields` ttf where ttif.itemId=? and ttif.`fieldId`=ttf.`fieldId`';
		$fields = $this->fetchAll($query, [$itemId]);

		foreach ($fields as $field) {
			if (empty($translation[$field['name']]) || $field['type'] != $translation[$field['name']]['type'] || $field['options'] != $translation[$field['name']]['options']) {
				// delete the field
				$this->itemFields()->delete(['itemId' => $field['itemId'], 'fieldId' => $field['fieldId']]);
			} else {
				// transfer
				$this->itemFields()->update(
					[
						'fieldId' => $translation[$field['name']]['fieldId'],
					],
					[
						'itemId' => $field['itemId'],
						'fieldId' => $field['fieldId'],
					]
				);
			}
		}
	}

	/* copy the fields of one item ($from) to another one ($to) of the same tracker - except/only for some fields */
	/* note: can not use the generic function as they return not all the multilingual fields */
	public function copy_item($from, $to, $except = null, $only = null, $status = null)
	{
		global $user, $prefs;

		if ($prefs['feature_categories'] == 'y') {
			$categlib = TikiLib::lib('categ');
			$cats = $categlib->get_object_categories('trackeritem', $from);
		}
		if (empty($to)) {
			$is_new = 'y';
			$info_to['trackerId'] = $this->items()->fetchOne('trackerId', ['itemId' => $from]);
			$info_to['status'] = empty($status) ? $this->items()->fetchOne('status', ['itemId' => $from]) : $status;
			$info_to['created'] = $info_to['lastModif'] = $this->now;
			$info_to['createdBy'] = $info_to['lastModifBy'] = $user;
			$to = $this->items()->insert($info_to);
		}

		$query = 'select ttif.*, ttf.`type`, ttf.`options` from `tiki_tracker_item_fields` ttif left join `tiki_tracker_fields` ttf on (ttif.`fieldId` = ttf.`fieldId`) where `itemId`=?';
		$result = $this->fetchAll($query, [$from]);
		$clean = [];
		$factory = new Tracker_Field_Factory;
		foreach ($result as $res) {
			$typeInfo = $factory->getFieldInfo($res['type']);
			$options = Tracker_Options::fromSerialized($res['options'], $typeInfo);
			$res['options_array'] = $options->buildOptionsArray();

			if ($prefs['feature_categories'] == 'y' && $res['type'] == 'e') {
				//category
				if ((! empty($except) && in_array($res['fieldId'], $except))
					|| (! empty($only) && ! in_array($res['fieldId'], $only))) {
					// take away the categories from $cats
					if (ctype_digit($res['options_array'][0]) && $res['options_array'][0] > 0) {
						$filter = ['identifier' => $res['options_array'][0], 'type' => 'children'];
					} else {
						$filter = null;
					}
					$children = $categlib->getCategories($filter, true, false);
					$local = [];
					foreach ($children as $child) {
						$local[] = $child['categId'];
					}
					$cats = array_diff($cats, $local);
				}
			}

			if ((! empty($except) && in_array($res['fieldId'], $except))
				|| (! empty($only) && ! in_array($res['fieldId'], $only))
				|| ($res['type'] == 'q')
				) {
				continue;
			}
			if (! empty($is_new) && in_array($res['type'], ['u', 'g', 'I']) && ($res['options_array'][0] == 1 || $res['options_array'][0] == 2)) {
				$res['value'] = ($res['type'] == 'u') ? $user : (($res['type'] == 'g') ? $_SESSION['u_info']['group'] : TikiLib::get_ip_address());
			}
			if (in_array($res['type'], ['A', 'N'])) {
				// attachment - image
				continue; //not done yet
			}
			//echo "duplic".$res['fieldId'].' '. $res['value'].'<br>';
			if (! in_array($res['fieldId'], $clean)) {
				$this->itemFields()->delete(['itemId' => $to, 'fieldId' => $res['fieldId']]);
				$clean[] = $res['fieldId'];
			}

			$data = [
				'itemId' => $to,
				'fieldId' => $res['fieldId'],
				'value' => $res['value'],
			];

			$this->itemFields()->insert($data);
		}

		if (! empty($cats)) {
			$trackerId = $this->items()->fetchOne('trackerId', ['itemId' => $from]);
			$this->categorized_item($trackerId, $to, "item $to", $cats);
		}
		return $to;
	}

	public function export_attachment($itemId, $archive)
	{
		global $prefs;
		$files = $this->list_item_attachments($itemId, 0, -1, 'attId_asc');
		foreach ($files['data'] as $file) {
			$localZip = "item_$itemId/" . $file['filename'];
			$complete = $this->get_item_attachment($file['attId']);
			if (! empty($complete['path']) && file_exists($prefs['t_use_dir'] . $complete['path'])) {
				if (! $archive->addFile($prefs['t_use_dir'] . $complete['path'], $localZip)) {
					return false;
				}
			} elseif (! empty($complete['data'])) {
				if (! $archive->addFromString($localZip, $complete['data'])) {
					return false;
				}
			}
		}
		return true;
	}

	/* fill a calendar structure with items
	 * fieldIds contains one date or 2 dates
	 */
	public function fillTableViewCell($items, $fieldIds, &$cell)
	{
		$smarty = TikiLib::lib('smarty');
		if (empty($items)) {
			return;
		}
		$iStart = -1;
		$iEnd = -1;
		foreach ($items[0]['field_values'] as $i => $field) {
			if ($field['fieldId'] == $fieldIds[0]) {
				$iStart = $i;
				$iEnd = $i; //$end can be the same as start
			} elseif (count($fieldIds) > 1 && $field['fieldId'] == $fieldIds[1]) {
				$iEnd = $i;
			}
		}
		foreach ($cell as $i => $line) {
			foreach ($line as $j => $day) {
				if (! $day['focus']) {
					continue;
				}
				$overs = [];
				foreach ($items as $item) {
					$endDay = TikiLib::make_time(23, 59, 59, $day['month'], $day['day'], $day['year']);
					if ((count($fieldIds) == 1 && $item['field_values'][$iStart]['value'] >= $day['date'] && $item['field_values'][$iStart]['value'] <= $endDay)
						|| (count($fieldIds) > 1 && $item['field_values'][$iStart]['value'] <= $endDay && $item['field_values'][$iEnd]['value'] >= $day['date'])) {
							$cell[$i][$j]['items'][] = $item;
							$overs[] = preg_replace('|(<br /> *)*$|m', '', $item['over']);
					}
				}
				if (! empty($overs)) {
					$smarty->assign_by_ref('overs', $overs);
					$cell[$i][$j]['over'] = $smarty->fetch('tracker_calendar_over.tpl');
				}
			}
		}
		//echo '<pre>'; print_r($cell); echo '</pre>';
	}

	public function get_tracker_by_name($name)
	{
		return $this->trackers()->fetchOne('trackerId', ['name' => $name]);
	}

	public function get_field_by_name($trackerId, $fieldName)
	{
		return $this->fields()->fetchOne('fieldId', ['trackerId' => $trackerId, 'name' => $fieldName]);
	}

	public function get_field_by_names($trackerName, $fieldName)
	{
		$trackerId = $this->trackers()->fetchOne('trackerId', ['name' => $trackerName]);
		return $fieldId = $this->fields()->fetchOne('fieldId', ['trackerId' => $trackerId, 'name' => $fieldName]);
	}

	public function get_fields_by_names($trackerName, $fieldNames)
	{
		$fields = [];
		foreach ($fieldNames as $fieldName) {
			$fields[$fieldName] = $this->get_field_by_names($trackerName, $fieldName);
		}
		return $fields;
	}

	/**
	 * Get a field handler for a specific fieldtype. The handler comes initialized with the field / item data passed.
	 * @param array $field.
	 * <pre>
	 * $field = array(
	 * 		// required
	 * 		'trackerId' => 1 // trackerId
	 * );
	 * </pre
	 * @param array $item - array('itemId1' => value1, 'itemid2' => value2)
	 * @return Tracker_Field_Abstract $tracker_field_handler - i.e. Tracker_Field_Text
	 */
	public function get_field_handler($field, $item = [])
	{
		if (! isset($field['trackerId'])) {
			return false;
		}

		$trackerId = (int) $field['trackerId'];
		$definition = Tracker_Definition::get($trackerId);

		if (! $definition) {
			return false;
		}

		return $definition->getFieldFactory()->getHandler($field, $item);
	}

	public function get_field_value($field, $item)
	{
		$handler = $this->get_field_handler($field, $item);
		$values = $handler->getFieldData();

		return isset($values['value']) ? $values['value'] : null;
	}

	private function parse_comment($data)
	{
		return nl2br(htmlspecialchars($data));
	}

	public function send_replace_item_notifications($args)
	{
		global $prefs, $user;

		// Don't send a notification if this operation is part of a bulk import
		if ($args['bulk_import']) {
			return;
		}

		$trackerId = $args['trackerId'];
		$itemId = $args['object'];

		$new_values = $args['values'];
		$old_values = $args['old_values'];

		$the_data = $this->generate_watch_data($old_values, $new_values, $trackerId, $itemId, $args['version']);

		if (empty($the_data) && $prefs['tracker_always_notify'] !== 'y') {
			return;
		}

		$tracker_definition = Tracker_Definition::get($trackerId);
		if (! $tracker_definition) {
			return;
		}
		$tracker_info = $tracker_definition->getInformation();

		$watchers = $this->get_notification_emails($trackerId, $itemId, $tracker_info, $new_values['status'], $old_values['status']);

		if (count($watchers) > 0) {
			$simpleEmail = isset($tracker_info['simpleEmail']) ? $tracker_info['simpleEmail'] : "n";

			$trackerName = $tracker_info['name'];
			if (! isset($_SERVER["SERVER_NAME"])) {
				$_SERVER["SERVER_NAME"] = $_SERVER["HTTP_HOST"];
			}
			include_once('lib/webmail/tikimaillib.php');
			if ($simpleEmail == "n") {
				$mail_main_value_fieldId = $this->get_main_field($trackerId);
				$mail_main_value_field = $tracker_definition->getField($mail_main_value_fieldId);
				if (in_array($mail_main_value_field['type'], ['r', 'q'])) {
					// Item Link & auto-inc are special cases as field value is not the displayed text. There might be other such field types.
					$handler = $this->get_field_handler($mail_main_value_field);
					$desc = $handler->renderOutput(['list_mode' => 'csv']);
				} else {
					$desc = $this->get_item_value($trackerId, $itemId, $mail_main_value_fieldId);
				}
				if ($tracker_info['doNotShowEmptyField'] === 'y') {
					// remove empty fields if tracker says so
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
				$machine = $this->httpPrefix(true) . $foo["path"];
				$smarty->assign('mail_machine', $machine);
				$parts = explode('/', $foo['path']);
				if (count($parts) > 1) {
					unset($parts[count($parts) - 1]);
				}
				$smarty->assign('mail_machine_raw', $this->httpPrefix(true) . implode('/', $parts));
				$smarty->assign_by_ref('status', $new_values['status']);
				$smarty->assign_by_ref('status_old', $old_values['status']);
				// expose the pretty tracker fields to the email tpls
				foreach ($tracker_definition->getFields() as $field) {
					$fieldId = $field['fieldId'];
					$old_value = isset($old_values[$fieldId]) ? $old_values[$fieldId] : '';
					$new_value = isset($new_values[$fieldId]) ? $new_values[$fieldId] : '';
					$smarty->assign('f_' . $fieldId, $new_value);
					$smarty->assign('f_' . $field['permName'], $new_value);
					$smarty->assign('f_old_' . $fieldId, $old_value);
					$smarty->assign('f_old_' . $field['permName'], $old_value);
					$smarty->assign('f_name_' . $fieldId, $field['name']);
					$smarty->assign('f_name_' . $field['permName'], $field['name']);
				}
				foreach ($watchers as $watcher) {
					$watcher['language'] = $this->get_user_preference($watcher['user'], 'language', $prefs['site_language']);
					$label = $itemId ? tra('Item Modification', $watcher['language']) : tra('Item creation', $watcher['language']);
					$mail_action = "\r\n$label\r\n\r\n";
					$mail_action .= tra('Tracker', $watcher['language']) . ":\n   " . tra($trackerName, $watcher['language']) . "\r\n";
					$mail_action .= tra('Item', $watcher['language']) . ":\n   $itemId $desc";

					$smarty->assign('mail_action', $mail_action);

					if (! isset($watcher['template'])) {
						$watcher['template'] = '';
					}
					$content = $this->parse_notification_template($watcher['template']);

					$subject = $smarty->fetchLang($watcher['language'], $content['subject']);
					list($watcher_data, $watcher_subject) = $this->translate_watch_data($the_data, $subject, $watcher['language']);

					$smarty->assign('mail_data', $watcher_data);
					if (isset($watcher['action'])) {
						$smarty->assign('mail_action', $watcher['action']);
					}
					$smarty->assign('mail_to_user', $watcher['user']);
					$mail_data = $smarty->fetchLang($watcher['language'], $content['template']);
					$mail = new TikiMail($watcher['user']);
					$mail->setSubject($watcher_subject);
					if (isset($watcher['templateFormat']) && $watcher['templateFormat'] == 'html') {
						$mail->setHtml($mail_data, str_replace('&nbsp;', ' ', strip_tags($mail_data)));
					} else {
						$mail->setText(str_replace('&nbsp;', ' ', strip_tags($mail_data)));
					}
					$mail->send([$watcher['email']]);
				}
			} else {
					// Use simple email
				$foo = parse_url($_SERVER["REQUEST_URI"]);
				$machine = $this->httpPrefix(true) . $foo["path"];
				$parts = explode('/', $foo['path']);
				if (count($parts) > 1) {
					unset($parts[count($parts) - 1]);
				}
				$machine = $this->httpPrefix(true) . implode('/', $parts);

				$userlib = TikiLib::lib('user');

				if (! empty($user)) {
					$my_sender = $userlib->get_user_email($user);
				} else {
					// look if a email field exists
					$fieldId = $this->get_field_id_from_type($trackerId, 'm');
					if (! empty($fieldId)) {
						$my_sender = $this->get_item_value($trackerId, $itemId, $fieldId);
					}
				}

				// Try to find a Subject in $the_data looking for strings marked "-[Subject]-" TODO: remove the tra (language translation by submitter)
				$the_string = '/^\[-\[' . tra('Subject') . '\]-\] -\[[^\]]*\]-:\n(.*)/m';
				$subject_test_unchanged = preg_match($the_string, $the_data, $unchanged_matches);
				$the_string = '/^\[-\[' . tra('Subject') . '\]-\]:\n(.*)\n(.*)\n\n(.*)\n(.*)/m';
				$subject_test_changed = preg_match($the_string, $the_data, $matches);
				$subject = '';

				if ($subject_test_unchanged == 1) {
					$subject = $unchanged_matches[1];
				}
				if ($subject_test_changed == 1) {
					$subject = $matches[1] . ' ' . $matches[2] . ' ' . $matches[3] . ' ' . $matches[4];
				}

				$i = 0;
				foreach ($watchers as $watcher) {
					$watcher['language'] = $this->get_user_preference($watcher['user'], 'language', $prefs['site_language']);
					$mail = new TikiMail($watcher['user']);
					list($watcher_data, $watcher_subject) = $this->translate_watch_data($the_data, $subject, $watcher['language']);

					$mail->setSubject('[' . $trackerName . '] ' . str_replace('> ', '', $watcher_subject) . ' (' . tra('Tracker was modified at %0 by %1', $watcher['language'], false, [$_SERVER["SERVER_NAME"], $user]) . ')');
					$mail->setText(tra('View the tracker item at:', $watcher['language']) . " $machine/tiki-view_tracker_item.php?itemId=$itemId\n\n" . $watcher_data);
					if (! empty($my_sender)) {
						$mail->setReplyTo($my_sender);
					}
					$mail->send([$watcher['email']]);
					$i++;
				}
			}
		}
	}

	private function parse_notification_template($template)
	{
		$tikilib = TikiLib::lib('tiki');
		$subject = "";
		if (! empty($template)) { //tpl
			if (! preg_match('/^(:?tpl)?wiki\:/', $template, $match)) {
				if (! preg_match('/\.tpl$/', $template)) {		// template file
					$template .= '.tpl';
				}
				$template = 'mail/' . $template;
				$subject = str_replace('.tpl', '_subject.tpl', $template);
			} else {	// wiki template
				$pageName = substr($template, strlen($match[0]));
				if (! $tikilib->page_exists($pageName)) {
					Feedback::error(tr('Missing wiki email template page "%0"', htmlspecialchars($template)), 'session');
					$template = '';
				} else {
					$subject_name = str_replace('tpl', 'subject tpl', $pageName);
					if ($tikilib->page_exists($subject_name)) {
						$subject = $match[0] . $subject_name;
					} else {
						$subject_name = str_replace('tpl', 'subject-tpl', $pageName);
						if ($tikilib->page_exists($subject_name)) {
							$subject = $match[0] . $subject_name;
						}
					}
				}
			}
		}
		if (empty($template)) {
			$template = 'mail/tracker_changed_notification.tpl';
		}
		if (empty($subject)) {
			$subject = 'mail/tracker_changed_notification_subject.tpl';
		}
		return [
			'subject' => $subject,
			'template' => $template,
		];
	}


	/**
	 * Translate the watch data and subject for each watcher
	 *
	 * @param string $the_data
	 * @param string $subject
	 * @param string $language
	 * @return array				translated [data, subject]
	 */
	private function translate_watch_data($the_data, $subject, $language)
	{
		// first we look for strings marked "-[...]-" to translate by watcher language
		$watcher_subject = $subject;
		$watcher_data = $the_data;

		if (preg_match_all('/-\[([^\]]*)\]-/', $the_data, $tra_matches) > 0 && $language !== 'en') {
			foreach ($tra_matches[1] as $match) {
				// now we replace the marked strings with correct translations
				$tra_replace = tra($match, $language);
				$tra_match = "/-\[" . preg_quote($match) . "\]-/m";
				$watcher_subject = preg_replace($tra_match, $tra_replace, $watcher_subject);
				$watcher_data = preg_replace($tra_match, $tra_replace, $watcher_data);
			}
		}
		return [$watcher_data, $watcher_subject];
	}

	private function generate_watch_data($old, $new, $trackerId, $itemId, $version)
	{
		global $prefs;

		$tracker_definition = Tracker_Definition::get($trackerId);
		if (! $tracker_definition) {
			return '';
		}

		$oldStatus = $old['status'];
		$newStatus = $new['status'];
		$changed = false;

		$the_data = '';
		if (! empty($oldStatus) || ! empty($newStatus)) {
			if (! empty($itemId) && $oldStatus != $newStatus) {
				 $this->log($version, $itemId, -1, $oldStatus);
			}
			$the_data .= '-[Status]-: ';
			$statusTypes = $this->status_types();
			if (isset($oldStatus) && $oldStatus != $newStatus) {
				$the_data .= isset($statusTypes[$oldStatus]['label']) ? $statusTypes[$oldStatus]['label'] . ' -> ' : '';
				$changed = true;
			}

			if (! empty($newStatus)) {
				$the_data .= $statusTypes[$newStatus]['label'];
			}
			$the_data .= "\n----------\n";
		}

		foreach ($tracker_definition->getFields() as $field) {
			$fieldId = $field['fieldId'];

			$old_value = isset($old[$fieldId]) ? $old[$fieldId] : '';
			$new_value = isset($new[$fieldId]) ? $new[$fieldId] : '';

			if ($old_value == $new_value) {
				continue;
			}

			$handler = $this->get_field_handler($field);
			if ($handler) {
				$the_data .= $handler->watchCompare($old_value, $new_value);
			} else {
				$the_data .= tr('Tracker field not enabled: fieldId=%0 type=%1', $field['fieldId'], tra($field['type'])) . "\n";
			}
			$the_data .= "\n----------\n";
			$changed = true;
		}

		if ($changed || $prefs['tracker_always_notify'] === 'y') {
			return $the_data;
		} else {
			return '';
		}
	}

	private function tracker_is_syncable($trackerId)
	{
		global $prefs;
		if (! empty($prefs["user_trackersync_trackers"])) {
			$trackersync_trackers = unserialize($prefs["user_trackersync_trackers"]);
			return in_array($trackerId, $trackersync_trackers);
		}

		return false;
	}

	private function get_tracker_item_users($trackerId, $values)
	{
		global $user, $prefs;
		$userlib = TikiLib::lib('user');
		$trackersync_users = [$user];

		$definition = Tracker_Definition::get($trackerId);

		if ($definition) {
			$fieldId = $definition->getUserField();
			$value = isset($values[$fieldId]) ? $values[$fieldId] : '';

			if ($value) {
				$trackersync_users = $this->parse_user_field($value);
			}
		}

		return $trackersync_users;
	}

	private function get_tracker_item_coordinates($trackerId, $values)
	{
		$definition = Tracker_Definition::get($trackerId);

		if ($definition && $fieldId = $definition->getGeolocationField()) {
			if (isset($values[$fieldId])) {
				return TikiLib::lib('geo')->parse_coordinates($values[$fieldId]);
			}
		}
	}

	public function sync_user_lang($args)
	{
		global $prefs;

		$trackerId = $args['trackerId'];

		if ($prefs['user_trackersync_lang'] != 'y') {
			return;
		}

		if (! $this->tracker_is_syncable($trackerId)) {
			return;
		}

		$trackersync_users = $this->get_tracker_item_users($trackerId, $args['values']);
		if (empty($trackersync_users)) {
			return;
		}

		$definition = Tracker_Definition::get($trackerId);
		if ($definition && $fieldId = $definition->getLanguageField()) {
			foreach ($trackersync_users as $trackersync_user) {
				TikiLib::lib('tiki')->set_user_preference($trackersync_user, 'language', $args['values'][$fieldId]);
			}
		}
	}

	public function sync_user_realname($args)
	{
		global $prefs;

		$trackerId = $args['trackerId'];

		if (! $this->tracker_is_syncable($trackerId)) {
			return;
		}

		$trackersync_users = $this->get_tracker_item_users($trackerId, $args['values']);
		if (empty($trackersync_users)) {
			return;
		}

		if (! empty($prefs["user_trackersync_realname"])) {
			// Fields to concatenate are delimited by + and priority sets are delimited by ,
			$trackersync_realnamefields = preg_split('/\s*,\s*/', $prefs["user_trackersync_realname"]);

			foreach ($trackersync_realnamefields as $fields) {
				$parts = [];
				$fields = preg_split('/\s*\+\s*/', $fields);
				foreach ($fields as $field) {
					$field = (int) $field;
					if (isset($args['values'][$field])) {
						$parts[] = $args['values'][$field];
					}
				}

				$realname = implode(' ', $parts);

				if (! empty($realname)) {
					foreach ($trackersync_users as $trackersync_user) {
						TikiLib::lib('tiki')->set_user_preference($trackersync_user, 'realName', $realname);
					}
				}
			}
		}
	}

	public function sync_user_geo($args)
	{
		global $prefs;

		$trackerId = $args['trackerId'];

		if (! $this->tracker_is_syncable($trackerId)) {
			return;
		}

		$trackersync_users = $this->get_tracker_item_users($trackerId, $args['values']);
		if (empty($trackersync_users)) {
			return;
		}

		if ($geo = $this->get_tracker_item_coordinates($trackerId, $args['values'])) {
			$tikilib = TikiLib::lib('tiki');

			foreach ($trackersync_users as $trackersync_user) {
				$tikilib->set_user_preference($trackersync_user, 'lon', $geo['lon']);
				$tikilib->set_user_preference($trackersync_user, 'lat', $geo['lat']);
				if (! empty($geo['zoom'])) {
					$tikilib->set_user_preference($trackersync_user, 'zoom', $geo['zoom']);
				}
			}
		}
	}

	public function sync_item_geo($args)
	{
		$trackerId = $args['trackerId'];
		$itemId = $args['object'];

		if ($geo = $this->get_tracker_item_coordinates($trackerId, $args['values'])) {
			if ($geo && $itemId) {
				TikiLib::lib('geo')->set_coordinates('trackeritem', $itemId, $geo);
			}
		}
	}

	public function sync_user_groups($args)
	{
		global $prefs;

		$trackerId = $args['trackerId'];

		if (! $this->tracker_is_syncable($trackerId)) {
			return;
		}

		$trackersync_users = $this->get_tracker_item_users($trackerId, $args['values']);
		if (empty($trackersync_users)) {
			return;
		}

		if (empty($prefs["user_trackersync_groups"])) {
			return;
		}

		$definition = Tracker_Definition::get($trackerId);
		$userslib = TikiLib::lib('user');

		$trackersync_groupfields = preg_split('/\s*,\s*/', $prefs["user_trackersync_groups"]);
		foreach ($trackersync_groupfields as $field) {
			$field = intval($field);
			if (! isset($args['values'][$field])) {
				continue;
			}
			$field = $definition->getField($field);
			$handler = $this->get_field_handler($field, $args['values']);
			$group = $handler->renderOutput();
			if (empty($group) || ! $userslib->group_exists($group)) {
				continue;
			}
			foreach ($trackersync_users as $trackersync_user) {
				if (! $userslib->user_exists($trackersync_user)) {
					continue;
				}
				if ($userslib->user_is_in_group($trackersync_user, $group)) {
					continue;
				}
				$userslib->assign_user_to_group($trackersync_user, $group);
			}
		}
	}

	public function sync_item_auto_categories($args)
	{
		$trackerId = $args['trackerId'];
		$itemId = $args['object'];
		$definition = Tracker_Definition::get($trackerId);

		if ($definition && $definition->isEnabled('autoCreateCategories')) {
			$categlib = TikiLib::lib('categ');
			$tracker_item_desc = $this->get_isMain_value($trackerId, $itemId);

			// Verify that parentCat exists Or Create It
			$parentcategId = $categlib->get_category_id("Tracker $trackerId");
			if (! isset($parentcategId)) {
				$parentcategId = $categlib->add_category(0, "Tracker $trackerId", $definition->getConfiguration('description'));
			}
			// Verify that the sub Categ doesn't already exists
			$currentCategId = $categlib->get_category_id("Tracker Item $itemId");
			if (! isset($currentCategId) || $currentCategId == 0) {
				$currentCategId = $categlib->add_category($parentcategId, "Tracker Item $itemId", $tracker_item_desc);
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
			if (! $catObjectId) {
				$catObjectId = $categlib->add_categorized_object($cat_type, $cat_objid, $cat_desc, $cat_name, $cat_href);
			}
			$categlib->categorize($catObjectId, $currentCategId);
		}
	}

	private function get_viewable_category_field_cats($trackerId)
	{
		$definition = Tracker_Definition::get($trackerId);
		$categories = [];

		if (! $definition) {
			return [];
		}

		foreach ($definition->getFields() as $field) {
			if ($field['type'] == 'e') {
				$parentId = $field['options_array'][0];
				$descends = isset($field['options_array'][3]) && $field['options_array'][3] == 1;
				if (ctype_digit($parentId) && $parentId > 0) {
					$cats = TikiLib::lib('categ')->getCategories(['identifier' => $parentId, 'type' => $descends ? 'descendants' : 'children']);
				} else {
					$cats = [];
				}

				foreach ($cats as $c) {
					$categories[] = $c['categId'];
				}
			}
		}

		return array_unique(array_filter($categories));
	}

	public function invalidate_item_cache($args)
	{
		$itemId = $args['object'];

		$cachelib = TikiLib::lib('cache');
		$cachelib->invalidate('trackerItemLabel' . $itemId);

		if (isset($args['values']) && isset($args['old_values'])) {
			$fields = array_merge(array_keys($args['values']), array_keys($args['old_values']));
			$fields = array_unique($fields);
		}

		if (! empty($fields)) {
			foreach ($fields as $fieldId) {
				$old = isset($args['old_values'][$fieldId]) ? $args['old_values'][$fieldId] : null;
				$new = isset($args['values'][$fieldId]) ? $args['values'][$fieldId] : null;

				if ($old !== $new) {
					$this->invalidate_field_cache($fieldId);
				}
			}
		}
	}

	private function invalidate_field_cache($fieldId)
	{
		global $prefs, $user;
		$multi_languages = $prefs['available_languages'];
		if (! $multi_languages) {
			$multi_languages = [];
		}

		$multi_languages[] = '';

		$cachelib = TikiLib::lib('cache');

		foreach ($multi_languages as $lang) {
			$cachelib->invalidate(md5('trackerfield' . $fieldId . 'o' . $user . $lang));
			$cachelib->invalidate(md5('trackerfield' . $fieldId . 'c' . $user . $lang));
			$cachelib->invalidate(md5('trackerfield' . $fieldId . 'p' . $user . $lang));
			$cachelib->invalidate(md5('trackerfield' . $fieldId . 'op' . $user . $lang));
			$cachelib->invalidate(md5('trackerfield' . $fieldId . 'oc' . $user . $lang));
			$cachelib->invalidate(md5('trackerfield' . $fieldId . 'pc' . $user . $lang));
			$cachelib->invalidate(md5('trackerfield' . $fieldId . 'opc' . $user . $lang));
		}
	}

	public function group_tracker_create($args)
	{
		global $user, $group;
		$trackerId = $args['trackerId'];
		$itemId = $args['object'];
		$new_itemId = isset($args['new_itemId']) ? $args['new_itemId'] : '';
		$tracker_info = isset($args['tracker_info']) ? $args['tracker_info'] : '';
		$definition = Tracker_Definition::get($trackerId);

		if ($definition && $definition->isEnabled('autoCreateGroup')) {
			$creatorGroupFieldId = $definition->getWriterGroupField();

			if (! empty($creatorGroupFieldId) && $definition->isEnabled('autoAssignGroupItem')) {
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
					$userlib->group_inclusion($groupName, $this->table('users_groups')->fetchOne('groupName', ['id' => $groupId]));
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

	public function update_tracker_summary($args)
	{
		$items = $this->items();
		$trackerId = (int) $args['trackerId'];
		$cant_items = $items->fetchCount(['trackerId' => $trackerId]);
		$this->trackers()->update(['items' => (int) $cant_items, 'lastModif' => $this->now], ['trackerId' => $trackerId]);
	}

	public function sync_freetags($args)
	{
		$definition = Tracker_Definition::get($args['trackerId']);

		if ($definition && $field = $definition->getFreetagField()) {
			global $user;
			$freetaglib = TikiLib::lib('freetag');
			$freetaglib->update_tags($user, $args['object'], 'trackeritem', $args['values'][$field]);
		}
	}

	public function update_create_missing_pages($args)
	{
		global $user;
		$tikilib = TikiLib::lib('tiki');

		$definition = Tracker_Definition::get($args['trackerId']);
		if (! $definition) {
			return;
		}

		foreach ($definition->getFields() as $field) {
			$fieldId = $field['fieldId'];
			$value = isset($args['values'][$fieldId]) ? $args['values'][$fieldId] : '';
			if ($field['type'] == 'k' && $value != '' && ! empty($field['options'][2])) {
				if (! $this->page_exists($value)) {
					$IP = $this->get_ip_address();
					$info = $this->get_page_info($field['options'][2]);
					$tikilib->create_page($value, 0, $info['data'], $tikilib->now, '', $user, $IP, $info['description'], $info['lang'], $info['is_html'], [], $info['wysiwyg'], $info['wiki_authors_style']);
				}
			}
		}
	}

	public function get_maximum_value($fieldId)
	{
		return $this->itemFields()->fetchOne($this->itemFields()->expr('MAX(CAST(`value` as UNSIGNED))'), ['fieldId' => (int) $fieldId]);
	}

	public function sync_categories($args)
	{
		$definition = Tracker_Definition::get($args['trackerId']);
		if (! $definition) {
			return;
		}

		$ins_categs = [];
		$parent_categs_only = [];
		$tosync = false;
		$managed_fields = [];

		$categorizedFields = $definition->getCategorizedFields();

		if (isset($args['supplied'])) {
			// Exclude fields that were not part of the request
			$categorizedFields = array_intersect($categorizedFields, $args['supplied']);
		}

		foreach ($categorizedFields as $fieldId) {
			if (isset($args['values'][$fieldId])) {
				$ins_categs = array_merge($ins_categs, array_filter(explode(',', $args['values'][$fieldId])));
				$managed_fields[] = $fieldId;
				$tosync = true;
			}
		}

		if ($tosync) {
			$this->categorized_item($args['trackerId'], $args['object'], "item {$args['object']}", $ins_categs, null, false, $managed_fields);
		}
	}


	/**
	 * Render a field value for input or output. The result depends on the fieldtype.
	 * Note: Each fieldtype has its own input/output handler.
	 * @param array $params - either a complete field array or a trackerid and a permName
	 * <pre>
	 * $param = array(
	 * 		// required
	 * 		'field' => array( 'fieldId' => 1, 'trackerId' => 2, 'permName' => 'myPermName', 'etc' => '...')
	 * 		//'trackerId' => 1 // instread of 'field'
	 * 		//'permName>' => 'myPermName' // instread of 'field'
	 *
	 * 		// optional
	 * 		'item' => array('fieldId1' => fieldValue1, 'fieldId2' => fieldValue2) // optional
	 * 		'itemId' = 5 // itemId
	 * 		'process' => 'y' // ? will be used in xyz
	 *
	 * 		// unsure
	 * 		'list_mode' => '' // i.e. 'cvs' will be used in xyz
	 * )
	 * </pre>
	 * @return string - rendered value (with html ?). i.e from $r = $handler->renderInput($context)
	 */
	public function field_render_value($params)
	{
		// accept either a complete field definition or a trackerId/permName
		if (isset($params['field'])) {
			$field = $params['field'];
		} elseif (isset($params['trackerId'], $params['permName'])) {
			$definition = Tracker_Definition::get($params['trackerId']);
			$field = $definition->getFieldFromPermName($params['permName']);
		} else {
			return tr('Field not specified');
		}

		// preset $item = array('itemId' => value). Either from param or empty
		$item = isset($params['item']) ? $params['item'] : [];

		// if we have an itemId, pass it to our new item structure
		if (isset($params['itemId'])) {
			$item['itemId'] = $params['itemId'];
		}

		// check wether we have a value assigned to $fields.
		// This might be the case if $fields was passed through $params and not from the tracker definition.
		// Build the $items['fieldId'] = value structure
		if (isset($field['value'])) {
			$item[$field['fieldId']] = $field['value'];
		} elseif (isset($item['itemId'])) {
			$item[$field['fieldId']] = $this->get_item_value(null, $item['itemId'], $field['fieldId']);
		}

		// get the handler for the specific fieldtype.
		$handler = $this->get_field_handler($field, $item);

		if ($handler && isset($params['process']) && $params['process'] == 'y') {
			if ($field['type'] === 'e') {	// category
				$requestData = ['ins_' . $field['fieldId'] => explode(',', $field['value'])];
			} else {
				$requestData = $field;
			}
			$linkedField = $handler->getFieldData($requestData);
			$field = array_merge($field, $linkedField);
			$field['ins_id'] = 'ins_' . $field['fieldId'];
			$handler = $this->get_field_handler($field, $item);
		}

		if ($handler) {
			$context = $params;
			$fieldId = $field['fieldId'];
			unset($context['item']);
			unset($context['field']);
			if (empty($context['list_mode'])) {
				$context['list_mode'] = 'n';
			}

			if (! empty($params['editable']) && $params['field']['type'] !== 'STARS') {
				if ($params['editable'] === true) {
					// Some callers pass true/false instead of an actual mode, default to block
					$params['editable'] = 'block';
				}

				if ($params['editable'] == 'direct') {
					$r = $handler->renderInput($context);
					$params['editable'] = 'block';
					$fetchUrl = null;
				} else {
					$r = $handler->renderOutput($context);
					$fetchUrl = [
						'controller' => 'tracker',
						'action' => 'fetch_item_field',
						'trackerId' => $field['trackerId'],
						'itemId' => $item['itemId'],
						'fieldId' => $field['fieldId'],
						'listMode' => $context['list_mode']
					];
				}

				$r = new Tiki_Render_Editable(
					$r,
					[
						'layout' => $params['editable'],
						'label' => $field['name'],
						'group' => ! empty($params['editgroup']) ? $params['editgroup'] : false,
						'object_store_url' => [
							'controller' => 'tracker',
							'action' => 'update_item',
							'trackerId' => $field['trackerId'],
							'itemId' => $item['itemId'],
						],
						'field_fetch_url' => $fetchUrl,
					]
				);
			} else {
				$r = $handler->renderOutput($context);
			}

			TikiLib::lib('smarty')->assign("f_$fieldId", $r);
			$fieldPermName = $field['permName'];
			TikiLib::lib('smarty')->assign("f_$fieldPermName", $r);
			return $r;
		}
	}

	public function get_child_items($itemId)
	{
		return $this->fetchAll('SELECT permName as field, itemId FROM tiki_tracker_item_fields v INNER JOIN tiki_tracker_fields f ON v.fieldId = f.fieldId WHERE f.type = "r" AND v.value = ?', [$itemId]);
	}

	public function get_field_by_perm_name($permName)
	{
		return $this->get_tracker_field($permName);
	}

	public function refresh_index_on_master_update($args)
	{
		// Event handler
		// See pref tracker_refresh_itemlink_detail

		$modifiedFields = [];
		foreach ($args['old_values'] as $key => $old) {
			if (! isset($args['values'][$key]) || $args['values'][$key] != $old) {
				$modifiedFields[] = $key;
			}
		}

		$items = $this->findLinkedItems(
			$args['object'],
			function ($field, $handler) use ($modifiedFields, $args) {
				return $handler->itemsRequireRefresh($args['trackerId'], $modifiedFields);
			}
		);

		$searchlib = TikiLib::lib('unifiedsearch');
		foreach ($items as $itemId) {
			$searchlib->invalidateObject('trackeritem', $itemId);
		}
	}

	private function findLinkedItems($itemId, $callback)
	{
		$fields = $this->table('tiki_tracker_fields');
		$list = $fields->fetchAll(
			$fields->all(),
			['type' => $fields->exactly('r')]
		);

		$toConsider = [];

		foreach ($list as $field) {
			$handler = $this->get_field_handler($field);

			if ($handler && $callback($field, $handler)) {
				$toConsider[] = $field['fieldId'];
			}
		}

		$itemFields = $this->itemFields();
		$items = $itemFields->fetchColumn(
			'itemId',
			[
				'fieldId' => $itemFields->in($toConsider),
				'value' => $itemId,
			]
		);

		return array_unique($items);
	}

	public function update_user_account($args)
	{
		// Try to find if the tracker is a user tracker, flag update to associated user

		$fields = array_keys($args['values']);
		if (! $fields) {
			return;
		}
		$table = $this->table('users_groups');
		$field = $table->fetchOne(
			'usersFieldId',
			[
				'usersFieldId' => $table->in($fields),
			]
		);

		if ($field && ! empty($args['values'][$field])) {
			TikiLib::events()->trigger(
				'tiki.user.update',
				[
					'type' => 'user',
					'object' => $args['values'][$field],
				]
			);
		}
	}
	// connect a user to his user item on the email field / email user
	public function update_user_item($user, $email, $emailFieldId)
	{
		$field = $this->get_tracker_field($emailFieldId);
		$trackerId = $field['trackerId'];
		$definition = Tracker_Definition::get($trackerId);
		$userFieldId = $definition->getUserField();
		$listfields[$userFieldId] = $definition->getField($userFieldId);
		$filterfields[0] = $emailFieldId; // Email field in the user tracker
		$exactvalue[0] = $email;
		$items = $this->list_items($trackerId, 0, -1, 'created', $listfields, $filterfields, '', 'opc', '', $exactvalue);
		$found = false;
		foreach ($items['data'] as $item) {
			if (empty($item['field_values'][0]['value'])) {
				$found = true;
				$this->modify_field($item['itemId'], $userFieldId, $user);
			} elseif ($item['field_values'][0]['value'] == $user) {
				$found = true;
			}
		}
		return $found;
	}


	/**
	 * Called from lib/setup/events.php when object are categorized.
	 * This is to ensure that article and trackeritem categories stay in sync when article indexing is on
	 * as part of the RSS Article generator feature.
	 * @param $args
	 * @param $event
	 * @param $priority
	 * @throws Exception
	 */
	public function sync_tracker_article_categories($args, $event, $priority)
	{
		global $prefs;
		$catlib = TikiLib::lib('categ');
		if ($args['type'] == 'article') {
			//if it's an article, find the associated trackeritem per the relation
			$relationlib = TikiLib::lib('relation');
			$artRelation = $relationlib->get_relations_to('article', $args['object'], 'tiki.article.attach', '', '1');
			if (empty($artRelation)) {
				return;
			}
			$tracker_item_id = $artRelation[0]['itemId'];
			//if the tracker isn't the article tracker as per the pref, don't sync
			if (! $tracker_item_id || $prefs['tracker_article_trackerId'] != $this->get_tracker_for_item($tracker_item_id)) {
				return;
			}
			// get the trackeritem's categories and add or remove the same categories that the article had
			// added or removed as per the event
			$categories = $catlib->get_object_categories('trackeritem', $tracker_item_id);
			$categories_old = $categories;
			foreach ($args['added'] as $added) {
				if (! in_array($added, $categories)) {
					$categories[] = $added;
				}
			}
			foreach ($args['removed'] as $removed) {
				if (in_array($removed, $categories)) {
					$categories = array_diff($categories, [$removed]);
				}
			}
			//update the trackeritems categories if there were new ones added/removed
			if ($categories != $categories_old) {
				$catlib->update_object_categories($categories, $tracker_item_id, 'trackeritem');
			}
		} elseif ($args['type'] == 'trackeritem') {
			//if trackeritem, make sure it's the article tracker that we're dealing with
			$trackerId = $this->get_tracker_for_item($args['object']);
			if ($prefs['tracker_article_trackerId'] != $trackerId) {
				return;
			}
			$definition = Tracker_Definition::get($trackerId);
			//find the article field in this tracker and from there find the relation for the
			$relationlib = TikiLib::lib('relation');
			$artRelation = $relationlib->get_relations_from('trackeritem', $args['object'], 'tiki.article.attach', '', '1');
			if (empty($artRelation)) {
				return;
			}
			$articleId = $artRelation[0]['itemId'];
			// get the articles's categories and add or remove the same categories that the trackeritem had
			// added or removed as per the event
			$categories = $catlib->get_object_categories('article', $articleId);
			$categories_old = $categories;
			foreach ($args['added'] as $added) {
				if (! in_array($added, $categories)) {
					$categories[] = $added;
				}
			}
			foreach ($args['removed'] as $removed) {
				if (in_array($removed, $categories)) {
					$categories = array_diff($categories, [$removed]);
				}
			}
			//update the article's categories if there were new ones added/removed
			if ($categories != $categories_old) {
				$catlib->update_object_categories($categories, $articleId, 'article');
			}
		}
	}

	/**
	 * Called when accessing contents of a Tracker UserSelector field.
	 * Purpose is to parse the csv string of usernames stored inside and format an array.
	 * @param $value csv-formatted string
	 * @return array of resulting usernames
	 */
	public function parse_user_field($value)
	{
		return array_filter(
			array_map(function ($user) {
				return trim($user);
			}, str_getcsv($value))
		);
	}
}
