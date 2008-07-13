<?php

// $Id: /cvsroot/tikiwiki/tiki/lib/mypage/mypagelib.php,v 1.86 2007-10-16 14:53:07 niclone Exp $
// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

/* checks about features */
global $prefs;

if ($prefs['feature_ajax'] != 'y') {
	die("feature ajax is required for mypage");
}

define('MYPAGEBORDER_W', 10);
define('MYPAGEBORDER_H', 20);

/*
 * convert an array recursively to a javascript array
 */
function phptojsarray($var, $offset="") {
	if (is_array($var)) {
		$offset.="  ";
		$str="{\n".$offset."'COUNT' : ".count($var);
		foreach($var as $k => $v) {
			$str.=",\n".$offset."'$k' : ".phptojsarray($v, $offset);
		}
		$str.=" }";
	} else if (is_numeric($var)) {
		$str="".$var;
	} else if (is_bool($var)) {
		$str=($var ? 'true' : 'false');
	} else if (is_string($var)) {
		$str="'".str_replace(array("\n", "\r", "</"), array("\\n", "\\r", "<'+'/"), addslashes($var))."'";
	} else if (is_null($var)) {
		$str="null";
	} else {
		$str="null";
	}
	
	return $str;
}
define ('MYERROR_ENOENT', 2);
define ('MYERROR_EIO', 5);
define ('MYERROR_EACCESS', 13);
define ('MYERROR_EINVAL', 22);

class MyError {
	var $code;
	var $str;
	function MyError($code, $str) {
		$this->code=$code;
		$this->str=$str;
	}
	function getErrorString() {
		return $this->str;
	}
	function getErrorCode() {
		return $this->code;
	}
}

function is_myerror($obj) {
	return (is_object($obj) && is_a($obj, 'MyError'));
}

/*
 * MyPage object is the container of MyPageWindow
 */
class MyPage {
	var $id;
	var $id_users;
	var $windows;
	var $lastid;
	var $params;
	var $modified;
// 	var $perms;
	var $lasterror;
	var $typeclass;
	var $clonning;

	/* never call this constructor directly */
	function MyPage($id=NULL, $id_users) {
		$this->id=$id;
		$this->id_users=$id_users; //the viewer
		$this->windows=array();
		$this->lastid=0;
		$this->params=array();
		$this->modified=array();
		$this->lasterror=NULL;
		$this->typeclass=NULL;
		$this->clonning=false;
	}

	/*static*/
	function getMyPage_new($id_users, $id_types) {
		$mypage=new MyPage(NULL, $id_users);
		$mypage->params['id_users']=(int)$id_users;
		$mypage->params['id_types']=(int)$id_types;
		$mypage->modified['id_types']=true;
		
		$type=MyPage::getMypageType((int)$id_types);
		if (!is_array($type)) return new MyError(MYERROR_EINVAL, tra("mypage type unaivalable"));
		$classname=MyPage::getTypeClassName($type['name']);
		if ($classname !== NULL)
			$mypage->typeclass=call_user_func(array($classname, 'newInstance_new'), $mypage);
		
		return $mypage;
	}
	
	/*static*/
	function getMyPage_byId($id, $id_users) {
		$mypage=new MyPage($id, $id_users);
		$mypage->checkout();
		if ($mypage->lasterror) return $mypage->lasterror;
		
		$typeclass=$mypage->getTypeClass();
		if (is_myerror($typeclass)) return $typeclass;

		return $mypage;
	}

	/*static*/
	function getMyPage_byName($name, $id_users) {
		global $tikilib;

		$res=$tikilib->query("SELECT `id` FROM tiki_mypage WHERE `name`=?", array($name));
		if ($line = $res->fetchRow()) {
			$mypage=new MyPage($line['id'], $id_users);
			$mypage->checkout();
			if ($mypage->lasterror) return $mypage->lasterror;

			$typeclass=$mypage->getTypeClass();
			if (is_myerror($typeclass)) return $typeclass;

			return $mypage;

		} else {
			return new MyError(MYERROR_ENOENT, "mypage '$name' not found");
		}
	}

	/*static*/
	function getMyPage_clone($mypage_src, $id_users) {
		$id_types=$mypage_src->getParam('id_types');
		$mypage_dst=new MyPage(NULL, $id_users);
		$mypage_dst->params['id_users']=(int)$id_users;
		$mypage_dst->params['id_types']=$id_types;
		$mypage_dst->modified['id_types']=true;
		
		$type=MyPage::getMypageType((int)$id_types);
		if (!is_array($type)) return new MyError(MYERROR_EINVAL, tra("mypage type unaivalable"));
		$classname=MyPage::getTypeClassName($type['name']);
		if (is_myerror($classname)) return $classname;
		$mypage_dst->typeclass=call_user_func(array($classname, 'newInstance_clone'), $mypage_dst, $mypage_src->getTypeClass());
		if (is_myerror($mypage_dst->typeclass)) return $mypage_dst->typeclass;

		/* copy mypage params */
		$copys=array('width', 'height', 'bgcolor', 'description', 'categories', 'winbgcolor', 'bgimage', 'winbgimage',
					 'bgtype', 'winbgtype');
		foreach($copys as $copy) $mypage_dst->setParam($copy, $mypage_src->getParam($copy));
		//$mypage_dst->commit();
		
		foreach($mypage_src->windows as $window_src) {
			$window_dst=MyPageWindow::newInstance_clone($mypage_dst, --$mypage_dst->lastid, $window_src);
			$mypage_dst->windows[$mypage_dst->lastid]=$window_dst;
		}
		$mypage_dst->clonning=false;
		return $mypage_dst;
	}

