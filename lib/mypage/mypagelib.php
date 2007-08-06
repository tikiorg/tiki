<?php

// $Header: /cvsroot/tikiwiki/tiki/lib/mypage/mypagelib.php,v 1.5 2007-08-06 19:16:14 niclone Exp $
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
function phptojsarray($array, $offset="") {
  if (is_array($array)) {
    $offset.="  ";
    $str="{\n".$offset."'COUNT' : ".count($array);
    foreach($array as $k => $v) {
      $str.=",\n".$offset."'$k' : ".phptojsarray($v, $offset);
    }
    $str.=" }";
  } else if (is_numeric($array)) {
    $str="".$array;
  } else if (is_string($array)) {
    $str="'".str_replace(array("\n", "\r", "</"), array("\\n", "\\r", "<'+'/"), addslashes($array))."'";
  } else if (is_null($array)) {
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
    var $windows;
    var $lastid;

    function MyPage($id=NULL) {
	$this->id=$id;
	$this->windows=array();
	$this->lastid=0;
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

    function checkout() {
	global $tikilib;

	$this->windows=array();

	if (!is_null($this->id)) {
	    $res=$tikilib->query("SELECT * FROM tiki_mypage WHERE `id`=?", array($this->id));
	    if ($line = $res->fetchRow()) {
		
	    } else { // bad... no mypage found
		$this->id=NULL;
		return FALSE;
	    }

	    $res=$tikilib->query("SELECT * FROM tiki_mypagewin WHERE `id_mypage`=?", array($this->id));
	    while ($line = $res->fetchRow()) {
		$this->windows[$line['id']]=new MyPageWindow($this, $line['id'], $line);
	    }

	}
    }

    function commit() {
	global $tikilib;

	if (is_null($this->id)) {
	    // create a new mypage id
	} else {
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
}

class MyPageWindow {
    var $mypage;
    var $id;
    var $params;
    var $modified;

    /*
     * this constructor may be called only by the MyPage class
     * you should not create a new instance of this object directly
     */
    function MyPageWindow($mypage, $id, $line) {
	$this->mypage=$mypage;
	$this->id=$id;
	$this->params=$line;
	$this->modified=array();

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
	}
    }

    function commit() {
	global $tikilib;

	if ($this->id < 0) {
	    // create a new mypagewin id
	    
	    $res=$tikilib->query("INSERT INTO tiki_mypagewin (`id_mypage`) values (?)", array($this->mypage->id));
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

    function getJSCode() {
	global $tikilib;

	// check perms
	switch ($this->params['contenttype']) {
	case 'iframe':
	    // don't do nothing here for the special iframe case
	    break;
	default:
	    if (file_exists("components/comp-".$this->params['contenttype'].".php")) {
			require_once("components/comp-".$this->params['contenttype'].".php");
			$classname="Comp_".$this->params['contenttype'];
			$comp=new $classname($this->params['content']);
			$compperms = $comp->get_perm_object();
			if (!isset($compperms['tiki_p_view_'.$this->params['contenttype']]) || $compperms['tiki_p_view_'.$this->params['contenttype']] != 'y') {
				return '';
			}
	    } else {
			return '';
		}
	    break;
	}

	$v="tikimypagewin[".$this->id."]";

	$winparams=array('left'      => (int)$this->params['left'],
			 'top'       => (int)$this->params['top'],
			 'width'     => (int)$this->params['width'],
			 'height'    => (int)$this->params['height'],
			 'title'     => $this->params['title'],
			 'position'  => false,
			 'container' => false,
			 );
	
	switch ($this->params['contenttype']) {
	case 'iframe':
	    $winparams['type']="iframe";
	    $winparams['url']=$this->params['content'];
	    break;
	}
	
	$js =$v."=new Windoo(".phptojsarray($winparams).");\n";
	$js.=$v.".addEvent('onResizeComplete', function(){ state=$v.getState(); xajax_mypage_win_setrect(".$this->mypage->id.", ".$this->id.", state.outer); });\n";
	$js.=$v.".addEvent('onDragComplete', function(){ state=$v.getState(); xajax_mypage_win_setrect(".$this->mypage->id.", ".$this->id.", state.outer); });\n";
	$js.=$v.".addEvent('onClose', function(){ xajax_mypage_win_destroy(".$this->mypage->id.", ".$this->id."); });\n";
	
	
	switch ($this->params['contenttype']) {
	    
	case 'iframe':
	    // don't do nothing here for the special iframe case
	    break;
	    
	default:
		$js.=$v.".setHTML(".phptojsarray($comp->getHTMLContent()).");\n";
	    break;
	}
	

	$js.=$v.".show();\n";

	return $js;
    }
}


?>