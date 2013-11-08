<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
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

function smarty_block_accordion_group($params, $content, $smarty, $repeat)
{
	if ($repeat) {
		return;
	}

	global $accordion_current_group, $accordion_position;

	if (empty($accordion_current_group)) {
		$accordion_current_group = uniqid();
		$accordion_position = 0;
	}

	$title = smarty_modifier_escape($params['title']);
	$id = $accordion_current_group . '-' . ++$accordion_position;

	$first = ($accordion_position == 1) ? 'in' : '';

	return <<<CONTENT
<div class="accordion-group">
	<div class="accordion-heading">
		<a class="accordion-toggle" data-toggle="collapse" data-parent="#$accordion_current_group" href="#$id">
			$title
		</a>
	</div>
	<div id="$id" class="accordion-body collapse $first">
		<div class="accordion-inner">
			$content
		</div>
	</div>
</div>
CONTENT;
}