	function getPerm($for) {
		global $tiki_p_admin;
		switch ($for) {
		case 'edit':
			if ($tiki_p_admin == 'y') return true;
			if ($this->id_users <= 0) return false;
			return ((int)$this->id_users == (int)$this->params['id_users']);
		case 'view':
			return true;
		}
		return false;
	}

	function getWindows() {
		return $this->windows;
	}
	
	function getWindow($id) {
		if (!isset($this->windows[$id])) return new MyError(MYERROR_ENOENT, tra("window not found"));
		return $this->windows[$id];
	}

	function getWindowsOfType($type) {
		$windows=array();
		foreach ($this->windows as $window) {
			if ($window->getParam('contenttype') == $type)
				$windows[]=$window;
		}
		return $windows;
	}
	
	function newWindow($contenttype) {
		// actually, we only allow one component of one contenttype per mypage.
		$wins=$this->getWindowsOfType($contenttype);
		if (count($wins)) return new MyError(MYERROR_EINVAL, tra("You cannot have more than one windows of this type."));

		$win=MyPageWindow::newInstance_new($this, --$this->lastid, $contenttype);
		$this->windows[$this->lastid]=$win;
		return $win;
	}
	
	function _update_id_win($from, $to) {
		$this->windows[$to]=$this->windows[$from];
		unset($this->windows[$from]);
	}
	
	function destroy() {
		global $tikilib;
		
// 		if ($this->perms['tiki_p_edit_mypage'] != 'y' && !($this->perms['tiki_p_edit_own_mypage'] == 'y' && $this->id_users == $this->getParam('id_users'))) {
// 			$this->lasterror=new MyError(MYERROR_EACCESS, tra('You do not have permissions to delete the page'));
// 			return $this->lasterror;
// 		}

		if (!$this->getPerm('edit'))
			return $this->lasterror=new MyError(MYERROR_EACCESS, tra('You are not the owner of this page'));

		// we firstly destroy every windows that this mypage contain
		foreach($this->windows as $window) {
			$window->destroy();
		}
		
		$typeclass=$this->getTypeClass();
		if (!is_myerror($typeclass)) $typeclass->destroy();

		// finally, we destroy this mypage
		$tikilib->query("DELETE FROM tiki_mypage WHERE `id`=?",
						array($this->id));
	}
	
	function _destroyWindow($window) {
		global $tikilib;
		
		$id_win=0;
		if (is_object($window)) {
			$id_win=$window->id;
		} else if (is_int($window)) {
			$id_win=$window;
		}
		
		if (!isset($this->windows[$id_win])) return;
		
		if (!$this->getPerm('edit'))
			return $this->lasterror=new MyError(MYERROR_EACCESS, tra('You are not the owner of this page'));

		if ($id_win > 0) {
// 			if ($this->perms['tiki_p_edit_mypage'] != 'y' && !($this->perms['tiki_p_edit_own_mypage'] == 'y' && $this->id_users == $this->getParam('id_users'))) {
// 				$this->lasterror=new MyError(MYERROR_EACCESS, tra('You do not have permissions to delete this component'));
// 				return $this->lasterror;
// 			}
			$tikilib->query("DELETE FROM tiki_mypagewin WHERE `id`=? AND `id_mypage`=?",
							array($id_win, $this->id));
		}
		
		unset($this->windows[$id_win]);
	}
	
	/* static */
	function isNameFree($name) {
		global $tikilib;
		$r=$tikilib->getOne('SELECT COUNT(*) FROM tiki_mypage WHERE name=?', $name);
		return $r == 0 ? true : false;
	}

	/* static */
	function countPages($id_users, $type=NULL) {
		global $tikilib;
		global $tiki_p_admin;
		
		$query="SELECT count(*) ".
			"FROM tiki_mypage mp ".
			"LEFT JOIN tiki_mypage_types mpt ON mp.id_types = mpt.id WHERE (1=1)";
		$r=array();


		if (($id_users!=-1) || ($tiki_p_admin != 'y')) {
			$query.=" AND `id_users`=?";
			$r[]=(int)$id_users;
		}

		if ($type !== NULL) {
			$query.=" AND mpt.name=?";
			$r[]=$type;
		}

		$res=$tikilib->query($query, $r, $limit, $offset);

		return $tikilib->getOne($query, $r);
	}
	
