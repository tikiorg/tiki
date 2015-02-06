<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}

/*
ThemeControlLib 
@extends ThemeLib
*/
class ThemeControlLib extends ThemeLib
{

	/*
	@param $categId
	@param $theme
	@param $option
	*/
    function tc_assign_category($categId, $theme)
	{
		$this->tc_remove_cat($categId);
		$query = "delete from `tiki_theme_control_categs` where `categId`=?";
		$this->query($query, array($categId), -1, -1, false);
		$query = "insert into `tiki_theme_control_categs`(`categId`,`theme`) values(?,?)";
		$this->query($query, array($categId, $theme));
	}

	/*
	@param $section
	@param $theme
	@param string $option
	*/
	function tc_assign_section($section, $theme)
	{
		$this->tc_remove_section($section);
		$query = "delete from `tiki_theme_control_sections` where `section`=?";
		$this->query($query, array($section), -1, -1, false);
		$query = "insert into `tiki_theme_control_sections`(`section`,`theme`) values(?,?)";
		$this->query($query, array($section, $theme));
	}

	/*
	@param $objId
	@param $theme
	@param $type
	@param $name
	@param string $option
	*/
    function tc_assign_object($objId, $theme, $type, $name)
	{
		$objId = md5($type . $objId);
		$this->tc_remove_object($objId);
		$query = "delete from `tiki_theme_control_objects` where `objId`=?";
		$this->query($query, array($objId), -1, -1, false);
		$query = "insert into `tiki_theme_control_objects`(`objId`,`theme`,`type`,`name`) values(?,?,?,?)";
		$this->query($query, array($objId, $theme, $type, $name));
	}

    /*
    @param $categId
    @return string
    */
    function tc_get_theme_by_categ($categId)
	{
		if ($this->getOne("select count(*) from `tiki_theme_control_categs` where `categId`=?", array($categId))) {
			return $this->getOne("select `theme` from `tiki_theme_control_categs` where `categId`=?", array($categId));
		} else {
			return '';
		}
	}

	/*
	@param $section
	@return string
	*/
    function tc_get_theme_by_section($section)
	{
		if ($this->getOne("select count(*) from `tiki_theme_control_sections` where `section`=?", array($section))) {
			return $this->getOne("select `theme` from `tiki_theme_control_sections` where `section`=?", array($section));
		} else {
			return '';
		}
	}

	/*
	@param $type
	@param $objId
	@return string
	*/
	function tc_get_theme_by_object($type, $objId)
	{
		$objId = md5($type . $objId);
		if ($this->getOne("select count(*) from `tiki_theme_control_objects` where `type`=? and `objId`=?", array($type, $objId))) {
			return $this->getOne("select `theme` from `tiki_theme_control_objects` where `type`=? and `objId`=?", array($type, $objId));
		} else {
			return '';
		}
	}

	/*
	@param $offset
	@param $maxRecords
	@param $sort_mode
	@param $find
	@return array
	*/
	function tc_list_categories($offset, $maxRecords, $sort_mode, $find)
	{
		if ($find) {
			$findesc = '%' . $find . '%';
			$mid = " and (`theme` like ?)";
			$bindvars=array($findesc);
		} else {
			$mid = "";
			$bindvars=array();
		}
		$query = "select tc.`categId`,tc.`name`,`theme`" .
						" from `tiki_theme_control_categs` ttt,`tiki_categories` tc where ttt.`categId`=tc.`categId` $mid" .
						" order by " . $this->convertSortMode($sort_mode);
		$query_cant = "select count(*) from `tiki_theme_control_categs` ttt,`tiki_categories` tc where ttt.`categId`=tc.`categId` $mid";
		$result = $this->query($query, $bindvars, $maxRecords, $offset);
		$cant = $this->getOne($query_cant, $bindvars);
		$ret = array();
		while ($res = $result->fetchRow()) {
			$ret[] = $res;
		}
		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}
	
