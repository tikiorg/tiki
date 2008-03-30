<?php

// $Id: /cvsroot/tikiwiki/tiki/tiki-mypages.php,v 1.26.2.2 2008-03-01 17:12:48 lphuberdeau Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

require_once ('tiki-setup.php');

if($prefs['feature_mypage'] != 'y') {
    $smarty->assign('msg', tra('This feature is disabled').': feature_mypage');
    $smarty->display('error.tpl');
    die;  
}

require_once ('lib/mypage/mypagelib.php');
require_once ('lib/mypage/mypagelib.php');
require_once ('lib/ajax/ajaxlib.php');

/*
 * TODO: one day, it may be possible to list anonymous mypages. So a tiki permission for anonymous would be welcome here
 */
if (!$user) {
	$smarty->assign('msg', tra("You are not logged in"));

	$smarty->display("error.tpl");
	die();
}

if (strlen($user) <= 0) {
	$id_users=0;
} else {
	$id_users=$userlib->get_user_id($user);
}

$mp_columns=array();

function mypageedit_populate($typeclassname) {
	global $smarty, $id_users;
	global $tiki_p_admin;

	$mypage_type=isset($_REQUEST['type']) ? $_REQUEST['type'] : NULL;

	$lpp=25;
	$showpage=isset($_REQUEST['showpage']) ? (int)$_REQUEST['showpage'] : 0;

	$use_id_users=$id_users;
	if (($tiki_p_admin == 'y') && (isset($_REQUEST['admin']))) {
		$use_id_users=-1;
		$smarty->assign('mypage_admin', 1);
	} else $smarty->assign('mypage_admin', 0);

	$tcount=MyPage::countPages($use_id_users, $mypage_type);
	$pcount=(int)(($tcount-1) / $lpp) + 1;
	$offset=$showpage * $lpp;
	$pages=MyPage::listPages($use_id_users, $mypage_type, $offset, $lpp);
 	if (is_callable(array($typeclassname, "customizeMypagesListing")))
 		call_user_func(array($typeclassname, "customizeMypagesListing"), &$pages);

	$pagesnum=array(); for($i=0; $i < $pcount; $i++) $pagesnum[$i]=$i+1;
	$smarty->assign("pagesnum", $pagesnum);
	$smarty->assign("pcount", $pcount);
	$smarty->assign("showpage", $showpage);
	$smarty->assign("mypages", $pages);
}

function mypage_ajax_init() {
	global $ajaxlib, $smarty;

	$smarty->assign("mootools",'y');
	$smarty->assign("mootab",'y');
	$smarty->assign("mootools_windoo",'y');

	//$ajaxlib->debugOn();
	$ajaxlib->setRequestURI("tiki-mypage_ajax.php");
	$ajaxlib->registerTemplate("tiki-mypages.tpl");
	$ajaxlib->registerFunction("mypage_update");
	$ajaxlib->registerFunction("mypage_create");
	$ajaxlib->registerFunction("mypage_delete");
	$ajaxlib->registerFunction("mypage_fillinfos");
    $ajaxlib->registerFunction("mypage_isNameFree");
	$ajaxlib->processRequests();
}

function &mypageedit_addcolumn($title, $hidden=false,
							  $header_tpl=NULL, $content_tpl=NULL, $position=NULL) {
	global $mp_columns;
	if ($position !== NULL) {
		for ($i=count($mp_columns); $i>$position; $i--) {
			$mp_columns[$i]=$mp_columns[$i - 1];
		}
	} else $position=count($mp_columns);

	$l=array('title' => $title, 'hidden' => $hidden,
			 'header_tpl' => $header_tpl,
			 'content_tpl' => $content_tpl);
	$mp_columns[$position]=&$l;
	return $l;
}

