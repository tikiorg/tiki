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

class MenuLib extends TikiLib
{

	function empty_menu_cache($menuId = 0)
	{
		global $cachelib; include_once('lib/cache/cachelib.php');
		if ( $menuId > 0 ) {
			$cachelib->empty_type_cache('menu_'.$menuId.'_');
		} else {
			$menus = $this->list_menus();
			foreach ( $menus['data'] as $menu_info ) {
				$cachelib->empty_type_cache('menu_'.$menu_info['menuId'].'_');
			}
		}
	}

	function list_menus($offset = 0, $maxRecords = -1, $sort_mode = 'menuId_asc', $find = '')
	{
		if ($find) {
			$findesc = '%' . $find . '%';
			$mid = " where (`name` like ? or `description` like ?)";
			$bindvars=array($findesc,$findesc);
		} else {
			$mid = "";
			$bindvars=array();
		}

		$query = "select * from `tiki_menus` $mid order by ".$this->convertSortMode($sort_mode);
		$result = $this->query($query,$bindvars,$maxRecords,$offset);
		$query_cant = "select count(*) from `tiki_menus` $mid";
		$cant = $this->getOne($query_cant,$bindvars);
		$ret = array();

		while ( $res = $result->fetchRow() ) {
			$query = "select count(*) from `tiki_menu_options` where `menuId`=?";
			$res["options"] = $this->getOne($query, array((int)$res["menuId"]));
			$ret[] = $res;
		}

		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}

	function replace_menu($menuId, $name, $description='', $type='d', $icon=null, $use_items_icons='n')
	{
		// Check the name
		if (isset($menuId) and $menuId > 0) {
			$query = "update `tiki_menus` set `name`=?,`description`=?,`type`=?, `icon`=?, `use_items_icons`=? where `menuId`=?";
			$bindvars = array($name,$description,$type,$icon,$use_items_icons,(int)$menuId);
			$this->empty_menu_cache($menuId);
		} else {
			// was: replace into. probably we need a delete here
			$query = "insert into `tiki_menus` (`name`,`description`,`type`,`icon`,`use_items_icons`) values(?,?,?,?,?)";
			$bindvars = array($name,$description,$type,$icon,$use_items_icons);
		}

		$result = $this->query($query,$bindvars);
		return true;
	}

	function clone_menu($menuId) {
		$menus = $this->table('tiki_menus');
		$row = $menus->fetchFullRow( array( 'menuId' => $menuId ));
		$row['menuId'] = null;
		$row['name'] = $row['name'] . ' ' . tra('(copy)');
		$newId = $menus->insert( $row );

		$menuoptions = $this->table('tiki_menu_options');
		$oldoptions = $menuoptions->fetchAll( $menuoptions->all(), array( 'menuId' => $menuId ));
		$row = null;

		foreach( $oldoptions as $row ) {
			$row['optionId'] = null;
			$row['menuId'] = $newId;
			$menuoptions->insert( $row );
		}
	}

	/*
	 * Replace the current menu options for id 42 with what's in tiki.sql
	 */
	function reset_app_menu() {
		$tiki_sql = file_get_contents('db/tiki.sql');
		preg_match_all('/^INSERT (?:INTO )?`tiki_menu_options` .*$/mi', $tiki_sql, $matches);

		if ($matches && count($matches[0])) {
			$menuoptions = $this->table('tiki_menu_options');
			$menuoptions->deleteMultiple( array( 'menuId' => 42 ));
			
			foreach ($matches[0] as $query) {
				$this->query($query);
			}
			$this->empty_menu_cache($menuId);
		}
	}

	function get_max_option($menuId)
	{
		$query = "select max(`position`) from `tiki_menu_options` where `menuId`=?";

		$max = $this->getOne($query,array((int)$menuId));
		return $max;
	}

	function replace_menu_option($menuId, $optionId, $name, $url, $type='o', $position=1, $section='', $perm='', $groupname='', $level=0, $icon='')
	{
		if ($optionId) {
			$query = "update `tiki_menu_options` set `name`=?,`url`=?,`type`=?,`position`=?,`section`=?,`perm`=?,`groupname`=?,`userlevel`=?,`icon`=?  where `optionId`=?";
			$bindvars=array($name,$url,$type,(int)$position,$section,$perm,$groupname,$level,$icon,$optionId);
		} else {
			$query = "insert into `tiki_menu_options`(`menuId`,`name`,`url`,`type`,`position`,`section`,`perm`,`groupname`,`userlevel`,`icon`) values(?,?,?,?,?,?,?,?,?,?)";
			$bindvars=array((int)$menuId,$name,$url,$type,(int)$position,$section,$perm,$groupname,$level,$icon);
		}

		$this->empty_menu_cache($menuId);
		$result = $this->query($query, $bindvars);
		return true;
	}

