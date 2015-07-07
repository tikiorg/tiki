<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
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
 * smarty_function_form_item: Display a basic form item in proper Bootstrap syntax
 *
 * params will be used as params for as smarty self_link params, except those special params specific to smarty button :
 *  - _label : this is the name of the label that should show up
 *	- _field: the form input field should be passed to this parameter. 
 *  Usage of this function should be something like {formitem _field={$f_title} _label="Title"}
  */
function smarty_function_formitem($params, $smarty)
{
	if ( ! is_array($params) || ! isset($params['_field']) || ! isset($params['_label']) ) return;
	global $tikilib, $prefs;

	$class = "";
	if (isset($params['class'])){
		$class = $params['class'];
	}
    $id = "";
    if (isset($params['id'])){
        $temp = $params['id'];
        $id = "id='".$temp."'";
    }

	$help = "";
	if (isset($params['_help'])){
		$help = '<span class="help-block">'.$params['_help'].'</span>';
	}

    if ($params['_help-popup']){
        $popup = '<a tabindex="0" data-toggle="popover" data-trigger="focus" title="Dismissible popover" data-content="'.$params['_help-popup'].'"><span class="fa fa-question-circle"></span></a>';
    }

	$smarty->loadPlugin('smarty_block_self_link');

	if ($params["mandatory"]=="y"){ //override optional label
		$params['_field'] =preg_replace("/(\&nbsp\;\<small\>\<i\>\(\w*\)\<\/i\>\<\/small\>)*(.*)/", "$2", $params['_field']); 
	}

	if ($params['is_checkbox'] == 'y'){
		$html = '<div class="checkbox"><label>'.$params['_field'].$params['_label'].'</label> '.$popup.'</div>';
	}else{
		$html = '<div '.$id.' class="form-group '. $params['class']. '"><label>'.$params['_label'].'</label> '.$popup. $help . $params['_field'].'</div>';
	}

	
	return $html;
}