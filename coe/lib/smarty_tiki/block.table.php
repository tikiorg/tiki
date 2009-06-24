<?php
/* $Id:  $ */

// this script may only be included - so it's better to die if called directly
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

/**
 * \brief smarty_block_tabs : add tabs to a template
 *
 * params: TODO
 *
 * usage: 
 * \code
 *      {table name="matable" from=$montableau cycle=$uncycle}
 *       {col name="col1" title="" class="" sort="" type=(string|numeric|user|...) hidden='y'}
 *         %col1% %col2 %((%col3%+%col4%))
 *       {/col}
 *       {col name="col12 title="" class="" sort="" type=(string|numeric|user|...) _link="tiki-download_file.php?%col3%"}
 *         %col5%
 *       {/col}
 *       {action_col}
 *         {action name="{tr}Delete{tr}" _icon="delete" _link="..."}
 *          ...
 *       {/action_col}
 *      {/table}
 * \endcode
 *
 */

function replace_columns_content(&$line,$content) {
	preg_match_all("/((%25(?![0-9]))|%)(.+?)\\1/",$content,$matches,PREG_PATTERN_ORDER);
	foreach($matches[3] as $c) {
		$pattern[] = "/((%25(?![0-9]))|%)$c\\1/";
		$replace[] = $line[$c];
	}
	return preg_replace($pattern,$replace,$content);
}

function smarty_block_table($params, $content, &$smarty, &$repeat) {
	global $prefs, $smarty_tables ;

	if ( $repeat ) {
		// opening 
		if (!isset($smarty_tables) or !is_array($smarty_tables) ) {
			$smarty_tables = array();
		}
		$smarty_tables[] = array();
		return;
	} else {
		//closing
		end($smarty_tables);
		$current = key($smarty_tables);
		if ( is_array($params['from']) and $current !== false ) {
			$ret .= "<table><tr>";
			require_once $smarty->_get_plugin_filepath('block', 'self_link');
			if ( isset($params['_template']) ) $params_link['_template'] = $params['_template'];
			if ( isset($params['_htmlelement']) ) $params_link['_htmlelement'] = $params['_htmlelement'];
			if ( isset($params['_sort_arg']) ) $params_sort['_sort_arg'] = $params['_sort_arg'];
			foreach ($smarty_tables[$current] as $columns) {
				if (isset($columns['sort']) and $columns['sort'] == 'y') {
					$params_sort = $params_link;
					$params_sort['_sort_field'] = $columns['name'];
					$ret .= "<th>".smarty_block_self_link($params_sort,$columns['title'],$smarty,false)."</th>";
				} else {
					$ret .= "<th>".$columns['title']."</th>";
				}
			}
			$ret .= "</tr>";
			foreach($params['from'] as $line) {
				if ( !empty($params['cycle']) ) {
					require_once $smarty->_get_plugin_filepath('function', 'cycle');
					$class = smarty_function_cycle(array('name' => $params['cycle']),$smarty);
					$ret .= "<tr class=\"$class\">";
				} else $ret .= "<tr>";
				foreach ($smarty_tables[$current] as $column) {
					if (!empty($column['content'])) {
						$content = replace_columns_content(&$line,$column['content']);
					} else {
						$content = $line[$column['name']];
					}
					if (isset($column['type'])) {
						switch ($column['type']) {
							case 'icon':
								require_once $smarty->_get_plugin_filepath('function', 'icon');
								$content = smarty_function_icon(array('_id' => $content), $smarty);
								break;
							case 'link':
								$params_link2 = $params_link;
								if ( !empty($column['href']) ) $params_link2['_script'] = replace_columns_content(&$line,$column['href']);
								require_once $smarty->_get_plugin_filepath('block', 'self_link');
								$content = smarty_block_self_link($params_link2, $content, $smarty, false);
								break;
							default:
						}
					}
					$ret .= "<td>$content</td>";
				}
				$ret .= "</tr>";
			}
			$ret .= "</table>";
		} else {
			$ret .= "CURRENT=$current<br/>";
			$ret .= "<pre>".print_r($params['from'],true)."</pre>";
		}
		return $ret;
		unset($smarty_tables[$params['name']]);
	}
}