	/*
	@param $offset
	@param $maxRecords
	@param $sort_mode
	@param $find
	@return array
	*/
	function tc_list_sections($offset, $maxRecords, $sort_mode, $find)
	{
		if ($find) {
			$findesc = '%' . $find . '%';
			$mid = " where (`theme` like $findesc)";
			$bindvars = array($findesc);
		} else {
			$mid = "";
			$bindvars = array();
		}
		$query = "select * from `tiki_theme_control_sections` $mid order by " . $this->convertSortMode($sort_mode);
		$query_cant = "select count(*) from `tiki_theme_control_sections` $mid";
		$result = $this->query($query, $bindvars, $maxRecords, $offset);
		$cant = $this->getOne($query_cant, $bindvars);
		$ret = array();
		while ($res = $result->fetchRow()) {
			$ret[] = $res;
		}

		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}
	
	/*
	@param $type
	@param $offset
	@param $maxRecords
	@param $sort_mode
	@param $find
	@return array
	*/
	function tc_list_objects($type, $offset, $maxRecords, $sort_mode, $find)
	{
		if ($find) {
			$findesc = '%' . $find . '%';
			$mid = " where (`type` like ? and `name` like ?)";
			$bindvars = array($type, $findesc);
		} else if (!empty($type)) {
			$mid = " where `type` like ?";
			$bindvars = array($type);
		}
		$query = "select * from `tiki_theme_control_objects` $mid order by " . $this->convertSortMode($sort_mode);
		$query_cant = "select count(*) from `tiki_theme_control_objects` $mid";
		$result = $this->query($query, $bindvars, $maxRecords, $offset);
		$cant = $this->getOne($query_cant, $bindvars);
		$ret = array();
		while ($res = $result->fetchRow()) {
			$ret[] = $res;
		}
		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}

	/*
	@param $cat
	*/
	function tc_remove_cat($cat)
	{
		$query = "delete from `tiki_theme_control_categs` where `categId`=?";
		$this->query($query, array($cat));
	}
	
	/*
	@param $section
	*/
	function tc_remove_section($section)
	{
		$query = "delete from `tiki_theme_control_sections` where `section`=?";
		$this->query($query, array($section));
	}
	
	/*
	@param $objId
	*/
	function tc_remove_object($objId)
	{
		$query = "delete from `tiki_theme_control_objects` where `objId`=?";
		$this->query($query, array($objId));
	}

	/*
	@param $type
	@return: the theme control theme (tc_theme) and option (tc_theme_option). First check section, than override with category than finally override with object setting.
	*/
	function get_tc_theme($type, $objectId)
	{
		global $prefs, $section;
		$categlib = TikiLib::lib('categ');
		
		//SECTION
		if (!empty($section)) {
			$tc_themeoption = $this->tc_get_theme_by_section($section);
		}
		
		//CATEGORY
		$tc_categs = $categlib->get_object_categories($type, $objectId);
		if (count($tc_categs)) {
			$cat_themes = array();	// collect all the category themes
			foreach ($tc_categs as $cat) {
				$ct = $this->tc_get_theme_by_categ($cat);
				if (!empty($ct) && !in_array($ct, $cat_themes)) {
					$cat_themes[] = $ct;
					if ($prefs['feature_theme_control_autocategorize'] == 'y' && !empty($cat)) {
						$_SESSION['tc_theme_cat'] = $cat;
						$_SESSION['tc_theme_cattheme'] = $ct;
					}
				}
			}
			if (count($cat_themes) == 1) {	// only use category theme if there is exactly one set
				$tc_themeoption = $cat_themes[0];
			}
		}
		
		//OBJECT
		if (!empty($this->tc_get_theme_by_object($type, $objectId))) {
			$tc_themeoption = $this->tc_get_theme_by_object($type, $objectId);
		}
		
		//Autocategory -> leave it as it was, but unsure about the sense of this
		if ($prefs['feature_theme_control_autocategorize'] == 'y') {
			$tc_themeoption = $_SESSION['tc_theme_cattheme'];
		}
		
		//create the array with tc_theme and tc_theme_option
		list($tc_theme, $tc_theme_option) = $this->extract_theme_and_option($tc_themeoption);
		
		//return array
		return array($tc_theme, $tc_theme_option);
	}
}
