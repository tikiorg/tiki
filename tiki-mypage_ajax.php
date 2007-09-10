<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-mypage_ajax.php,v 1.39 2007-09-10 23:20:59 niclone Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

require_once ('tiki-setup.php');
require_once ('lib/mypage/mypagelib.php');
require_once ('lib/ajax/ajaxlib.php');

if (strlen($user) <= 0) {
    $id_users=0;
} else {
    $id_users=$userlib->get_user_id($user);
}

if (0) {
    header("Content-Type: text/xml; charset=utf-8");
    header("Pragma: no-cache");
    echo '<?xml version="1.0" encoding="utf-8" ?><xjx></xjx>';
    exit(0);
}

if (0) {
	$jax=xajaxResponseManager::getInstance();
	$objResponse = new xajaxResponse();
	//$objResponse->addAlert($outp);
	$jax->append($objResponse);
	$jax->send();
}

function mypage_error($err) {
    $objResponse = new xajaxResponse();

	/*
    if (function_exists('xdebug_get_function_stack')) {
		function mydumpstack($stack) {
			$o='';
			foreach($stack as $line) {
				$o.='* '.$line['file']." : ".$line['line']." -> ".$line['function']."(".var_export($line['params'], true).")\n";
			}
			return $o;
		}
		$err.= str_replace("\n", '\n', "\n".mydumpstack(xdebug_get_function_stack()));
    }
	*/
	$objResponse->addScript("alert('".addslashes($err)."');");
    return $objResponse;
}

function mypage_win_setrect($id_mypage, $id_mypagewin, $rect) {
    global $id_users;
	
    $objResponse = new xajaxResponse();
	
    $mypage=MyPage::getMyPage_byId((int)$id_mypage, $id_users);
	if (is_string($mypage))
		return mypage_error($mypage);

    $mywin=$mypage->getWindow((int)$id_mypagewin);
	if (is_string($mypage))
		return mypage_error($mywin);

	if ($mywin) {
		$err=$mywin->setRect($rect['left'], $rect['top'], $rect['width'], $rect['height']);
		if (is_string($err))
			return mypage_error($err);
		$err=$mywin->commit();
		if (is_string($err))
			return mypage_error($err);
	} else {
		return mypage_error(tra("Window not found"));
	}

    return $objResponse;
}

function mypage_win_destroy($id_mypage, $id_mypagewin) {
    global $id_users;

    $objResponse = new xajaxResponse();

    $mypage=MyPage::getMyPage_byId((int)$id_mypage, $id_users);
	if (is_string($mypage))
		return mypage_error($mypage);

	$win=$mypage->getWindow((int)$id_mypagewin);
	if (is_string($win)) return mypage_error($err);
	if ($win) $err=$win->destroy();
    
    if (!empty($err)) {
		$objResponse=mypage_error($err);
	
		// hack... re-open the windows
		$win=$mypage->getWindow((int)$id_mypagewin);
		$objResponse->addScript($win->getJSCode(true));
    }
	
    return $objResponse;
}

function mypage_win_create($id_mypage, $contenttype, $title, $form_config) {
    global $id_users;

	if (is_array($form_config)) // it seem that xajax add some backslashes here
		foreach($form_config as $k => $v)
			$form_config[$k]=stripslashes($v);

    $objResponse = new xajaxResponse();

    $mypage=MyPage::getMyPage_byId((int)$id_mypage, $id_users);
	if (is_string($mypage))
		return mypage_error($mypage);

    $mywin=$mypage->newWindow();
	if (is_string($mywin))
		return mypage_error($mywin);

    $mywin->setTitle($title);
    $mywin->setContentType($contenttype);
    $comp=$mywin->getComponent();
    $comp->configure($form_config);
    $err=$mywin->commit();
	if (strlen($err)) {
		$mywin->destroy();
		return mypage_error($err);
	}
	$objResponse->addScript($mywin->getJSCode(true));

    return $objResponse;
}

