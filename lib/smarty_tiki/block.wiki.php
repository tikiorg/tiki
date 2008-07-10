<?php
/*
 *
 * Smarty plugin to display wiki-parsed content
 *
 * Usage: {wiki}wiki text here{/wiki} 
 * {wiki isHtml="true" }html text as stored by fckEditor here{/wiki}
 */

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function smarty_block_wiki($params, $content, &$smarty)
{
		global $user,$userlib,$tikilib;
		if ( (isset($params['isHtml'])) and ($params['isHtml'] ) ) {
		  $isHtml = true;
		} else {
		  $isHtml = false;
		}
		return $tikilib->parse_data($content,$isHtml);
}

?>
