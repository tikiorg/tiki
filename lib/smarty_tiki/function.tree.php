<?php
  // $header: $
//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

/*
 * JavaScript Tree
 * 
 * That smarty function is mostly intended to be used in .tpl files
 * syntax: {tree}
 * 
 */
function smarty_function_tree($params, &$smarty) {
	global $prefs;

	if ( $prefs['feature_phplayers'] != 'y' || $prefs['javascript_enabled'] == 'n' ) {
	  // If PHP Layers and/or JavaScript are disabled, force the php version of the tree
	  $params['type'] = 'phptree';
	}

	global $tikiphplayers;
	include_once('lib/phplayers_tiki/tiki-phplayers.php');
	require_once $smarty->_get_plugin_filepath('function', 'query');

	if ( ! function_exists('data2struct') ) {
		function data2struct(&$data, $level, &$expanded) {
			static $cur = 0;
			$ret = '';
			if ( is_array($data) && $level > 0 ) {
				$cur++;
				$link = '';
				if ( isset($data['link']) && $data['link'] != '') {
					$link = $data['link'];
				} elseif ( isset($data['link_id']) && isset($data['link_var']) && $data['link_id'] >= 0 ) {
					$link = smarty_function_query(array(
						'_type' => 'absolute_path',
						$data['link_var'] => $data['link_id'],
						'offset' => 'NULL' // Always go back to the first page of the destination
					), $smarty);
				}
				if ( isset($data['current']) ) $data['name'] = '<b>'.$data['name'].'</b>';
				$name = $data['name'] . ( isset($data['addon']) ? ' '.$data['addon'] : '' );
				$ret .= str_repeat('.', $level).'|'.$name.'|'.$link.'||folder.png';
				if ( in_array($cur, $expanded) ) $ret .= '||1';
				$ret .= "\n";
				if ( is_array($data['data']) ) {
					foreach ( $data['data'] as $d ) {
						$ret .= data2struct($d, $level + 1, $expanded);
					}
				}
			}
			return $ret;
		}
	}

	$structure = '';

	if ( ! isset($params['type']) ) $params['type'] = 'tree';
	if ( ! isset($params['expanded']) ) $params['expanded'] = array(1);
	if ( isset($params['data']) && is_array($params['data']) ) {
		$expanded = ( $params['type'] == 'phptree' ) ? array(1) : $params['expanded'];
		$structure = data2struct($params['data'], 1, $expanded);
	}

	if ( $prefs['javascript_enabled'] == 'y' ) {

		// Update cookie that stores tree elements that should be expanded
		//   by keeping those already expanded by the user and those that should now be expanded ($params['expanded'])
		//
		if ( isset($_COOKIE) && isset($_COOKIE['phplm_expand']) && $_COOKIE['phplm_expand'] != '' ) {
			$phplm_expand = implode('|', array_unique(array_merge($params['expanded'], explode('|', $_COOKIE['phplm_expand']))));
			if ( ! headers_sent() ) {
				// Not using php's setcookie function because pipes '|' are converted to %7C
				//   and are no more understood by PHP Layers javacript
				header('Set-Cookie: phplm_expand='.$phplm_expand.'; path=/');
			}
		}
	
		// Reset cookie that stores tree elements collapsed by the user, in order
		//   to be sure every elements of $params['expanded'] are really expanded
		//
		setcookie('phplm_collapse', '', false, '/');

	}

	return $tikiphplayers->mkMenu($structure, '', $params['type'], '', 0, max($params['expanded']));
}
?>