	/* static */
	function listPages($id_users, $type=NULL, $offset=-1, $limit=-1, $sort_mode='mp.name') {
		global $tikilib;
		global $tiki_p_admin;

		$pages=array();

		$query="SELECT mp.*, ".
			"mpt.name as type_name, ".
			"mpt.description as type_description, ".
			"mpt.section as type_section, ".
			"mpt.permissions as type_permissions ".
			"FROM tiki_mypage mp ".
			"LEFT JOIN tiki_mypage_types mpt ON mp.id_types = mpt.id WHERE (1=1)";
		$r=array();

		if (($id_users!=-1) || ($tiki_p_admin != 'y')) {
			$query.=" AND `id_users`=?";
			$r[]=(int)$id_users;
		}

		if ($type !== NULL) {
			$query.=" AND mpt.name=?";
			$r[]=$type;
		}

		$sortstr='';
		if (is_array($sort_mode)) {
			foreach($sort_mode as $k => $v) $sort_mode[$k]=$tikilib->convert_sortmode($v);
			$sortstr=implode(', ', $sort_mode);
		} else $sortstr=$sort_mode;

		$query.=" ORDER BY ".$sortstr;
		$res=$tikilib->query($query, $r, $limit, $offset);

		while ($line = $res->fetchRow()) {
// 			$line['perms'] = $tikilib->get_perm_object($line['id'], 'mypage', '', false);
			$pages[]=$line;
		}

		return $pages;
	}
	
	function setParams($id_mypage, $vals) {
		foreach($vals as $k=>$v) {
			$err=$this->setParam($k, $v);
			if ($err) return $err;
		}
	}

	function checkParam($param, $value, $type=NULL) {
		switch($param) {
		case 'name':
			if (strlen($value) <= 0) return tra('Name is empty');
			break;
		}

		if ($this) {
			$typeclass=$this->getTypeClass();
			if (!is_myerror($typeclass) && is_callable(array($typeclass, 'checkParam')))
				return $typeclass->checkParam($param, $value);
		} else if (!empty($type)) {
			$tcn=MyPage::getTypeClassName($type);
			if (!is_myerror($tcn) && is_callable(array($tcn, 'checkParam'))) {
				return call_user_func(array($tcn, 'checkParam'), $param, $value);
			}
		}
		return false;
	}

	function setParam($param, $value) {
		if (!$this->getPerm('edit'))
			return $this->lasterror=new MyError(MYERROR_EACCESS, tra('You are not the owner of this page'));

		$allowed=array('width', 'height', 'wintitlecolor', 'wintextcolor', 'winbgcolor',
					   'name', 'description', 'bgcolor', 'categories', 'bgimage', 'winbgimage',
					   'bgtype', 'winbgtype');

		if ($param == 'categories') {
			if (is_null($value)) $value=array();
			else if (!is_array($value)) {
				if (empty($value)) $value=array();
				else $value=array($value);
			}
		}

		$err=$this->checkParam($param, $value);
		if ($err) return $this->lasterror=$err;

		$typeclass=$this->getTypeClass();
		if (in_array($param, $allowed)) {
			// TODO: verify permissions when changing id_users or id_types !
			$this->params[$param]=$value;
			$this->modified[$param]=true;
			if (!is_myerror($typeclass)) $typeclass->mypage_setParam($param, $value);
		} else {
			return $this->lasterror=new MyError(MYERROR_EINVAL, tra("Parameter not found :").$param);
		}
	}
	
	function _getcateg() {
		global $categlib; include_once ('lib/categories/categlib.php');

		if (array_key_exists('categories', $this->params)) return $this->params['categories'];
 	    $cat=$categlib->get_object_categories('mypage', $this->id, -1);
		return $cat;
	}

	function getParam($param, $default=NULL) {
		if ($param=='categories') {
			if (array_key_exists($param['categories']))
				return $param['categories'];
			else
				return $param['categories']=$this->_getcateg();
		} else {
			if (!isset($this->params[$param]))
				return $default;
			else
				return $this->params[$param];
		}
	}
	
	function checkout() {
		global $tikilib;
		
		$this->windows=array();
		
		if (!is_null($this->id)) {
			$res=$tikilib->query("SELECT * FROM tiki_mypage WHERE `id`=?", array($this->id));
			if ($line = $res->fetchRow()) {
				$this->params=$line;
				$this->modified=array();
			} else { // bad... no mypage found
				$this->id=0;
				return $this->lasterror=new MyError(MYERROR_ENOENT, "MyPage not found");
			}
			$this->perms = $tikilib->get_perm_object($this->id, 'mypage', '', false);
// 			if ($this->perms['tiki_p_view_mypage'] != 'y' && !($this->perms['tiki_p_edit_own_mypage'] == 'y' && $this->id_users == $this->getParam('id_users'))) {
// 				return $this->lasterror=new MyError(MYERROR_EACCESS, tra('You do not have permissions to view the page'));
// 			}
			
			$res=$tikilib->query("SELECT * FROM tiki_mypagewin WHERE `id_mypage`=?", array($this->id));
			while ($line = $res->fetchRow()) {
				$instance=MyPageWindow::newInstance_load($this, $line);
				if (is_myerror($instance)) return $instance;
				$this->windows[$line['id']]=$instance;
			}
		}
	}
	
