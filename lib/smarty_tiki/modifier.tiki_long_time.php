<?php

require_once $smarty->_get_plugin_filepath('modifier','tiki_date_format');
function smarty_modifier_tiki_long_time($string)
{
	global $tikilib;
	return smarty_modifier_tiki_date_format($string, $tikilib->get_long_time_format());
}

/* vim: set expandtab: */

?>