function mypage_win_configure($id_mypage, $id_win, $form) {
    global $id_users;

	if (is_array($form)) // it seem that xajax add some backslashes here
		foreach($form as $k => $v)
			$form[$k]=stripslashes($v);

    $objResponse = new xajaxResponse();

    $mypage=MyPage::getMyPage_byId((int)$id_mypage, $id_users);
	if (is_string($mypage))
		return mypage_error($mypage);

	if ($id_win) {
		$mywin=$mypage->getWindow((int)$id_win);
	} else {
		$mywin=$mypage->newWindow();
	}

	if (is_string($mywin))
		return mypage_error($mywin);

    $comp=$mywin->getComponent();
	if (is_string($comp))
		return mypage_error($comp);

    $reload=$comp->configure($form);
    $err=$mywin->commit();
	if (strlen($err)) {
		return mypage_error($err);
	}

	if ($reload) {
// 		if ($mywin->getParam('contenttype') == 'iframe')
// 			$objResponse->addScriptCall("tikimypagewin[$id_win].setURL", $mywin->getParam('config'));
// 		else
// 			$objResponse->addScriptCall("tikimypagewin[$id_win].setHTML", $comp->getHTMLContent());

// 		$objResponse->addScriptCall("tikimypagewin[$id_win].setSize",
// 									$mywin->getParam('width'), $mywin->getParam('height'));

		$objResponse->addScript("tikimypagewin[$id_win].destroy();");
		$objResponse->addScript($mywin->getJSCode(true));
	}
    return $objResponse;
}

function mypage_win_prepareConfigure($id_mypage, $id_win, $compname=null) {
    global $id_users;
    
    $objResponse = new xajaxResponse();

    $mypage=MyPage::getMyPage_byId((int)$id_mypage, $id_users);
	if (is_string($mypage))
		return mypage_error($mypage);

	if (!$id_win && strlen($compname)) {
		if (MyPageWindow::isComponentConfigurable($compname))
			$confdiv=MyPageWindow::getComponentConfigureDiv($compname);
		else
			$confdiv=NULL;
	} else {
		$mywin=$mypage->getWindow($id_win);
		if (is_string($mywin))
			return mypage_error($mywin);
		
		$comp=$mywin->getComponent();
		if (is_string($comp))
			return mypage_error($comp);
		
		if ($comp->isConfigurable())
			$confdiv=$comp->getConfigureDiv();
		else
			$confdiv=NULL;
	}

	if ($confdiv === NULL) $confdiv='';
	if (is_string($confdiv)) $confdiv=array('html' => $confdiv);

	if (isset($confdiv['html']))
		$objResponse->addAssign('mypage_divconfigure', 'innerHTML', $confdiv['html']);
	if (isset($confdiv['js']))
		$objResponse->addScript($confdiv['js']);
	
    return $objResponse;    
}

function mypage_update($id_mypage, $vals, $form) {
    global $id_users;

	if (is_array($form)) // it seem that xajax add some backslashes here
		foreach($form as $k => $v)
			$form[$k]=stripslashes($v);

    $objResponse = new xajaxResponse();

    $mypage=MyPage::getMyPage_byId((int)$id_mypage, $id_users);
	if (is_string($mypage))
		return mypage_error($mypage);

	if (array_key_exists('id_types', $vals)) unset($vals['id_types']);
	$mypage->setParams($id_mypage, $vals);

	if (is_array($form)) {
		$typeclass=$mypage->getTypeClass();
		if ($typeclass) {
			$err=$typeclass->configure($form, false);
			if (strlen($err)) return mypage_error($err);
		}
	}

    $err=$mypage->commit();
	if (strlen($err)) {
		return mypage_error($err);
	}

	$newvals=array();
	foreach($vals as $k => $v) {
		$newvals[$k]=$mypage->getParam($k);
	}

	//$objResponse->call("updateMypageParams", $id_mypage, $newvals);
	//$objResponse->addScript('closeMypageEdit();');
	if (array_key_exists('name', $vals)) // if it seem that update come from tiki-mypages...
		$objResponse->addScript("window.location.reload()");

    return $objResponse;
}

function mypage_create($vals, $form) {
    global $id_users;

	if (is_array($form)) // it seem that xajax add some backslashes here
		foreach($form as $k => $v)
			$form[$k]=stripslashes($v);

    $objResponse = new xajaxResponse();

    $mypage=MyPage::getMyPage_new($id_users, $vals['id_types']);
	if (is_string($mypage))
		return mypage_error($mypage);

	unset($vals['id_types']);

	$err=$mypage->setParams($id_mypage, $vals);
	if ($err) return mypage_error($err);

	$typeclass=$mypage->getTypeClass();

	if (is_array($form)) {
		if ($typeclass) {
			$err=$typeclass->configure($form, true);
			if (strlen($err)) return mypage_error($err);
		}
	}

	$err=$mypage->commit();
	if (strlen($err)) {
		return mypage_error($err);
	}
	
	$oncreategoto=is_callable(array($typeclass, 'onCreateGoTo')) ? $typeclass->onCreateGoTo() : 'mypage';
	switch($oncreategoto) {
	case 'mypage':
		$objResponse->addScript("window.location='tiki-mypage.php?mypage=".urlencode($mypage->getParam('name'))."&edit=1'");
		break;
	case 'same':
		$objResponse->addScript("window.location.reload()");
		break;
	default:
		$objResponse->addScript("window.location='".addslashes($oncreategoto)."'");
		break;
	}

    return $objResponse;
}

