<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}

/**
 *
 */
class BanLib extends TikiLib
{
    /**
     * @param $banId
     * @return mixed
     */
    function get_rule($banId)
	{
		$query = "select * from `tiki_banning` where `banId`=?";

		$result = $this->query($query, array($banId));
		$res = $result->fetchRow();
		$aux = array();
		$query2 = "select `section` from `tiki_banning_sections` where `banId`=?";
		$result2 = $this->query($query2, array($banId));
		$aux = array();

		while ($res2 = $result2->fetchRow()) {
			$aux[] = $res2['section'];
		}

		$res['sections'] = $aux;
		return $res;
	}

    /**
     * @param $banId
     */
    function remove_rule($banId)
	{
		$query = "delete from `tiki_banning` where `banId`=?";

		$this->query($query, array($banId));
		$query = "delete from `tiki_banning_sections` where `banId`=?";
		$this->query($query, array($banId));
	}

    /**
     * @param $offset
     * @param $maxRecords
     * @param $sort_mode
     * @param $find
     * @return array
     */
    function list_rules($offset, $maxRecords, $sort_mode, $find)
	{

		if ($find) {
			$findesc = '%' . $find . '%';

			$mid = " where ((`message` like ?) or (`title` like ?))";
			$bindvars = array($findesc, $findesc);
		} else {
			$mid = "";
			$bindvars = array();
		}

		$query = "select * from `tiki_banning` $mid order by " . $this->convertSortMode($sort_mode);
		$query_cant = "select count(*) from `tiki_banning` $mid";
		$result = $this->query($query, $bindvars, $maxRecords, $offset);
		$cant = $this->getOne($query_cant, $bindvars);
		$ret = array();

		while ($res = $result->fetchRow()) {
			$aux = array();

			$query2 = "select * from `tiki_banning_sections` where `banId`=?";
			$result2 = $this->query($query2, array($res['banId']));

			while ($res2 = $result2->fetchRow()) {
				$aux[] = $res2;
			}

			$res['sections'] = $aux;
			$ret[] = $res;
		}

		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		$query = "select `banId` from `tiki_banning` where `use_dates`=? and `date_to` < FROM_UNIXTIME(?)";
		$result = $this->query($query, array('y', $this->now));

		while ($res = $result->fetchRow()) {
			$this->remove_rule($res['banId']);
		}

		return $retval;
	}

    /**
     * @param $rules
     * @return string
     */
    function export_rules($rules)
	{
		$csv = "banId,mode,title,ip1,ip2,ip3,ip4,user,date_from,date_to,use_dates,created,created_readable,message,sections\n";
		foreach ($rules as $rule) {
			if (!isset($rule['title'])) {
				$rule['title'] = '';
			}
			if (isset($rule['user'])) {
				$rule['ip1'] = '';
				$rule['ip2'] = '';
				$rule['ip3'] = '';
				$rule['ip4'] = '';
			}
			if ($rule['mode'] == 'ip') {
				$rule['user'] = '';
			}
			if ($rule['use_dates'] != 'y') {
				$rule['date_from'] = '';
				$rule['date_to'] = '';
			}
			if (!isset($rule['message'])) {
				$rule['message'] = '';
			}
			$csv .= '"' . $rule['banId']
					. '","' . $rule['mode']
					. '","' . $rule['title']
					. '","' . $rule['ip1']
					. '","' . $rule['ip2']
					. '","' . $rule['ip3']
					. '","' . $rule['ip4']
					. '","' . $rule['user']
					. '","' . $rule['date_from']
					. '","' . $rule['date_to']
					. '","' . $rule['use_dates']
					. '","' . $rule['created']
					. '","' . $this->date_format("%y%m%d %H:%M", $rule['created'])
					. '","' . $rule['message'] . '","';

			if (!empty($rule['sections'])) {
				foreach ($rule['sections'] as $section) {
					$csv .= $section['section'] . '|';
				}
				$csv = rtrim($csv, '|');
			}
			$csv .= "\"\n";
		}
		return $csv;
	}

