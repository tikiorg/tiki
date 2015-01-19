<?php
// (c) Copyright 2002-2014 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * Smarty plugin for Tiki using jQuery ClueTip instead of OverLib
 */

/**
 * Smarty {popup} function plugin
 *
 * Type:     function<br>
 * Name:     popup<br>
 * Purpose:  make text pop up in windows via ClueTip
 * @link     not very relevant anymore http://smarty.php.net/manual/en/language.function.popup.php {popup}
 *           (Smarty online manual)
 * @author   Jonny Bradley, replacing Smarty original (by Monte Ohrt <monte at ohrt dot com>)
 * @param    array
 * @param    Smarty
 * @return   string now formatted to use popover natively
 *
 * params still relevant:
 *
 *     text        Required: the text/html to display in the popup window
 *     trigger     'onMouseOver' or 'onClick' (onMouseOver default)
 *     sticky      false/true
 *     width       in pixels?
 *     fullhtml
 */
function smarty_function_popup($params, $smarty)
{
	$options = array(
		'data-toggle' => 'popover',
		'data-container' => 'body',
		'data-trigger' => 'hover',
		'data-content' => '',
	);

	foreach ($params as $key => $value) {
		switch ($key) {
			case 'text':
				$options['data-content'] = $value;
				break;
			case 'trigger':
				switch ($value) {
					case 'onclick':
					case 'onClick':
						$options['data-trigger'] = 'click';
						break;
					default:
						$options['data-trigger'] = $value;
						break;
				}
				break;
			case 'caption':
				$options['title'] = $value;
				break;
			case 'width':
			case 'height':
				$options[$key] = $value;
				break;
			case 'sticky':
				//$options['data-trigger'] = 'focus'; // doesn't seem to work
				break;
			case 'fullhtml':
				$options['data-html'] = true;
				break;
			case 'background':
				if (!empty($params['width'])) {
					if (!isset($params["height"])) {
						$params["height"] = 300;
					}
					$options['data-content'] = "<div style='background-image:url(" . $value . ");background-repeat:no-repeat;width:" . $params["width"] . "px;height:" . $params["height"] . "px;'>" . $options['data-content'] . "</div>";
				} else {
					$options['data-content'] = "<div style='background-image:url(" . $value . ");width:100%;height:100%;'>" . $options['data-content'] . "</div>";
				}
				$options['data-html'] = true;
				break;
		}
	}

    if (empty($options['title']) && empty($options['data-content'])) {
		trigger_error("popover: attribute 'text' or 'caption' required");
        return false;
	}

	$options['data-content'] = preg_replace(array('/\\\\r\n/','/\\\\n/','/\\\\r/', '/\\t/'), '', $options['data-content']);
	$options['data-content'] = str_replace('\&#039;', '&#039;', $options['data-content']);	// unescape previous js escapes
	$options['data-content'] = str_replace('\&quot;', '&quot;', $options['data-content']);
	$options['data-content'] = str_replace('&lt;\/', '&lt;/', $options['data-content']);

	$retval = '';

	foreach ($options as $k => $v) {
		$retval .= $k . '=' . json_encode($v, JSON_UNESCAPED_SLASHES) . ' ';
	}

	return $retval;
}
