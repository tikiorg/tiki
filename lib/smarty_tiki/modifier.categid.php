<?php

if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}
/**
 * Gets Category Id from the Category name
 */

function smarty_modifier_categid($category)
{
	return TikiLib::lib('categ')->get_category_id($category);
}