	function commit() {
		global $tikilib, $tiki_p_edit_mypage, $tiki_p_edit_own_mypage;
		
		if (!$this->getPerm('edit'))
			return $this->lasterror=new MyError(MYERROR_EACCESS, tra('You are not the owner of this page'));

		if (is_null($this->id)) {
// 			if ($tiki_p_edit_mypage != 'y' && $tiki_p_edit_own_mypage != 'y') {
// 				return $this->lasterror=new MyError(MYERROR_EACCESS, tra('You do not have permissions to edit the page'));;
// 			}
			
			$this->params['created']=$tikilib->now;
			$this->modified['created']=1;

			// verify that we have a category if mandatory
			global $prefs;
			$categories = $this->getParam('categories');
			if (($prefs['feature_mypage_mandatory_category'] > 0) && (count($categories) == 0)) {
				return $this->lasterror = new MyError(MYERROR_EINVAL, tra('A category is mandatory'));
			}

			// create a new mypage id
			
			$res=$tikilib->query("INSERT INTO tiki_mypage (`id_users`) values (?)",
								 array($this->id_users));
			if (!$res) return $this->lasterror = new MyError(MYERROR_EIO, 'commit failed');
			
			$id=$tikilib->getOne("SELECT LAST_INSERT_ID()");
			if (!$id) return $this->lasterror = new MyError(MYERROR_EIO, 'commit error');
			
			$this->id=$id;
			
			$typeclass=$this->getTypeClass();
			if (!is_myerror($typeclass)) $typeclass->create();
			else return $this->lasterror = new MyError(MYERROR_EIO, "commit failed: $typeclass");

			// now run again for update ;)
			$res=$this->commit();
			if (is_myerror($res)) {
				$tikilib->query("DELETE FROM tiki_mypage WHERE `id`=?",
								array($this->id));
				$this->id=NULL;
				return $res;
			}
			return $res;
			
		} else {
			
			if (count($this->modified) > 0) {
				if (isset($this->modified['name'])) {
					$c=$tikilib->getOne('SELECT COUNT(*) FROM tiki_mypage WHERE `name`=? AND `id`!=?',
										array($this->params['name'], $this->id));
					if ($c != 0)
						return $this->lasterror=new MyError(MYERROR_EINVAL, tra(sprintf('Name "%s" already exists', $this->params['name'])));
				}
				$this->params['modified']=$tikilib->now;
				$this->modified['modified']=1;

				$l=array();
				$r=array();
				foreach($this->modified as $k => $v) {
					if ($k=='categories') continue; // categories is handled separatly
					$l[]="`$k`=?";
					$r[]=$this->params[$k];
				}

				global $prefs;
				$categories = $this->getParam('categories');
				if ($prefs['feature_mypage_mandatory_category'] > 0 && count($categories) == 0) {
					return $this->lasterror = new MyError(MYERROR_EINVAL, tra('A category is mandatory'));
				}

				$query="UPDATE tiki_mypage SET ".implode(',', $l)." WHERE `id`=?";
				$r[]=$this->id;
				
				$res=$tikilib->query($query, $r);

				if ($this->modified['categories'] || $this->modified['name'] || $this->modified['description']) {
					global $categlib; include_once ('lib/categories/categlib.php');
					foreach($this->params['categories'] as $categid) {
						$categlib->update_object_categories($categid, $this->id, 'mypage',
															$this->params['description'], $this->params['name'],
															"tiki-mypage.php?id_mypage=".$this->id);
					}
				}

			}

			$typeclass=$this->getTypeClass();
			if (!is_myerror($typeclass)) $typeclass->commit();
			$this->modified=array();

			foreach($this->windows as $window) {
				$window->commit();
			}
		}
	}
	
	/*
	 * increment viewed count
	 */
	function viewed() {
		global $tikilib;
		if ($this->id > 0) {
			$this->params['viewed']++;
			$tikilib->query('UPDATE tiki_mypage SET `viewed`=? WHERE `id`=?',
							array($this->params['viewed'], $this->id));
		}
	}

	function getJSCode($editable=false) {
		$js="/* windows creation */\n";
		foreach($this->windows as $win) {
			$js.=$win->getJSCode($editable);
		}
		$js.="/* end of windows creation */\n";
		return $js;
	}

	/*
	 * one day, i'll make a libcomponents...
	 *
	 * if called by the static way, it will return every available component
	 * if called by the instance way, it will return every available component for this type of mypage
	 */
	/* static or not static ! */
	function getAvailableComponents() {
		$r=array();
		$d=opendir("components");
		if ($d === FALSE) return $r;

		if (isset($this)) $cft=$this->getComponentsFromTypes();
		else $cft=NULL;

		while (($file = readdir($d)) !== false) {
			if (preg_match('/^comp-[a-zA-Z0-9_-]+\.php$/', $file)) {
				$compname=substr($file, 5, -4);
				if ($cft !== NULL) {
					foreach($cft as $l) {
						if ($l['compname'] == $compname)
							$r[]=$compname;
					}
				} else $r[]=$compname;
			}
        }
		return $r;
	}