function mypage_delete($id_mypage) {
    global $id_users;

    $objResponse = new xajaxResponse();

    $mypage=MyPage::getMyPage_byId((int)$id_mypage, $id_users);
	if (is_string($mypage))
		return mypage_error($mypage);

    $err=$mypage->destroy();
	if (strlen($err))
		return mypage_error($err);

	$objResponse->addScript("window.location.reload()");

    return $objResponse;
}

function mypage_isNameFree($name) {
    $objResponse = new xajaxResponse();
	$r=MyPage::isNameFree($name);
	$objResponse->assign('mypageedit_name_unique', 'innerHTML', $r ? '' : tra('name already exists'));
	return $objResponse;
}

function mypage_fillinfos($id_mypage, $id_types=NULL, $update_only_type=false) {
    global $id_users, $smarty;

    $objResponse = new xajaxResponse();

	$conf=NULL;
	if ($id_mypage) {
		$mypage=MyPage::getMyPage_byId((int)$id_mypage, $id_users);
		if (is_string($mypage))
			return mypage_error($mypage);

		$id_types=$mypage->getParam('id_types');

		if (!$update_only_type) {
			$objResponse->addAssign('mypageedit_id', 'value', $id_mypage);
			$objResponse->addAssign('mypageedit_name', 'value', $mypage->getParam('name'));
			$objResponse->addAssign('mypageedit_name_orig', 'value', $mypage->getParam('name'));
			$objResponse->addAssign('mypageedit_description', 'value', $mypage->getParam('description'));
			$objResponse->addAssign('mypageedit_width', 'value', $mypage->getParam('width'));
			$objResponse->addAssign('mypageedit_height', 'value', $mypage->getParam('height'));
			$objResponse->addAssign('mypageedit_type', 'value', $mypage->getParam('id_types'));
			$objResponse->addScript('mypageTypeChange('.(int)$mypage->getParam('id_types').');');
			$cat_type = 'mypage';
			$cat_objid = $id_mypage;
		}
		$conf=$mypage->getTypeHTMLConfig();
		$type=NULL;
	} else {
		$type=MyPage::getMypageType($id_types);
		if (is_array($type)) {
			$conf=MyPage::getTypeHTMLConfig($type['name']);
		}
	}

	if ($conf === NULL) $conf='';
	
	$objResponse->addAssign('mypageedit_typeconf', 'innerHTML', ''); // this fixe a bug that don't really update value of input field
	$objResponse->addAssign('mypageedit_typeconf', 'innerHTML', $conf);

	/* categories update */
	if (!$update_only_type) {
		include('categorize_list.php');
		$smarty->assign('mandatory_category', 'y');
		$objResponse->addAssign('mypageedit_categorize_tpl', 'innerHTML', $smarty->fetch('netineo_categorize.tpl'));
	}

	/* templates update */
	if (is_array($type)) {
		$templates=MyPage::listPages($type['templateuser'], $type['name']);
		$templates_html="<option id='0'>".tra("Without template")."</option>";
		foreach($templates as $template)
			$templates_html.="<option id='".$template['id']."'>".htmlspecialchars($template['name'])."</option>";
		$objResponse->addAssign('mypageedit_template', 'innerHTML', $templates_html);
		$objResponse->addScript('$("mypageedit_tr_template").style.display="";');
	} else {
		$objResponse->addScript('$("mypageedit_tr_template").style.display="none";');
	}

    return $objResponse;
}

