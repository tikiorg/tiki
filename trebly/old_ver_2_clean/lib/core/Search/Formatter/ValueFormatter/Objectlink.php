<?php

class Search_Formatter_ValueFormatter_Objectlink implements Search_Formatter_ValueFormatter_Interface
{
	function render($value, array $entry)
	{
		global $smarty;
		require_once $smarty->_get_plugin_filepath('function', 'object_link');

		$params = array(
			'type' => $entry['object_type'],
			'id' => $entry['object_id'],
			'title' => $value,
		);

		if (isset($entry['url'])) {
			$params['url'] = $entry['url'];
		}

		return '~np~' . smarty_function_object_link($params, $smarty) . '~/np~';
	}
}