function mypageedit_init() {
	global $smarty, $headerlib;
	global $mp_columns;

	$mptypes=MyPage::listMyPageTypes();
	foreach($mptypes as $k => $v) $mptypes_by_id[$v['id']]=$v;
	$smarty->assign("mptypes", $mptypes_by_id);

	$mptypes_js=array();
	foreach($mptypes as $v) {
		$mptypes_js[$v['id']]=$v;
	}
	$mptypes_js=phptojsarray($mptypes_js);
	$smarty->assign('mptypes_js', $mptypes_js);

	$id_types=0;
	$typeclassname=NULL;
	if (isset($_REQUEST['type'])) {
		$mptype_name=$_REQUEST['type'];
		foreach($mptypes as $mptype) {
			if ($mptype['name']==$mptype_name) {
				$id_types=$mptype['id'];
				$smarty->assign('id_types', $id_types);
				$smarty->assign('mptype_name', $mptype['name']);
				$typeclassname=MyPage::getTypeClassName($mptype['name']);
				break;
			}
		}
	}

	$smarty->assign('id_types', $id_types);

	mypageedit_addcolumn('name', false,
						 '{tr}Name{/tr}',
						 '<span id="mypagespan_name_{$mypage.id}">{$mypage.name|escape}</span>');

	mypageedit_addcolumn('description', false,
						 '{tr}Description{/tr}',
						 '<span id="mypagespan_description_{$mypage.id}">{$mypage.description|escape}</span>');

	mypageedit_addcolumn('type', $id_types > 0 ? true : false,
						 '{tr}Type{/tr}',
						 '<span id="mypagespan_type_{$mypage.id}">{$mypage.type_name|escape}</span>');

	mypageedit_addcolumn('dimensions', (($id_types > 0) && ($mptypes_by_id[$id_types]['fix_dimensions'] == 'yes')) ? true : false,
						 '{tr}Dimensions{/tr}',
						 '<span id="mypagespan_width_{$mypage.id}">{$mypage.width}</span> x <span id="mypagespan_height_{$mypage.id}">{$mypage.height}</span>');

	$l=mypageedit_addcolumn('action', false,
							'{tr}Action{/tr}',
							'<a id="mypage_viewurl_{$mypage.id}" href="tiki-mypage.php?mypage={$mypage.name|escape:"url"}" title="{tr}view content{/tr}"><img src="pics/icons/page.png" border="0" height="16" width="16" alt="{tr}view content{/tr}" /></a>'
							.'<a id="mypage_editurl_{$mypage.id}" href="tiki-mypage.php?mypage={$mypage.name|escape:"url"}&amp;edit=1" title="{tr}edit content{/tr}"><img src="pics/icons/page_edit.png" border="0" height="16" width="16" alt="{tr}edit content{/tr}" /></a>'
							.'<a href="#" onclick="showMypageEdit({$mypage.id});" title="{tr}edit entry{/tr}"><img src="pics/icons/pencil.png" border="0" height="16" width="16" alt="{tr}edit entry{/tr}" /></a>'
							.'<a href="#" onclick="deleteMypage({$mypage.id});" title="{tr}delete entry{/tr}"><img src="pics/icons/cross.png" border="0" height="16" width="16" alt="{tr}delete entry{/tr}" /></a>');

	if (!is_myerror($typeclassname) && is_callable(array($typeclassname, 'customizeColumns'))) {
		call_user_func(array($typeclassname, 'customizeColumns'), &$mp_columns);
	}


	$smarty->assign('mp_columns', $mp_columns);

	mypageedit_populate($typeclassname);
	mypage_ajax_init();

	$headerlib->add_cssfile("lib/mootools/extensions/windoo/themes/windoo.css");
	$headerlib->add_cssfile("lib/mootools/extensions/windoo/themes/windoo.aero.css");
// 	$headerlib->add_cssfile("lib/mootools/extensions/windoo/themes/windoo.alphacube.css");
// 	$headerlib->add_cssfile("lib/mootools/extensions/windoo/themes/windoo.aqua.css");
// 	$headerlib->add_cssfile("lib/mootools/extensions/windoo/themes/windoo.nada.css");
	$headerlib->add_cssfile("lib/mootools/extensions/windoo/themes/windoo.mypage.css");
	
	$smarty->assign("section", "mytiki");
	$smarty->assign("mid", "tiki-mypages.tpl");
	$smarty->display("tiki.tpl");
}

mypageedit_init();

/* For the emacs weenies in the crowd.
Local Variables:
   tab-width: 4
   c-basic-offset: 4
End:
*/

?>