	function remove_menu($menuId)
	{
		$query = "delete from `tiki_menus` where `menuId`=?";
		$result = $this->query($query,array((int)$menuId));

		$query = "delete from `tiki_menu_options` where `menuId`=?";
		$result = $this->query($query,array((int)$menuId));

		$this->empty_menu_cache($menuId);
		return true;
	}

	function remove_menu_option($optionId)
	{
		$query = "select `menuId` from `tiki_menu_options` where `optionId`=?";
		$menuId = $this->getOne($query,array((int)$optionId));

		$query = "delete from `tiki_menu_options` where `optionId`=?";
		$result = $this->query($query,array((int)$optionId));

		$this->empty_menu_cache($menuId);
		return true;
	}

	function get_menu_option($optionId)
	{
		$query = "select * from `tiki_menu_options` where `optionId`=?";

		$result = $this->query($query,array((int)$optionId));

		if (!$result->numRows())
			return false;

		$res = $result->fetchRow();
		return $res;
	}

	function prev_pos ($optionId)
	{
		$query="select `position`, `menuId` from  `tiki_menu_options` where  `optionId` =?";
		$result = $this->query($query, array($optionId));
		if (!($res = $result->fetchRow()))
			return;
		$position1 = $res['position'];
		$menuId = $res['menuId'];
		$query = "select `position` from `tiki_menu_options` where `menuId` =? and `position` < ? order by `position` desc";
		if (!($position = $this->getOne($query, array($menuId, $position1))))
			return;
		$query = "update `tiki_menu_options` set `position`=? where `position`=? and `menuId`=? ";
		$result=$this->query($query,array($position1, $position, $menuId));
		$query = "update `tiki_menu_options` set `position`=? where `optionId`=?";
		$result=$this->query($query,array($position, $optionId,));

		$this->empty_menu_cache($menuId);
	}

	function next_pos ($optionId)
	{
		$query = "select `position`, `menuId` from  `tiki_menu_options` where  `optionId` =?";
		$result = $this->query($query, array($optionId));
		if (!($res = $result->fetchRow()))
			return;
		$position1 = $res['position'];
		$menuId = $res['menuId'];
		$query = "select `position` from `tiki_menu_options` where `menuId` =? and `position` > ? order by `position` asc";
		if (!$position = $this->getOne($query, array($menuId, $position1)))
			return;
		$query = "update `tiki_menu_options` set `position`=? where `position`=? and `menuId`=? ";
		$result = $this->query($query, array($position1, $position, $menuId));
		$query = "update `tiki_menu_options` set `position`=? where `optionId`=?";
		$result = $this->query($query, array($position, $optionId));

		$this->empty_menu_cache($menuId);
	}
	/*
         * gets the result of list_menu_options and create the field "type_description"
         * with description of the type.
         */
	function describe_menu_types($channels)
	{
		if (isset($channels['data'])) {
			$cant = $channels['cant'];
			$channels = $channels['data'];
	  }

	    $types = array("o" => "option",
			   "s" => "section level 0",
			   "r" => "sorted section level 0",
				'1' => 'section level 1',
				'2' => 'section level 2',
				'3' => 'section level 3',
			   "-" => "separator");

		foreach($channels as &$channel) {
			$channel["type_description"] = tra($types[$channel["type"]]);
	  }

	  if (isset($cant)) {
			$channels = array ('data' => $channels,
			'cant' => $cant);
	  }

	    return $channels;

	}

	// rename all the url of the form ((pageName))
	function rename_wiki_page($oldName, $newName)
	{
		$query = "update `tiki_menu_options` set `url`=? where `url`=?";
		$result = $this->query($query, array('(('.$newName.'))', '(('.$oldName.'))'));
		// try to change some tiki-index.php?page - very limitted: for another http://anothersite/tiki-index.php?page= must not be changed
		$query = "select * from `tiki_menu_options` where `url` like ?";
		$result = $this->query($query, array("%tiki-index.php?page=$oldName%"));
		$query = "update `tiki_menu_options` set `url`=? where `optionId`=?";

		$menu_cache_removed = array();
		while ( $res = $result->fetchRow() ) {
			$p = parse_url($res['url']);
	  		if ( $p['path'] == 'tiki-index.php' ) {
				$this->parse_str($p['query'], $p);
				if ( $p['page'] == $oldName ) {
					$url = str_replace($oldName, $newName, $res['url']);
					$this->query($query, array($url, $res['optionId']));
					if ( ! isset($menu_cache_removed[$p['menuId']]) ) {
						$menu_cache_removed[$p['menuId']] = 1;
						$this->empty_menu_cache($p['menuId']);
					}
				}
			}
		}
	}

