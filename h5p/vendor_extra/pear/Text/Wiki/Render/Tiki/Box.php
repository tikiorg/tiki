<?php

class Text_Wiki_Render_Tiki_Box extends Text_Wiki_Render {

	/**
	*
	* Renders a token into text matching the requested format.
	*
	* @access public
	*
	* @param array $options The "options" portion of the token (second
	* element).
	*
	* @return string The text rendered from the token options.
	*
	*/

	function token($options)
	{
	global $prefs;

		if ($options['type'] == 'start') {
			return $prefs['feature_simplebox_delim'];
		}

		if ($options['type'] == 'end') {
			return $prefs['feature_simplebox_delim'];
		}
	}
}
