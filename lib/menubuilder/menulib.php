<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

class MenuLib extends TikiLib {
	function MenuLib($db) {
		$this->TikiLib($db);
	}

	function list_menus($offset, $maxRecords, $sort_mode, $find) {

		if ($find) {
			$findesc = '%' . $find . '%';

			$mid = " where (`name` like ? or `description` like ?)";
			$bindvars=array($findesc,$findesc);
		} else {
			$mid = "";
			$bindvars=array();
		}

		$query = "select * from `tiki_menus` $mid order by ".$this->convert_sortmode($sort_mode);
		$query_cant = "select count(*) from `tiki_menus` $mid";
		$result = $this->query($query,$bindvars,$maxRecords,$offset);
		$cant = $this->getOne($query_cant,$bindvars);
		$ret = array();

		while ($res = $result->fetchRow()) {
			$query = "select count(*) from `tiki_menu_options` where `menuId`=?";

			$res["options"] = $this->getOne($query,array((int)$res["menuId"]));
			$ret[] = $res;
		}

		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}

	function replace_menu($menuId, $name, $description, $type) {
		// Check the name
		if (isset($menuId) and $menuId > 0) {
			$query = "update `tiki_menus` set `name`=?,`description`=?,`type`=? where `menuId`=?";
			$bindvars=array($name,$description,$type,(int)$menuId);
		} else {
			// was: replace into. probably we need a delete here
			$query = "insert into `tiki_menus`(`name`,`description`,`type`) values(?,?,?)";
			$bindvars=array($name,$description,$type);
		}

		$result = $this->query($query,$bindvars);
		return true;
	}

	function get_max_option($menuId) {
		$query = "select max(`position`) from `tiki_menu_options` where `menuId`=?";

		$max = $this->getOne($query,array((int)$menuId));
		return $max;
	}

	function replace_menu_option($menuId, $optionId, $name, $url, $type='o', $position=1, $section='', $perm='', $groupname='', $level=0) {
		if ($optionId) {
			$query = "update `tiki_menu_options` set `name`=?,`url`=?,`type`=?,`position`=?,`section`=?,`perm`=?,`groupname`=?,`userlevel`=?  where `optionId`=?";
			$bindvars=array($name,$url,$type,(int)$position,$section,$perm,$groupname,$level,$optionId);
		} else {
			$query = "insert into `tiki_menu_options`(`menuId`,`name`,`url`,`type`,`position`,`section`,`perm`,`groupname`,`userlevel`) values(?,?,?,?,?,?,?,?,?)";
			$bindvars=array((int)$menuId,$name,$url,$type,(int)$position,$section,$perm,$groupname,$level);
		}

		$result = $this->query($query, $bindvars);
		return true;
	}

	function remove_menu($menuId) {
		$query = "delete from `tiki_menus` where `menuId`=?";

		$result = $this->query($query,array((int)$menuId));
		$query = "delete from `tiki_menu_options` where `menuId`=?";
		$result = $this->query($query,array((int)$menuId));
		return true;
	}

	function remove_menu_option($optionId) {
		$query = "delete from `tiki_menu_options` where `optionId`=?";

		$result = $this->query($query,array((int)$optionId));
		return true;
	}

	function get_menu_option($optionId) {
		$query = "select * from `tiki_menu_options` where `optionId`=?";

		$result = $this->query($query,array((int)$optionId));

		if (!$result->numRows())
			return false;

		$res = $result->fetchRow();
		return $res;
	}

	function prev_pos ($optionId) {
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
	}

	function next_pos ($optionId) {
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
	}
	/*
         * gets the result of list_menu_options and create the field "type_description"
         * with description of the type.
         */
	function describe_menu_types($channels) {

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

	    for ($i=0; $i<sizeof($channels); $i++) {
		$channels[$i]["type_description"] = tra($types[$channels[$i]["type"]]);
	    }

	    if (isset($cant)) {
		$channels = array ('data' => $channels,
				   'cant' => $cant);
	    }

	    return $channels;

	}
	// rename all the url of the form ((pageName))
	function rename_wiki_page($oldName, $newName) {
		$query = "update `tiki_menu_options` set `url`=? where `url`=?";
		$result = $this->query($query, array('(('.$newName.'))', '(('.$oldName.'))'));
		// try to change some tiki-index.php?page - very limitted: for another http://anothersite/tiki-index.php?page= must not be changed
		$query = "select * from `tiki_menu_options` where `url` like ?";
		$result = $this->query($query, array("%tiki-index.php?page=$oldName%"));
		$query = "update `tiki_menu_options` set `url`=? where `optionId`=?";
		while ($res = $result->fetchRow()) {
			$p = parse_url($res['url']);
	  		if ($p['path'] == 'tiki-index.php') {
				parse_str($p['query'], $p);
				if ($p['page'] == $oldName) {
					$url = str_replace($oldName, $newName, $res['url']);
					$this->query($query, array($url, $res['optionId']));
				}
			}
		}
	}
   	// look if the current url matches the menu option - do be improved a lot
	function menuOptionMatchesUrl($option) {
		global $prefs;
		if (empty($option['url'])) {
			return false;
		}
		$url = urldecode($_SERVER['REQUEST_URI']);
		if ($prefs['feature_sefurl'] == 'y' && !empty($option['sefurl'])) {
			$pos = strpos($url, '/'.$option['sefurl']);
			$lg = 1 + strlen($option['sefurl']);
		} else {
			$pos = strpos($url, $option['url']);
			$lg = strlen($option['url']);
		}
		if ($pos !== false) {
			$last = $pos + $lg;
			if ($last >= strlen($url) || $url['last'] == '#' || $url['last'] == '?' || $url['last'] == '&') {
				return true;
			}
		}
		return false;
	}
	// assign selected and selectedAscendant to a menu
	// sectionLevel ->shows only the list of submenus where the url is find in this level
	// toLevel -> do not show more than this level
	function setSelected($channels, $sectionLevel='', $toLevel='') {
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
		return $channels;
	}
}
global $dbTiki;
$menulib = new MenuLib($dbTiki);

?>