	function getComponentsFromTypes() {
		global $tikilib;
		$query="select c.compname, c.mincount, c.maxcount ".
			" FROM tiki_mypage_types t ".
			" LEFT JOIN tiki_mypage_types_components c ON c.id_mypage_types=t.id ".
			" WHERE t.id=?";
		$result=$tikilib->query($query, array($this->getParam('id_types')));
		$lines=array();
		while ($line=$result->fetchRow()) {
			$lines[]=$line;
		}
		return $lines;
	}

	/* static */
	function countMypageTypes() {
		global $tikilib;
		
		$pages=array();
		return $tikilib->getOne("SELECT COUNT(*) FROM tiki_mypage_types");
	}
	
	/* static */
	function listMypageTypes($offset=-1, $limit=-1) {
		global $tikilib;

		$lines=array();
		$res=$tikilib->query("SELECT ".
							 " mpt.id as id, ".
							 " mpt.name as name, ".
							 " mpt.description as description, ".
							 " mpt.section as section, ".
							 " mpt.permissions as permissions, ".
							 " mpt.def_width as def_width, ".
							 " mpt.def_height as def_height, ".
							 " mpt.fix_dimensions as fix_dimensions, ".
							 " mpt.def_bgcolor as def_bgcolor, ".
							 " mpt.fix_bgcolor as fix_bgcolor, ".
							 " mpt.templateuser as templateuser, ".
							 " mptc.compname as compname, ".
							 " mptc.mincount as mincount, ".
							 " mptc.maxcount as maxcount ".
							 "FROM tiki_mypage_types as mpt ".
							 "LEFT JOIN tiki_mypage_types_components as mptc ".
							 "ON mptc.id_mypage_types = mpt.id ".
							 "ORDER BY mpt.id ",
							 array(), $limit, $offset);
		$lastid=0;
		$lastline=NULL;
		while ($line = $res->fetchRow()) {
			if ($line['id'] != $lastid) {
				if ($lastline !== NULL) $lines[]=$lastline;
				$lastid=$line['id'];
				$lastline=array('id' => $line['id'],
								'name' => $line['name'],
								'description' => $line['description'],
								'section' => $line['section'],
								'permissions' => $line['permissions'],
								'def_width' => $line['def_width'],
								'def_height' => $line['def_height'],
								'fix_dimensions' => $line['fix_dimensions'],
								'def_bgcolor' => $line['def_bgcolor'],
								'fix_bgcolor' => $line['fix_bgcolor'],
								'templateuser' => $line['templateuser'],
								'components' => array());
			}
			$lastline['components'][]=array('compname' => $line['compname'],
											'mincount' => $line['mincount'],
											'maxcount' => $line['maxcount']);
		}
		if ($lastline !== NULL) $lines[]=$lastline;
		
		return $lines;
	}

	/* static */
	function getMypageType($id) {
		global $tikilib;

		$lines=array();
		$res=$tikilib->query("SELECT ".
							 " mpt.id as id, ".
							 " mpt.name as name, ".
							 " mpt.description as description, ".
							 " mpt.section as section, ".
							 " mpt.permissions as permissions, ".
							 " mpt.def_width as def_width, ".
							 " mpt.def_height as def_height, ".
							 " mpt.fix_dimensions as fix_dimensions, ".
							 " mpt.def_bgcolor as def_bgcolor, ".
							 " mpt.fix_bgcolor as fix_bgcolor, ".
							 " mpt.templateuser as templateuser, ".
							 " mptc.compname as compname, ".
							 " mptc.mincount as mincount, ".
							 " mptc.maxcount as maxcount ".
							 "FROM tiki_mypage_types as mpt ".
							 "LEFT JOIN tiki_mypage_types_components as mptc ".
							 "ON mptc.id_mypage_types = mpt.id ".
							 "WHERE mpt.id=? ".
							 "ORDER BY mpt.id ",
							 array((int)$id));
		$lastid=0;
		$lastline=NULL;
		while ($line = $res->fetchRow()) {
			if ($line['id'] != $lastid) {
				if ($lastline !== NULL) $lines[]=$lastline;
				$lastid=$line['id'];
				$lastline=array('id' => $line['id'],
								'name' => $line['name'],
								'description' => $line['description'],
								'section' => $line['section'],
								'permissions' => $line['permissions'],
								'def_width' => $line['def_width'],
								'def_height' => $line['def_height'],
								'fix_dimensions' => $line['fix_dimensions'],
								'def_bgcolor' => $line['def_bgcolor'],
								'fix_bgcolor' => $line['fix_bgcolor'],
								'templateuser' => $line['templateuser'],
								'components' => array());
			}
			$lastline['components'][]=array('compname' => $line['compname'],
											'mincount' => $line['mincount'],
											'maxcount' => $line['maxcount']);
		}
		if ($lastline !== NULL) $lines[]=$lastline;
		
		if (count($lines) == 1) return $lines[0];
		else return NULL;
	}

	/* static */
	function deleteMypageType($id) {
		global $tikilib;

		$res=$tikilib->query("DELETE FROM tiki_mypage_types_components ".
							 "WHERE id_mypage_types=?", array((int)$id));

		$res=$tikilib->query("DELETE FROM tiki_mypage_types ".
							 "WHERE id=?", array((int)$id));
	}
	
