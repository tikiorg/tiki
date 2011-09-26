<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

class LogsQueryLib {
	var $type = "";
	var $id = "";
	var $action = "";
	var $start = "";
	var $end = "";
	var $client = "";
	var $count = false;
	var $limit = null;
	var $desc = true;
	
	static function type($type = "") {
		$me = new self();
		$me->type = $type;
		return $me;
	}
	
	function id($id) {
		$this->id =$id;
		return $this;
	}
	
	function action($action) {
		$this->action = $action;
		return $this;
	}
	
	function start($start) {
		$this->start = $start;
		return $this;
	}
	
	function end($end) {
		$this->end = $end;
		return $this;
	}
	
	function client($client) {
		$this->client = $client;
		return $this;
	}
	
	function count() {
		$this->count = true;
		return $this->fetchAll();
	}
	
	function countByDay() {
	
	}
	
	function limit($limit) {
		$this->limit = $limit;
		return $this;
	}
	
	function desc() {
		$this->desc = true;
		return $this;
	}
	
	function asc() {
		$this->desc = false;
		return $this;
	}
	
	function fetchAll() {
		global $tikilib;
		
		if (empty($this->type)) return array();
		
		$query = "
			SELECT
				".(
					$this->count == true ? 
						"COUNT(actionId) as count" :
						"*"
				)."
			FROM
				tiki_actionlog
			WHERE
				objectType = ?
				".(
					!empty($this->id) ? " AND object = ? " : ""
				)."
				".(
					!empty($this->action) ? " AND action = ? " : ""
				)."
				".(
					!empty($this->start) ? " AND lastModif > ? " : ""
				)."
				".(
					!empty($this->end) ? " AND lastModif < ? " : ""
				)."
				".(
					!empty($this->client) ? " AND client = ? " : ""
				)."
			
			ORDER BY lastModif ". ($this->desc == true ? "DESC" : "ASC") ."
			
			".(!empty($this->limit) ? 
				" LIMIT ".$this->limit
				: ""
			)."
		";
		
		$params = array($this->type);
		
		if (!empty($this->id)) $params[] = $this->id;
		if (!empty($this->action)) $params[] = $this->action;
		if (!empty($this->start)) $params[] = $this->start;
		if (!empty($this->end)) $params[] = $this->end;
		if (!empty($this->client)) $params[] = $this->client;
		
		if ($this->count == true) {
			return $tikilib->getOne($query, $params);
		} else {
			return $tikilib->fetchAll($query, $params);
		}
	}
}

$logsqrylib = new LogsQueryLib;
