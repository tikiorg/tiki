<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

class FlinksLib extends TikiLib
{

	function add_featured_link($url, $title, $description = '', $position = 0, $type = 'f') {
		$query = "delete from `tiki_featured_links` where `url`=?";
		$result = $this->query($query,array($url),-1,-1,false);
		$query = "insert into `tiki_featured_links`(`url`,`title`,`description`,`position`,`hits`,`type`) values(?,?,?,?,?,?)";
		$result = $this->query($query,array($url,$title,$description,$position,0,$type));
	}

	function remove_featured_link($url) {
		$query = "delete from `tiki_featured_links` where `url`=?";

		$result = $this->query($query,array($url));
	}

	function update_featured_link($url, $title, $description, $position = 0, $type = 'f') {
		$query = "update `tiki_featured_links` set `title`=?, `type`=?, `description`=?, `position`=? where `url`='$url'";

		$result = $this->query($query,array($title,$type,$description,$position,$url));
	}

	function add_featured_link_hit($url) {
		global $prefs, $user;

		if ($prefs['count_admin_pvs'] == 'y' || $user != 'admin') {
			$query = "update `tiki_featured_links` set `hits` = `hits` + 1 where `url` = ?";

			$result = $this->query($query,array($url));
		}
	}

	function get_featured_link($url) {
		$query = "select * from `tiki_featured_links` where `url`=?";

		$result = $this->query($query,array($url));

		if (!$result->numRows())
			return false;

		$res = $result->fetchRow();
		return $res;
	}

	function generate_featured_links_positions() {
		$query = "select `url` from `tiki_featured_links` order by `hits` desc";

		$result = $this->query($query,array());
		$position = 1;

		while ($res = $result->fetchRow()) {
			$url = $res["url"];

			$query2 = "update `tiki_featured_links` set `position`=? where `url`=?";
			$result2 = $this->query($query2,array($position,$url));
			$position++;
		}

		return true;
	}
}
$flinkslib = new FlinksLib;