	/* static */
	function createMypageType() {
		global $tikilib;

		$res=$tikilib->query("INSERT INTO tiki_mypage_types (created,modified) VALUES (?,?)",
							 array($tikilib->now, $tikilib->now));
		return $tikilib->getOne("SELECT LAST_INSERT_ID()");
	}

	/* static */
	function updateMypageType($id, $vals) {
		global $tikilib;

		$cols=array("name", "description", "section", "permissions",
					"def_width", "def_height", "fix_dimensions",
					"def_bgcolor", "fix_bgcolor", "modified", "templateuser");

		$vals['modified']=$tikilib->now;
		$tvals=$vals;
		// we remove unauthorized cols
		foreach($tvals as $k => $v)
			if (!in_array($k, $cols)) unset($tvals[$k]);

		if (array_key_exists('def_width', $tvals) && empty($tvals['def_width'])) $tvals['def_width']=NULL;
		if (array_key_exists('def_height', $tvals) && empty($tvals['def_height'])) $tvals['def_height']=NULL;
		if (array_key_exists('def_bgcolor', $tvals) && empty($tvals['def_bgcolor'])) $tvals['def_bgcolor']=NULL;

		if (count($tvals) > 0) {
			$l=array();
			$r=array();
			foreach($tvals as $k => $v) {
				$l[]="`$k`=?";
				$r[]=$v;
			}
			
			$query="UPDATE tiki_mypage_types SET ".implode(', ', $l)." WHERE `id`=?";
			$r[]=(int)$id;
			
			$res=$tikilib->query($query, $r);
		}

		if (isset($vals['components'])) {
			$res=$tikilib->query("DELETE FROM tiki_mypage_types_components ".
								 "WHERE id_mypage_types=?", array((int)$id));
			foreach($vals['components'] as $component) {
				$res=$tikilib->query("INSERT INTO tiki_mypage_types_components".
									 " (`id_mypage_types`, `compname`)".
									 " VALUES (?,?)", array((int)$id, $component));
			}
		}
	}

	/* static */
	function getTypeHTMLConfig($type=NULL) {
		if (isset($this)) {
			$typeclass=$this->getTypeClass();
			return (is_myerror($typeclass) ? NULL : $typeclass->getHTMLConfig());
		} else {
			$classname=MyPage::getTypeClassName($type);
			if (is_myerror($classname)) return NULL;
			if (is_callable(array($classname, 'getHTMLConfig')))
				return call_user_func(array($classname, 'getHTMLConfig'));
			else
				return NULL;
		}
	}

	/* static */
	function getTypeClassName($type) {
		if (!preg_match('/^[a-zA-Z0-9_-]+$/', $type))
			return new MyError(MYERROR_EINVAL, tra("bad type name"));
		if (file_exists("lib/mypage/types/type-".$type.".php")) {
			require_once("lib/mypage/types/type-".$type.".php");
			$classname="Mypagetype_".$type;
			return $classname;
		}
		return new MyError(MYERROR_ENOENT, tra("type class not found"));
	}

	function getTypeClass() {
		if ($this->typeclass) return $this->typeclass;
		$type=MyPage::getMypageType((int)$this->getParam('id_types'));
		if (is_myerror($type)) return $type;
		$classname=MyPage::getTypeClassName($type['name']);
		if (is_myerror($classname)) return $classname;
		$this->typeclass=call_user_func(array($classname, 'newInstance_load'), $this);
		if (is_myerror($this->typeclass)) return $this->typeclass;

		return $this->typeclass;
	}
}

class MyPageWindow {
	var $mypage;
	var $id;
	var $params;
	var $modified;
	var $comp;
	var $lasterror;

	/*
	 * this constructor may be called only by the MyPage class
	 * you should not create a new instance of this object directly
	 */
	function MyPageWindow($mypage, $id) {
		$this->mypage=$mypage;
		$this->id=$id;
		$this->params=$line;
		$this->modified=array();
		$this->comp=NULL;
	}

	/*
	 * construct a new instance for creating new window, should be called only by MyPage
	 */
	/* static */
	function newInstance_new($mypage, $id, $contenttype) {
		$win=new MyPageWindow($mypage, $id);
		$win->params['contenttype']=$contenttype;
		$win->modified['contenttype']=true;

			// some default values...
		$win->setParam('top',0);
		$win->setParam('left',0);
		$win->setParam('width',300);
		$win->setParam('height',300);

		$classname=MyPageWindow::getComponentClass($contenttype);
		if (is_myerror($classname)) return $classname;
		$win->comp=call_user_func(array($classname, 'newInstance_new'), $win);
		if (is_myerror($win->comp)) return $win->comp;

		return $win;
	}

