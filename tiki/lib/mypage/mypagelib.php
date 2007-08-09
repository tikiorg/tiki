<?php

// $Header: /cvsroot/tikiwiki/tiki/lib/mypage/mypagelib.php,v 1.19 2007-08-09 18:08:55 niclone Exp $
// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

/* checks about features */
if ($feature_ajax != 'y') {
	die("feature ajax is required for mypage");
}

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
	var $perms;

	function MyPage($id=NULL, $id_users) {
		$this->id=$id;
		$this->id_users=$id_users; //the viewer
		$this->windows=array();
		$this->lastid=0;
		$this->params=array();
		$this->modified=array();
		$this->checkout();
	}
	
	function getWindows() {
	}
	
	function getWindow($id) {
		return $this->windows[$id];
	}
	
	function newWindow() {
		$win=new MyPageWindow($this, --$this->lastid, array());
		$this->windows[$this->lastid]=$win;
		return $win;
	}
	
	function _update_id_win($from, $to) {
		$this->windows[$to]=$this->windows[$from];
		unset($this->windows[$from]);
	}
	
	function destroy() {
		global $tikilib;
		
		if ($this->perms['tiki_p_edit_mypage'] != 'y' && !($this->perms['tiki_p_edit_own_mypage'] == 'y' && $this->id_users == $this->getParam('id_users'))) {
			return "alert(tra('You do not have permissions to delete the page'))";
		}
		
		// we firstly destroy every windows that this mypage contain
		foreach($this->windows as $window) {
			$this->destroyWindow($window);
		}
		
		// finally, we destroy this mypage
		$tikilib->query("DELETE FROM tiki_mypage WHERE `id`=?",
						array($this->id));
	}
	
	function destroyWindow($window) {
		global $tikilib;
		
		$id_win=0;
		if (is_object($window)) {
			$id_win=$window->id;
		} else if (is_int($window)) {
			$id_win=$window;
		}
		
		if (!isset($this->windows[$id_win])) return;
		
		if ($id_win > 0) {
			if ($this->perms['tiki_p_edit_mypage'] != 'y' && !($this->perms['tiki_p_edit_own_mypage'] == 'y' && $this->id_users == $this->getParam('id_users'))) {
				return tra('You do not have permissions to delete this component');
			}
			$tikilib->query("DELETE FROM tiki_mypagewin WHERE `id`=? AND `id_mypage`=?",
							array($id_win, $this->id));
		}
		
		unset($this->windows[$id_win]);
	}
	
	/* static */
	function countPages($id_users) {
		global $tikilib;
		
		$pages=array();
		return $tikilib->getOne("SELECT COUNT(*) FROM tiki_mypage WHERE `id_users`=?",
								array((int)$id_users));
	}
	
	/* static */
	function listPages($id_users, $offset=-1, $limit=-1) {
		global $tikilib;

		$pages=array();
		$res=$tikilib->query("SELECT * FROM tiki_mypage WHERE `id_users`=?",
							 array((int)$id_users), $limit, $offset);
		while ($line = $res->fetchRow())
			$pages[]=$line;
		
		return $pages;
	}
	
	function setParam($param, $value) {
		$this->params[$param]=$value;
		$this->modified[$param]=true;
	}
	
	function getParam($param) {
		return $this->params[$param];
	}
	
	function checkout() {
		global $tikilib;
		
		$this->windows=array();
		
		if (!is_null($this->id)) {
			$res=$tikilib->query("SELECT * FROM tiki_mypage WHERE `id`=?", array($this->id));
			if ($line = $res->fetchRow()) {
				$this->params=$line;
			} else { // bad... no mypage found
				$this->id=0;
				return FALSE;
			}
			$this->perms = $tikilib->get_perm_object($this->id, 'mypage', false);
			if ($this->perms['tiki_p_view_mypage'] != 'y' && !($this->perms['tiki_p_edit_own_mypage'] == 'y' && $this->id_users == $this->getParam('id_users'))) {
				return "alert(tra('You do not have permissions to view the page'))";
			}
			
			$res=$tikilib->query("SELECT * FROM tiki_mypagewin WHERE `id_mypage`=?", array($this->id));
			while ($line = $res->fetchRow()) {
				$mypagewindow=new MyPageWindow($this, $line['id'], $line);
				if ($mypagewindow->perms['tiki_p_view_component'] == 'y' || $this->id_users == $this->getParam('id_users') || $this->perms['tiki_p_admin_mypage'] == 'y') {
					$this->windows[$line['id']] = $mypagewindow;
				}
			}
			
		}
	}
	
	function commit() {
		global $tikilib, $tiki_p_edit_mypage, $tiki_p_edit_own_mypage;
		
		if (is_null($this->id)) {
			
			if ($tiki_p_edit_mypage != 'y' && $tiki_p_edit_own_mypage != 'y') {
				return "alert(tra('You do not have permissions to edit the page'))";
			}
			
			// create a new mypage id
			
			$res=$tikilib->query("INSERT INTO tiki_mypage (`id_users`) values (?)",
								 array($this->id_users));
			if (!$res) return;
			
			$id=$tikilib->getOne("SELECT LAST_INSERT_ID()");
			if (!$id) return;
			
			$this->id=$id;
			
			// now run again for update ;)
			return $this->commit();
			
		} else {
			
			if ($this->perms['tiki_p_edit_mypage'] != 'y' && !($this->perms['tiki_p_edit_own_mypage'] == 'y' && $this->id_users == $this->getParam('id_users'))) {
				return "alert(tra('You do not have permissions to edit the page'))";
			}
			
			if (count($this->modified) > 0) {
				$l=array();
				$r=array();
				foreach($this->modified as $k => $v) {
					$l[]="`$k`=?";
					$r[]=$this->params[$k];
				}
				
				$query="UPDATE tiki_mypage SET ".implode(',', $l)." WHERE `id`=?";
				$r[]=$this->id;
				
				$res=$tikilib->query($query, $r);
				
				$this->modified=array();
			}
		}
	}
	
	function getJSCode() {
		$js="/* windows creation */\n";
		foreach($this->windows as $win) {
			$js.=$win->getJSCode();
		}
		$js.="/* end of windows creation */\n";
		return $js;
	}

	/*
	 * one day, i'll make a libcomponents...
	 */
	function getAvailableComponents() {
		return array('iframe', 'wiki');
	}

}

