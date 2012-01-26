<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class DiscountLib extends TikiDb_Bridge
{
	function replace_discount($params) {
		$info = $this->find_discount($params['code'], $params['id']);
		if (empty($info)) {
			if (empty($params['id'])) {
				$query = 'insert into `tiki_discount` (`code`, `value`, `max`, `comment`) values (?, ?, ?, ?)';
				$this->query($query, array($params['code'], $params['value'], $params['max'], $params['comment']));
			} else {
				$query = 'update `tiki_discount` set `code`=?, `value`=?, `max`=?, `comment`=? where `id`=?';
				$this->query($query, array($params['code'], $params['value'], $params['max'], $params['comment'], $params['id']));
			}
			return true;
		} else {
			return false;
		}
	}
	function del_discount($id) {
		$query = 'delete from `tiki_discount` where `id`=?';
		$this->query($query, array($id));
	}
	function use_discount($code) {
		$info = $this->find_discount($code);
		if (empty($info) || $info['max'] == 0) {
			return false;
		} elseif ($info['max'] > 0) {
			$query = 'update `tiki_discount` set `max`=`max`-1 where `code`=?';
			$this->query($query, array($code));
		}
		return $info['value'];
	}
	function find_discount($code, $notid=0) {
		$query = 'select * from `tiki_discount` where `code`=? and `id` !=?';
		$info = $this->fetchAll($query, array($code, $notid), 1, 0); 
		return $info? $info[0]: null;
	}
	function get_discount($id) {
		$query = 'select * from `tiki_discount` where `id` =?';
		$info = $this->fetchAll($query, array($id), 1, 0);
		return $info? $info[0]: null;
	}
	function list_discounts($offset=0, $max=-1) {
		$query = 'select * from `tiki_discount`';
		$bindvars = array();
		$discounts['data'] = $this->fetchAll($query, $bindvars, $max, $offset);
		$query = 'select count(*) from `tiki_discount`';
		$discounts['cant'] = $this->getOne($query, $bindvars);
		return $discounts;
	}
}
global $discountlib;
$discountlib = new DiscountLib;