	/*
	 * construct a new instance for an existing window, should be called only by MyPage
	 */
	/* static */
	function newInstance_load($mypage, $line) {
		$win=new MyPageWindow($mypage, $line['id']);
		if (is_myerror($win)) return $win;

		$win->params['contenttype']=$line['contenttype'];
		$win->modified['contenttype']=true;

		$allowed=array('title', 'inbody', 'modal', 'left', 'top', 'width', 'height', 'config', 'content');
		foreach($line as $k => $v) {
			if (in_array($k, $allowed))
				$win->params[$k]=$v;
		}
		return $win;
	}

	/*
	 * construct a new instance for clonning an existing window, should be called only by MyPage
	 */
	/* static */
	function newInstance_clone($mypage, $id, $win_src) {
		$win=new MyPageWindow($mypage, $id);
		if (is_myerror($win)) return $win;

		$win->params['contenttype']=$win_src->getParam('contenttype');
		$win->modified['contenttype']=true;

		$allowed=array('title', 'inbody', 'modal', 'left', 'top', 'width', 'height', 'config', 'content');
		foreach($allowed as $k) {
				$win->setParam($k, $win_src->getParam($k));
		}

		$comp_src=$win_src->getComponent();
		if (is_myerror($comp_src)) return $comp_src;

		$classname=MyPageWindow::getComponentClass($win_src->getParam('contenttype'));
		if (is_myerror($classname)) return $classname;

		$win->comp=call_user_func(array($classname, 'newInstance_clone'), $win, $comp_src);
		if (is_myerror($win->comp)) return $win->comp;

		return $win;
	}

	function destroy() {
		if (!$this->getPerm('edit'))
			return $this->lasterror=new MyError(MYERROR_EACCESS, tra('You are not the owner of this page'));

		$comp=$this->getComponent();
		if (is_myerror($comp)) return $comp;

		return $this->mypage->_destroyWindow($this);
	}


	function getPerm($for) {
		switch ($for) {
		case 'edit':
			return $this->mypage->getPerm('edit');
		case 'view':
			return $this->mypage->getPerm('edit');
		}
		return false;
	}

	
	function commit() {
		global $tikilib;
		/*		
		if ($this->mypage->perms['tiki_p_view_mypage'] != 'y' && !($this->mypage->perms['tiki_p_edit_own_mypage'] == 'y' && $this->mypage->id_users == $this->mypage->getParam('id_users'))) {
			$this->lasterror=tra('You do not have permissions to edit the component');
			return $this->lasterror;
		}
		*/
		
		if (!$this->getPerm('edit'))
			return $this->lasterror=new MyError(MYERROR_EACCESS, tra('You are not the owner of this page'));


		if ($this->id < 0) {
			// create a new mypagewin id
			
			$this->params['created']=$tikilib->now;
			$this->modified['created']=1;

			$res=$tikilib->query("INSERT INTO tiki_mypagewin (`id_mypage`) values (?)",
								 array($this->mypage->id));
			if (!$res) return new MyError(MYERROR_EIO, 'commit failed');
			
			$id=$tikilib->getOne("SELECT LAST_INSERT_ID()");
			if (!$id) return new MyError(MYERROR_EIO, 'commit failed');
			
			$oldid=$this->id;
			$this->id=$id;
			$this->mypage->_update_id_win($oldid, $this->id);
			
			// now run again for update ;)
			$r=$this->commit();
			if (is_myerror($r)) {
				$tikilib->query("DELETE FROM tiki_mypagewin WHERE `id`=?", array($this->id));
				$this->id=$oldid;
				$this->mypage->_update_id_win($this->id, $oldid);
				return $r;
			}
			$comp=$this->getComponent();
			if (!is_myerror($comp) && is_callable(array($comp, 'create'))) $comp->create();

		} else {
			
			if (count($this->modified) > 0) {
				$this->params['modified']=$tikilib->now;
				$this->modified['modified']=1;

				$l=array();
				$r=array();
				foreach($this->modified as $k => $v) {
					$l[]="`$k`=?";
					$r[]=$this->params[$k];
				}
				
				$query="UPDATE tiki_mypagewin SET ".implode(',', $l)." WHERE `id`=?";
				$r[]=$this->id;
				
				$res=$tikilib->query($query, $r);
				
				$this->modified=array();
			}

			$comp=$this->getComponent();
			if (!is_myerror($comp) && is_callable(array($comp, 'commit'))) $comp->commit();
		}
	}
	
	function setParam($param, $value) {
		if (!$this->getPerm('edit'))
			return $this->lasterror=new MyError(MYERROR_EACCESS, tra('You are not the owner of this page'));
		$this->params[$param]=$value;
		$this->modified[$param]=true;
	}
	
	function getParam($param) {
		return $this->params[$param];
	}
	
	/*
	 * $contenttype: must be 'iframe' or 'wiki'
	 */
	function setContentType($contenttype) {
		return $this->setParam('contenttype', $contenttype);
	}
	
	function setContent($content) {
		return $this->setParam('content', $content);
	}
	
	function setConfig($config) {
		return $this->setParam('config', $config);
	}
	
	function setPosition($left, $top) {
		$err=$this->setParam('left', (int)$left);
		if (is_myerror($err)) return $err;
		return $this->setParam('top', (int)$top);
	}

	function setSize($width, $height) {
		$err=$this->setParam('width', (int)$width);
		if (is_myerror($err)) return $err;
		return $this->setParam('height', (int)$height);
	}

