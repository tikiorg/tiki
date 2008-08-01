<?php
// $Id:
/**
 * \brief Smarty {remarksbox}{/remarksbox} block handler (tip (default), comment, note or warning)
 *
 * To make a module it is enough to place smth like following
 * into corresponding mod-name.tpl file:
 * \code
 *  {remarksbox type="tip|comment|note|warning" title="Remark title" highlight="y|n" icon="id"}
 *    <!-- module Smarty/HTML/Text here -->
 *  {/remarksbox}
 * \endcode
 *
 * \params
 *  - type		"tip|comment|note|warning" default=tip
 *  - title		Text as a label. Leave out for no label (or icon)
 *  - highlight	"y|n" default=n
 *  - icon		Override default icons. See function.icon.php for more info
 */

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'],basename(__FILE__)) !== false) {
  header('location: index.php');
  exit;
}


function smarty_block_remarksbox($params, $content, &$smarty) {
	extract($params);
	if (!isset($type))  $type = 'tip';
	if (!isset($title)) $title = '';
	if (isset($highlight) && $highlight == 'y') {
		$highlightClass = ' highlight';
	} else {
		$highlightClass = '';
	}
	if (!isset($icon) || $icon=='') {
		if ($type=='tip') {
			$icon='book_open';
		} else if ($type=='comment') {
			$icon='comments';
		} else if ($type=='warning') {
			$icon='exclamation';
		} else if ($type=='note') {
			$icon='information';
		}
	}
	
	$smarty->assign('remarksbox_title', $title);
	$smarty->assign('remarksbox_type', $type);
	$smarty->assign('remarksbox_highlight', $highlightClass);
	$smarty->assign('remarksbox_icon', $icon);
	$smarty->assign_by_ref('remarksbox_content', $content);
	return $smarty->fetch('remarksbox.tpl');
}

?>