    /**
     * @param $fname
     * @param $import_as_new
     * @return int
     */
    function importCSV($fname, $import_as_new)
	{
		$fields = false;
		if ($fhandle = fopen($fname, 'r')) {
			$fields = fgetcsv($fhandle, 1000);
		}
		if ($fields === false) {
			$smarty = TikiLib::lib('smarty');

			$smarty->assign('msg', tra("The file has incorrect syntax or is not a CSV file"));
			$smarty->display("error.tpl");
			die;
		}
		$nb = 0;
		while (($data = fgetcsv($fhandle, 1000)) !== FALSE) {
			$d = array("banId" => "", "mode" => "", "title" => "", "ip1" => "", "ip2" => "",
					   "ip3" => "", "ip4" => "", "user" => "", "date_from" => "", "date_to" => "", "use_dates" => "", "created" => "", "created_readable" => "", "message" => "");
			foreach ($fields as $field) {
				$d[$field] = $data[array_search($field, $fields)];
			}
			if (empty($d['message'])) {
				$d['message'] = tra('Spam is not welcome here');
			}
			if ($import_as_new) {
				$d['banId'] = 0;
			}
			$nb++;

			$this->replace_rule(
				$d['banId'], $d['mode'], $d['title'], $d['ip1'], $d['ip2'], $d['ip3'], $d['ip4'],
				$d['user'], strtotime($d['date_from']), strtotime($d['date_to']), $d['use_dates'], $d['message'],
				explode('|', $d['sections'])
			);
		}
		fclose($fhandle);
		return $nb;
	}

	/*
	banId integer(12) not null auto_increment,
	  mode enum('user','ip'),
	  title varchar(200),
	  ip1 integer(3),
	  ip2 integer(3),
	  ip3 integer(3),
	  ip4 integer(3),
	  user varchar(200),
	  date_from timestamp,
	  date_to timestamp,
	  use_dates char(1),
	  message text,
	  primary key(banId)
	  */
    /**
     * @param $banId
     * @param $mode
     * @param $title
     * @param $ip1
     * @param $ip2
     * @param $ip3
     * @param $ip4
     * @param $user
     * @param $date_from
     * @param $date_to
     * @param $use_dates
     * @param $message
     * @param $sections
     */
    function replace_rule($banId, $mode, $title, $ip1, $ip2, $ip3, $ip4, $user, $date_from, $date_to, $use_dates, $message, $sections)
	{
		if (empty($title)) {
			$title = empty($user) ? "$ip1.$ip2.$ip3.$ip4" : $user;
		}

		$count = TikiDb::get()->table('tiki_banning')->fetchCount(array('banId' => $banId));
		if ($banId && $count > 0) {
			$query = "update `tiki_banning` set `title`=?, `ip1`=?, `ip2`=?, `ip3`=?, `ip4`=?, `user`=?, " .
					 "`date_from` = FROM_UNIXTIME(?), `date_to` = FROM_UNIXTIME(?), `use_dates` = ?, `message` = ? where `banId`=?";

			$this->query($query, array($title, $ip1, $ip2, $ip3, $ip4, $user, $date_from, $date_to, $use_dates, $message, $banId));
		} else {
			$query = "insert into `tiki_banning`(`mode`,`title`,`ip1`,`ip2`,`ip3`,`ip4`,`user`,`date_from`,`date_to`,`use_dates`,`message`,`created`) " .
					 "values(?,?,?,?,?,?,?,FROM_UNIXTIME(?),FROM_UNIXTIME(?),?,?,?)";
			$this->query($query, array($mode, $title, $ip1, $ip2, $ip3, $ip4, $user, $date_from, $date_to, $use_dates, $message, $this->now));
			$banId = $this->getOne("select max(`banId`) from `tiki_banning` where `created`=?", array($this->now));
		}

		$query = "delete from `tiki_banning_sections` where `banId`=?";
		$this->query($query, array($banId));

		foreach ($sections as $section) {
			$query = "insert into `tiki_banning_sections`(`banId`,`section`) values(?,?)";

			$this->query($query, array($banId, $section));
		}
	}
}

$banlib = new BanLib;