   	// look if the current url matches the menu option - to be improved a lot
	function menuOptionMatchesUrl($option)
	{
		global $prefs;
		if (empty($option['url'])) {
			return false;
		}
		$url = str_replace('+', ' ', str_replace('&amp;', '&', urldecode($_SERVER['REQUEST_URI'])));
		$option['url'] = str_replace('+', ' ', str_replace('&amp;', '&', urldecode($option['url'])));
		if (strstr($option['url'], 'structure=') && !strstr($url, 'structure=')) { // try to find al the occurence of the page in structures
			$option['url'] = preg_replace('/&structure=.*/', '', $option['url']);
		}
		if (preg_match('/.*tiki.index.php$/', $url)) {
			global $wikilib; include_once('lib/wiki/wikilib.php');
			$homePage = $wikilib->get_default_wiki_page();
			$url .= "?page=$homePage";
		}
		if (preg_match('/.*tiki.index.php$/', $option['url'])) {
			global $wikilib; include_once('lib/wiki/wikilib.php');
			$homePage = $wikilib->get_default_wiki_page();
			$option['url'] .= "?page=$homePage";
		}
		if ($prefs['feature_sefurl'] == 'y' && !empty($option['sefurl'])) {
			$pos = strpos($url, '/'. str_replace('&amp;', '&', urldecode($option['sefurl']))); // position in $url
			$lg = 1 + strlen($option['sefurl']);
		} else {
			$pos = strpos(strtolower($url), strtolower($option['url']));
			$lg = strlen($option['url']);
		}
		if ($pos !== false) {
			$last = $pos + $lg;
			if ($last >= strlen($url) || $url[$last] == '#' || $url[$last] == '?' || $url[$last] == '&') {
				return true;
			}
		}
		return false;
	}

	// assign selected and selectedAscendant to a menu
	// sectionLevel ->shows only the list of submenus where the url is find in this level
	// toLevel -> do not show more than this level
	// also sets setion open/close according to javascript and cookies
	function setSelected($channels, $sectionLevel='', $toLevel='', $params='')
	{
		if (is_numeric($sectionLevel)) { // must extract only the submenu level sectionLevel where the current url is
			$findUrl = false;
			$optionLevel = 0;
			$cant = 0;
			foreach ($channels['data'] as $position=>$option) {
				if (is_numeric($option['type'])) {
					$optionLevel = $option['type'];
				} else if ($option['type'] == '-') {
					$optionLevel = $optionLevel - 1;
				} else if ($option['type'] == 'r' || $option['type'] == 's') {
					$optionLevel = 0;
				}
				if ($optionLevel < $sectionLevel) { //close the submenu
					if ($findUrl) {
						break;
					}
					if (!empty($subMenu))
						unset($subMenu);
					$cant = 0;
				}
				if ($optionLevel >= $sectionLevel - 1 && !empty($option['url']) && $this->menuOptionMatchesUrl($option)) {
					$findUrl = true;
				}
				if ($optionLevel >= $sectionLevel) {
					$subMenu[] = $option;
					++$cant;
					if (!empty($option['url']) && $this->menuOptionMatchesUrl($option)) {
						$findUrl = true;
						$selectedPosition = $cant - 1;
					}
				}
				if ($option['type'] != '-' && $option['type'] != 'o') {
					++$optionLevel;
				}
			}
			if (!empty($subMenu) && $findUrl && $cant) {
				$channels['data'] = $subMenu;
				$channels['cant'] = $cant;
			} else {
				$channels['data'] = array();
				$channels['cant'] = 0;
			}
		} else {
			$selecteds = array();
			$optionLevel = 0;
			foreach ($channels['data'] as $position=>$option) {
				if (is_numeric($option['type'])) {
					$optionLevel = $option['type'];
				} else if ($option['type'] == '-') {
					$optionLevel = $optionLevel - 1;
				} else if ($option['type'] == 'r' || $option['type'] == 's') {
					$optionLevel = 0;
				}
				if ($option['type'] != 'o' && $option['type'] != '-') {
					$selecteds[$optionLevel] = $position;
				}
				if ($this->menuOptionMatchesUrl($option)) {
					$selectedPosition = $position;
					break;
				}
				if ($option['type'] != '-' && $option['type'] != 'o') {
					++$optionLevel;
				}
			}
			if (isset($selectedPosition)) {
				for ($o = 0; $o < $optionLevel; ++$o) {
					$channels['data'][$selecteds[$o]]['selectedAscendant'] = true;
				}
			}
		}
		if (isset($selectedPosition)) {
			$channels['data'][$selectedPosition]['selected'] = true;
		}
		if (is_numeric($toLevel)) {
			$subMenu = array();
			$cant = 0;
			foreach ($channels['data'] as $position=>$option) {
				if (is_numeric($option['type'])) {
					$optionLevel = $option['type'];
				} else if ($option['type'] == '-') {
					$optionLevel = $optionLevel - 1;
				} else if ($option['type'] == 'r' || $option['type'] == 's') {
					$optionLevel = 0;
				}
				if ($optionLevel <= $toLevel) {
					$subMenu[] = $option;
					$cant++;
				}
				if ($option['type'] != '-' && $option['type'] != 'o') {
					++$optionLevel;
				}
			}
			$channels = array('data'=>$subMenu, 'cant'=>$cant);
		}
		// set sections open/close according to cookie
		if (!empty($params['id'])) {
			global $prefs;
			foreach ($channels['data'] as $position => &$option) {
				$option['open'] = false;
				if (!empty($params['menu_cookie']) && $params['menu_cookie'] == 'n') {
					if (!empty($option['selected']) || !empty($option['selectedAscendant'])) {
						$option['open'] = true;
					}
				} elseif ($option['type'] == 's') {
					$ck = getCookie('menu'.$params['id'].'__'.$option['position'], 'menu', 'o');
					$option['open'] = ($prefs['javascript_enabled'] == 'n' || $ck == 'o');
				}
			}
		}
		return $channels;
	}
	
