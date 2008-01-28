<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function smarty_function_initials_filter_links($params, &$smarty) {
	extract($params);

	$html = '';
	$sep = ' . ';
	$default_type = 'absolute_path';
	$current_initial = isset($_REQUEST['initial']) ? $_REQUEST['initial'] : '';
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
			array('_type' => $default_type, 'initial' => 'X', 'offset' => 'NULL', 'reloff' => 'NULL'),
			$smarty
		),
		$smarty
	).'>';

	foreach ( array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z') as $i ) {
		if ( $current_initial == $i ) {
			$html .= "\n".'<span class="button2"><span class="linkbuton">'.strtoupper($i).'</span></span>'.$sep;
		} else {
			$html .= "\n".str_replace('initial=X', 'initial='.$i, $tag_start).strtoupper($i).'</a>'.$sep;
		}
	}
	$html .= "\n".str_replace('initial=X', 'initial=', $tag_start).tra('All').'</a>';
	
	return '<div align="center">'.$html.'</div>';
}



?>