	function setRect($left, $top, $width, $height) {
		$err=$this->setPosition($left, $top);
		if (is_myerror($err)) return $err;
		return $this->setSize($width, $height);
	}

	function setTitle($title) {
		return $this->setParam('title', $title);
	}

	/* static */
	function isComponentConfigurable($compname=NULL) {
		if (!empty($compname)) {
			$classname=MyPageWindow::getComponentClass($compname);
			if (!is_myerror($classname) && is_callable(array($classname, 'isConfigurable')))
				return call_user_func(array($classname, 'isConfigurable'));
		}
		return false;
	}

	/* static or not static */
	function getComponentConfigureDiv($compname=NULL) {
		if (isset($this)) { // we are not static
			$comp=$this->getComponent();
			if (!is_myerror($comp)) return $comp->getConfigureDiv();
			return NULL;
		} else if (!empty($compname)) { // we are static
			$classname=MyPageWindow::getComponentClass($compname);
			if (!is_myerror($classname) && is_callable(array($classname, 'getConfigureDiv')))
				return call_user_func(array($classname, 'getConfigureDiv'));
		}
		return NULL;
	}
	
	/* static */
	function getComponentClass($compname) {
		if (!preg_match('/^[a-zA-Z0-9_-]+$/', $compname))
			return new MyError(MYERROR_EINVAL, tra("component name is invalid"));
		if (file_exists("components/comp-".$compname.".php")) {
			require_once("components/comp-".$compname.".php");
			$classname="Comp_".$compname;
			return $classname;
		}
		return new MyError(MYERROR_ENOENT, tra("component class not found"));
	}

	function getComponent() {
		if ($this->comp) return $this->comp;
		$classname=MyPageWindow::getComponentClass($this->params['contenttype']);
		if (is_myerror($classname)) return $classname;

		$this->comp=call_user_func(array($classname, 'newInstance_load'), $this);
		return $this->comp;
	}

	function getJSCode($editable=false) {
		global $tikilib;

		$comp=$this->getComponent();
		if (is_myerror($comp)) {
			$this->lasterror=$comp;
			return 'alert("'.addslashes($this->lasterror->getErrorString()).'");';
		}

		if (is_callable(array($comp, 'getPerm')) && !$comp->getPerm('view')) {
			$this->lasterror=new MyError(MYERROR_EACCESS, tra("You do not have permission to view this part of content"));
			return 'alert("'.addslashes($this->lasterror->getErrorString()).'");';
		}

		$v="tikimypagewin[".$this->id."]";

		$winparams=array('left'	     => (int)$this->params['left'],
						 'top'	     => (int)$this->params['top'],
						 'width'	 => (int)$this->params['width'],
						 'height'	 => (int)$this->params['height'],
						 'title'	 => $this->params['title'],
						 'position'  => false,
						 'theme'	 => 'mypage',
						 'container' => 'mypage',
						 'buttons'   => array()
						 );

		/*
		$winparams['effects'] = array('show' => array('options' => array('duration' => 0, 'opacity' => array('1','1'))),
									  'close' => array('options' => array('duration' => 0, 'opacity' => array('0','0'))),
									  'hide' => array('options' => array('duration' => 0, 'opacity' => array('0','0'))));
		*/

		if (is_callable(array($comp, 'isResizeable'))) {
			if (!$comp->isResizeable()) {
				$winparams['resizable']  = false;
				$winparams['buttons']['maximize'] = false;
				$winparams['buttons']['minimize'] = false;
			}
		}

		if (is_callable(array($comp, 'isResizeable'))) {
			if (!$comp->isCloseable()) $winparams['buttons']['close'] = false;
		}

		if (!$editable) {
			$winparams['theme']	  = 'mypage_view';
			$winparams['resizable']  = false;
			$winparams['draggable']  = false;
			$winparams['buttons']	 = array('close'	=> false,
											 'minimize' => false,
											 'maximize' => false);
		} else {
			if (is_callable(array($comp, 'isConfigurable')) && $comp->isConfigurable()) {
				$winparams['buttons']['menu'] = true;
			}
		}
	
		if ($this->params['contenttype'] == 'iframe') {
			$winparams['type']="iframe";
			$winparams['url']=$this->params['config'];
		}
		
		if (is_callable(array($comp, 'customizeWindooOptions'))) {
			$winparams=$comp->customizeWindooOptions($winparams);
		}

		$js = "mypagewin_create(".$this->mypage->id.", ".$this->id.", ".phptojsarray($this->params['contenttype']).", ".
			phptojsarray($winparams);

		if ($this->params['contenttype'] != 'iframe') $js.=", ".phptojsarray($comp->getHTMLContent());
		else $js.=", ";

		$js.=");";

		if (is_callable(array($comp, 'getOnOpenJSCode'))) {
			$js.=$comp->getOnOpenJSCode().";\n";
		}
		
		return $js;
	}
}

/* For the emacs weenies in the crowd.
Local Variables:
   tab-width: 4
   c-basic-offset: 4
End:
*/

?>
