<?php

require_once $this->_get_plugin_filepath('modifier','tiki_date_format');
function smarty_modifier_tiki_long_datetime($string)
{
	global $tikilib;
	return smarty_modifier_tiki_date_format($string, $tikilib->get_long_datetime_format());
}

/* vim: set expandtab: */

?>
