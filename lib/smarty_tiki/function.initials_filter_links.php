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

function smarty_function_initials_filter_links($params, &$smarty) {
	$html = '';
	$sep = ' . ';
	$default_type = 'absolute_path';
	if ( ! isset($params['_initial']) ) $params['_initial'] = 'initial';
	$current_initial = isset($_REQUEST[$params['_initial']]) ? $_REQUEST[$params['_initial']] : '';
	if ( ! isset($params['_htmlelement']) ) $params['_htmlelement'] = 'tiki-center';
	if ( ! isset($params['_template']) ) $params['_template'] = basename($_SERVER['PHP_SELF'], '.php').'.tpl';
	if ( ! isset($params['_class']) ) $params['_class'] = 'prevnext';

	// Include smarty functions used below
	global $smarty;
	require_once $smarty->_get_plugin_filepath('block', 'ajax_href');
	require_once $smarty->_get_plugin_filepath('function', 'query');

	$tag_start = "\n".'<a class="'.$params['_class'].'" '.smarty_block_ajax_href(
		array('template' => $params['_template'], 'htmlelement' => $params['_htmlelement']),
		smarty_function_query(
			array('_type' => $default_type,  $params['_initial'] => 'X', 'offset' => 'NULL', 'reloff' => 'NULL'),
			$smarty
		),
		$smarty,
		false
	).'>';

	foreach ( array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z') as $i ) {
		if ( $current_initial == $i ) {
			$html .= "\n" . '<span class="highlight">' . strtoupper($i) . '</span>' . $sep;
		} else {
			$html .= "\n".str_replace( $params['_initial'].'=X', $params['_initial'].'='.$i, $tag_start).strtoupper($i).'</a>'.$sep;
		}
	}
	$html .= "\n".str_replace( $params['_initial'].'=X', $params['_initial'].'=', $tag_start).tra('All').'</a>';
	
	return '<div class="alphafilter">'.$html.'</div>';
}