class MyPageWindow {
	var $mypage;
	var $id;
	var $params;
	var $modified;
	var $comp;
	var $perms;
	
	/*
	 * this constructor may be called only by the MyPage class
	 * you should not create a new instance of this object directly
	 */
	function MyPageWindow($mypage, $id, $line) {
		global $tikilib;
		$this->mypage=$mypage;
		$this->id=$id;
		$this->params=$line;
		$this->modified=array();
		$this->comp=NULL;
		
		if ($this->id < 0) {
			
			// some default values...
			if (!isset($this->params['top'])) $this->params['top']=0;
			if (!isset($this->params['left'])) $this->params['left']=0;
			if (!isset($this->params['width'])) $this->params['width']=100;
			if (!isset($this->params['height'])) $this->params['height']=100;
			
			foreach($this->params as $k => $v) {
				switch($k) {
				case 'title':
				case 'inbody':
				case 'modal':
				case 'left':
				case 'top':
				case 'width':
				case 'height':
				case 'contenttype':
				case 'content':
					$this->modified[$k]=true;
					break;
				}
			}
		} else {
			$this->perms = $tikilib->get_perm_object($this->id, 'component', false);
		}
	}
	
	function destroy() {

		$this->mypage->destroyWindow($this);
	}
	
	function commit() {
		global $tikilib;
		
		if ($this->mypage->perms['tiki_p_view_mypage'] != 'y' && !($this->mypage->perms['tiki_p_edit_own_mypage'] == 'y' && $this->mypage->id_users == $this->myspace->getParam('id_users'))) {
			return "alert(tra('You do not have permissions to edit the component'))";
		}
			
		if ($this->id < 0) {
			// create a new mypagewin id
			
			$res=$tikilib->query("INSERT INTO tiki_mypagewin (`id_mypage`) values (?)",
								 array($this->mypage->id));
			if (!$res) return;
			
			$id=$tikilib->getOne("SELECT LAST_INSERT_ID()");
			if (!$id) return;
			
			$oldid=$this->id;
			$this->id=$id;
			$this->mypage->_update_id_win($oldid, $this->id);
			
			// now run again for update ;)
			return $this->commit();
			
		} else {
			
			if (count($this->modified) > 0) {
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
		}
	}
	
	function setParam($param, $value) {
		$this->params[$param]=$value;
		$this->modified[$param]=true;
	}
	
	/*
	 * $contenttype: must be 'iframe' or 'wiki'
	 */
	function setContentType($contenttype) {
		$this->setParam('contenttype', $contenttype);
	}
	
	function setContent($content) {
		$this->setParam('content', $content);
	}
	
	function setPosition($left, $top) {
		$this->setParam('left', (int)$left);
		$this->setParam('top', (int)$top);
	}

	function setSize($width, $height) {
		$this->setParam('width', (int)$width);
		$this->setParam('height', (int)$height);
	}

	function setRect($left, $top, $width, $height) {
		$this->setPosition($left, $top);
		$this->setSize($width, $height);
	}

	function setTitle($title) {
		$this->setParam('title', $title);
	}

	function getComponent() {
		if ($this->comp) return $this->comp;
		if (file_exists("components/comp-".$this->params['contenttype'].".php")) {
			require_once("components/comp-".$this->params['contenttype'].".php");
			$classname="Comp_".$this->params['contenttype'];
			$this->comp=new $classname($this->params['content']);
			return $this->comp;
		}
		return NULL;
	}

	function getJSCode($editable=true) {
		global $tikilib;

		switch ($this->params['contenttype']) {
		case 'iframe':
			// don't do nothing here for the special iframe case
			break;

		default:
			$comp=$this->getComponent();
			if (!$comp) {
				return 'alert("Component not available: '.$this->params['contenttype'].'");';
			}

			$compperms = $comp->getPermObject();
			if (!isset($compperms['tiki_p_view_'.$this->params['contenttype']])
				|| $compperms['tiki_p_view_'.$this->params['contenttype']] != 'y') {

				return 'alert("You do not have permission to view this part of content");';
			}
			break;
		}

		$v="tikimypagewin[".$this->id."]";

		$winparams=array('left'	     => (int)$this->params['left'],
						 'top'	     => (int)$this->params['top'],
						 'width'	 => (int)$this->params['width'],
						 'height'	 => (int)$this->params['height'],
						 'title'	 => $this->params['title'],
						 'position'  => false,
						 'theme'	 => 'aero',
						 'container' => 'mypage',
						 );

		if (!$editable) {
			$winparams['theme']	  = 'nada';
			$winparams['resizable']  = false;
			$winparams['draggable']  = false;
			$winparams['buttons']	 = array('close'	=> false,
											 'minimize' => false,
											 'maximize' => false);
		}
	
		switch ($this->params['contenttype']) {
		case 'iframe':
			$winparams['type']="iframe";
			$winparams['url']=$this->params['content'];
			break;
		}
	
		$js =$v."=new Windoo(".phptojsarray($winparams).");\n";
		$js.=$v.".addEvent('onResizeComplete', function(){ state=$v.getState(); xajax_mypage_win_setrect(".$this->mypage->id.", ".$this->id.", state.outer); });\n";
		$js.=$v.".addEvent('onDragComplete', function(){ state=$v.getState(); xajax_mypage_win_setrect(".$this->mypage->id.", ".$this->id.", state.outer); });\n";
		$js.=$v.".addEvent('onBeforeClose', function(){ xajax_mypage_win_destroy(".$this->mypage->id.", ".$this->id."); return false; });\n";
	
	
		if ($this->params['contenttype'] != 'iframe') {
			$js.=$v.".setHTML(".phptojsarray($comp->getHTMLContent()).");\n";
		}
		
		$js.=$v.".show();\n";
		
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