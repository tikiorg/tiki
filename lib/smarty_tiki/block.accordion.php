<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: block.tabset.php 45356 2013-03-29 17:58:39Z lphuberdeau $

/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 *
 * \brief smarty_block_tabs : add tabs to a template
 *
 * params: name (optional but unique per page if set)
 * params: toggle=y on n default
 *
 * usage:
 * \code
 *	{accordion}
 * 		{accordion_group title="{tr}Title 1{/tr}"}tab content{/accordion_group}
 * 		{accordion_group title="{tr}Title 2{/tr}"}tab content{/accordion_group}
 *	{/accordion}
 * \endcode
 */

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function smarty_block_accordion($params, $content, $smarty, &$repeat)
{
	global $accordion_current_group;

	if ( $repeat ) {
		$accordion_current_group = null;
		return;
	} else {
		return <<<CONTENT
<div class="panel-group" id="$accordion_current_group">
$content
</div>
CONTENT;
	}
}