function mptype_fillinfos($id_mptype) {
    global $id_users;
    
    $objResponse = new xajaxResponse();

    /*
     * TODO: check if user has permissions
     */
        
    $mptype=MyPage::getMypageType($id_mptype);

    if ($mptype) {
		$objResponse->addAssign('mptype_id', 'value', (int)$id_mptype);
		$objResponse->addAssign('mptype_name', 'value', $mptype['name']);
		$objResponse->addAssign('mptype_description', 'value', $mptype['description']);
		$objResponse->addAssign('mptype_section', 'value', is_null($mptype['section']) ? '' : $mptype['section']);
		$objResponse->addAssign('mptype_permissions', 'value', is_null($mptype['permissions']) ? '' : $mptype['permissions']);
		$objResponse->addAssign('mptype_def_width', 'value', is_null($mptype['def_width']) ? '' : $mptype['def_width']);
		$objResponse->addAssign('mptype_def_height', 'value', is_null($mptype['def_height']) ? '' : $mptype['def_height']);
		$objResponse->addAssign('mptype_fix_dimensions', 'checked', $mptype['fix_dimensions'] == 'yes' ? true : false);
		$objResponse->addAssign('mptype_def_bgcolor', 'value', is_null($mptype['def_bgcolor']) ? '' : $mptype['def_bgcolor']);
		$objResponse->addAssign('mptype_fix_bgcolor', 'checked', $mptype['fix_bgcolor'] == 'yes' ? true : false);
		$objResponse->addAssign('mptype_templateuser', 'value', $mptype['templateuser']);
		foreach($mptype['components'] as $component)
			$objResponse->addAssign('mptype_components_'.$component['compname'], 'selected', '1');
    } else {
		$objResponse->addScript("alert('non');");
    }

    return $objResponse;
}

function mptype_delete($id_mptype) {
    global $id_users;

    $objResponse = new xajaxResponse();

    MyPage::deleteMyPageType($id_mptype);
    $objResponse->addScript("window.location.reload()");

    return $objResponse;
}

function mptype_create($vals) {
    global $id_users;

    $id=MyPage::createMyPageType();

	$objResponse=mptype_update($id, $vals);
    $objResponse->addScript("window.location.reload()");

    return $objResponse;
}

function mptype_update($id, $vals) {
    global $id_users;

    $objResponse = new xajaxResponse();

    MyPage::updateMyPageType($id, $vals);

	$vals=MyPage::getMypageType($id);
    $objResponse->addAssign('mptype_name_'.$id, 'innerHTML', $vals['name']);
    $objResponse->addAssign('mptype_description_'.$id, 'innerHTML', $vals['description']);
    $objResponse->addAssign('mptype_section_'.$id, 'innerHTML', is_null($vals['section']) ? '' : $vals['section']);
    $objResponse->addAssign('mptype_permissions_'.$id, 'innerHTML', is_null($vals['permissions']) ? '' : $vals['permissions']);
	$comps=''; foreach($vals['components'] as $v) $comps.=$v['compname'].' ';
    $objResponse->addAssign('mptype_components_'.$id, 'innerHTML', $comps);

    return $objResponse;
}

function comp_function($id_mypage, $id_win, $args) {
	global $id_users;

    $mypage=MyPage::getMyPage_byId((int)$id_mypage, $id_users);
	if (is_string($mypage))
		return mypage_error($mypage);

	$mywin=$mypage->getWindow((int)$id_win);
	if (is_string($mywin))
		return mypage_error($mywin);

    $comp=$mywin->getComponent();
	if (is_string($comp))
		return mypage_error($comp);

	return $comp->ajax($args);
}

function mypage_ajax_catchall($funcname, $args) {
    $objResponse = new xajaxResponse();

	$blah=var_export($args, true);
	
	//$objResponse->addScript("alert('".str_replace("\n", '\n', addslashes($blah))."');");

	return $objResponse;
}

function mypage_ajax_init() {
    global $ajaxlib;

    //$ajaxlib->debugOn();
    $ajaxlib->registerFunction("mypage_win_setrect");
    $ajaxlib->registerFunction("mypage_win_destroy");
    $ajaxlib->registerFunction("mypage_win_create");
    $ajaxlib->registerFunction("mypage_win_prepareConfigure");
    $ajaxlib->registerFunction("mypage_win_configure");

    $ajaxlib->registerFunction("mypage_update");
    $ajaxlib->registerFunction("mypage_create");
    $ajaxlib->registerFunction("mypage_delete");
    $ajaxlib->registerFunction("mypage_fillinfos");
    $ajaxlib->registerFunction("mypage_isNameFree");

    $ajaxlib->registerFunction("mptype_fillinfos");
    $ajaxlib->registerFunction("mptype_delete");
    $ajaxlib->registerFunction("mptype_create");
    $ajaxlib->registerFunction("mptype_update");

    $ajaxlib->registerFunction("comp_function");

	$ajaxlib->registerCatchAllFunction("mypage_ajax_catchall");
	
    $ajaxlib->processRequests();
}




mypage_ajax_init();

/* For the emacs weenies in the crowd.
Local Variables:
   tab-width: 4
   c-basic-offset: 4
End:
*/

?>