	// check if a option belongs to a menu
	function check_menu_option($menuId, $optionId)
	{
		$query = 'SELECT `menuId` FROM `tiki_menu_options` WHERE `optionId` = ?';
		$dbMenuId = $this->getOne($query, array($optionId));
		if ($dbMenuId == $menuId) {
			return true;
		} else {
			return false;
		}
	}
	
	function import_menu_options()
	{
		global $smarty;
		$options = array();
		$fname = $_FILES['csvfile']['tmp_name'];
		$fhandle = fopen($fname, "r");
		$fields = fgetcsv($fhandle, 1000);
		if (!$fields[0]) {
			$smarty->assign('msg', tra('The file is not a CSV file or has not a correct syntax'));
			$smarty->display("error.tpl");
			die;
		}
		while (!feof($fhandle)) {
			$res = array('optionId'=>'', 'type'=>'', 'name'=>'', 'url'=>'', 'position'=>0, 'section'=>'', 'perm'=>'', 'groupname'=>'', 'userlevel'=>'', 'remove'=>'');
			$data = fgetcsv($fhandle, 1000);
			if (empty($data))
				continue;
			for ($i = 0, $icount_fields = count($fields); $i < $icount_fields; $i++) {
				$res[$fields[$i]] = $data[$i];
			}
			if ($res['optionId'] == 0 || $this->check_menu_option($_REQUEST['menuId'], $res['optionId'])) {
				$options[] = $res;
			} else {
				$smarty->assign('msg', tra('You can only use optionId = 0 to create a new option or optionId equal an id that already belongs to the menu to update it.'));
				$smarty->display('error.tpl');
				die;
			}
		}
		fclose($fhandle);
		foreach ($options as $option) {
			if ($option['remove'] == 'y') {
				$this->remove_menu_option($option['optionId']);
			} else {
				$this->replace_menu_option($_REQUEST['menuId'], $option['optionId'], $option['name'], $option['url'], $option['type'], $option['position'], $option['section'], $option['perm'], $option['groupname'], $option['userlevel']);
			}
		}
	}
	
	function export_menu_options()
	{
		$data = '"optionId","type","name","url","position","section","perm","groupname","userlevel","remove"' . "\r\n";
		$options = $this->list_menu_options($_REQUEST['menuId'], 0, -1, 'position_asc', '', true);
		foreach ($options['data'] as $option) {
			$data .=  $option['optionId']
							. ',"' . $option['type']
							. '","' . str_replace('"', '""', $option['name'])
							. '","' . str_replace('"', '""', $option['url'])
							. '",' . $option['position']
							. ',"' . $option['section']
							. '","' . $option['perm']
							. '","' . $option['groupname']
							. '",' . $option['userlevel']
							. ',"n"' . "\r\n"
							;
		}
		if (empty($_REQUEST['encoding'])) {
			$_REQUEST['encoding'] = 'UTF-8';
		} elseif ($_REQUEST['encoding'] == 'ISO-8859-1') {
			$data = utf8_decode($data);
		}
		header("Content-type: text/comma-separated-values; charset:".$_REQUEST['encoding']);
		header("Content-Disposition: attachment; filename=".tra('menu')."_".$_REQUEST['menuId'].".csv");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
		header("Pragma: public");
		echo $data;
		die;
	}
	function get_option($menuId, $url) {
		$query = 'select `optionId` from `tiki_menu_options` where `menuId`=? and `url`=?';
		return $this->getOne($query, array($menuId, $url));
	}
}
$menulib = new MenuLib;
