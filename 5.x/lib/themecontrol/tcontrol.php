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

class ThemeControlLib extends TikiLib
{

	function tc_assign_category($categId, $theme, $option) {
		$this->tc_remove_cat($categId);

		$themeoption = $this->get_theme_option_string ($theme, $option);

		$query = "delete from `tiki_theme_control_categs` where `categId`=?";
		$this->query($query,array($categId),-1,-1,false);
		$query = "insert into `tiki_theme_control_categs`(`categId`,`theme`) values(?,?)";
		$this->query($query,array($categId,$themeoption));
	}

	function tc_assign_section($section, $theme, $option = '') {
		$this->tc_remove_section($section);

		$themeoption = $this->get_theme_option_string ($theme, $option);

		$query = "delete from `tiki_theme_control_sections` where `section`=?";
		$this->query($query,array($section),-1,-1,false);
		$query = "insert into `tiki_theme_control_sections`(`section`,`theme`) values(?,?)";
		$this->query($query,array($section,$themeoption));
	}

	function tc_assign_object($objId, $theme, $type, $name, $option = '') {

		$themeoption = $this->get_theme_option_string ($theme, $option);

		$objId = md5($type . $objId);
		$this->tc_remove_object($objId);
		$query = "delete from `tiki_theme_control_objects` where `objId`=?";
		$this->query($query,array($objId),-1,-1,false);
		$query = "insert into `tiki_theme_control_objects`(`objId`,`theme`,`type`,`name`) values(?,?,?,?)";
		$this->query($query,array($objId,$themeoption,$type,$name));
	}

	function tc_get_theme_by_categ($categId) {
		if ($this->getOne("select count(*) from `tiki_theme_control_categs` where `categId`=?",array($categId))) {
			return $this->getOne("select `theme` from `tiki_theme_control_categs` where `categId`=?",array($categId));
		} else {
			return '';
		}
	}

	function tc_get_theme_by_section($section) {
		if ($this->getOne("select count(*) from `tiki_theme_control_sections` where `section`=?",array($section))) {
			return $this->getOne("select `theme` from `tiki_theme_control_sections` where `section`=?",array($section));
		} else {
			return '';
		}
	}

	function tc_get_theme_by_object($type, $objId) {
		$objId = md5($type . $objId);

		if ($this->getOne("select count(*) from `tiki_theme_control_objects` where `type`=? and `objId`=?",array($type,$objId))) {
			return $this->getOne("select `theme` from `tiki_theme_control_objects` where `type`=? and `objId`=?",array($type,$objId));
		} else {
			return '';
		}
	}

	function tc_list_categories($offset, $maxRecords, $sort_mode, $find) {

		if ($find) {
			$findesc = '%' . $find . '%';

			$mid = " and (`theme` like ?)";
			$bindvars=array($findesc);
		} else {
			$mid = "";
			$bindvars=array();
		}

		$query = "select tc.`categId`,tc.`name`,`theme` from `tiki_theme_control_categs` ttt,`tiki_categories` tc where ttt.`categId`=tc.`categId` $mid order by ".$this->convertSortMode($sort_mode);
		$query_cant = "select count(*) from `tiki_theme_control_categs` ttt,`tiki_categories` tc where ttt.`categId`=tc.`categId` $mid";
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

	function tc_list_sections($offset, $maxRecords, $sort_mode, $find) {

		if ($find) {
			$findesc = '%' . $find . '%';

			$mid = " where (`theme` like $findesc)";
			$bindvars=array($findesc);
		} else {
			$mid = "";
			$bindvars=array();
		}

		$query = "select * from `tiki_theme_control_sections` $mid order by ".$this->convertSortMode($sort_mode);
		$query_cant = "select count(*) from `tiki_theme_control_sections` $mid";
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

	function tc_list_objects($type, $offset, $maxRecords, $sort_mode, $find) {

		if ($find) {
			$findesc = '%' . $find . '%';
			$mid = " where (`type` like ? and `name` like ?)";
			$bindvars=array($type,$findesc);
		} else if (!empty($type)){
			$mid = " where `type` like ?";
			$bindvars=array($type);
		}

		$query = "select * from `tiki_theme_control_objects` $mid order by ".$this->convertSortMode($sort_mode);
		$query_cant = "select count(*) from `tiki_theme_control_objects` $mid";
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

	function tc_remove_cat($cat) {
		$query = "delete from `tiki_theme_control_categs` where `categId`=?";

		$this->query($query,array($cat));
	}

	function tc_remove_section($section) {
		$query = "delete from `tiki_theme_control_sections` where `section`=?";

		$this->query($query,array($section));
	}

	function tc_remove_object($objId) {
		$query = "delete from `tiki_theme_control_objects` where `objId`=?";

		$this->query($query,array($objId));
	}
	
	// hgelpers for admin forms
	
	function setup_theme_menus() {
		global $prefs, $tikilib, $smarty;
		
		$list_styles = $tikilib->list_styles();
		$smarty->assign_by_ref('styles', $list_styles);
		if (!empty($_REQUEST['theme'])) {
			$a_style = $_REQUEST['theme'];
		} else {
			$a_style = $prefs['style'];
		}
		$smarty->assign('a_style', $a_style);
		$loplist = $tikilib->list_style_options($a_style);
		if (!$loplist) {
			$loplist = Array(tra('None'));
		}
		$smarty->assign_by_ref("style_options", $loplist);
	
	}
	
	function get_theme_option_string ($theme, $option) {
		$themeoption = $theme;
		if ($option) {
			if ($option == tra('None')) {
				$option = '';
			}
			$themeoption .= '/'.$option;
		}
		return $themeoption;
	}
	
	function parse_theme_option_string($themeoption) {
		$p = strpos($themeoption, '/'); // theme option starts after a / char
		$retval = array();
		if ($p === false) {
			$retval[] = $themeoption;
			$retval[] = '';
		} else {
			$retval[] = substr($themeoption, 0, $p);
			$retval[] = substr($themeoption, $p + 1);
		}
		return $retval;
	}
	
}
$tcontrollib = new ThemeControlLib;
