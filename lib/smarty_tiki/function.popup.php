<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
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
 * @return   string now formatted to use cluetips natively
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
	$options = array();
    $trigger = 'hover';
	$body = '';
	$title = '';

	foreach ($params as $key => $value) {
		switch ($key) {
			case 'text':
				$body = $value;
				break;
			case 'trigger':
				switch ($value) {
					case 'onclick':
					case 'onClick':
						$trigger = 'click';
						break;
					default:
						break;
				}
				break;
			case 'caption':
				$title = $value;
				break;
		}
	}

    if (empty($title) && empty($body)) {
		trigger_error("cluetips: attribute 'text' or 'caption' required");
        return false;
	}

	$body = preg_replace(array('/\\\\r\n/','/\\\\n/','/\\\\r/', '/\\t/'), '', $body);
	$body = str_replace('\&#039;', '&#039;', $body);	// unescape previous js escapes
	$body = str_replace('\&quot;', '&quot;', $body);
	$body = str_replace('&lt;\/', '&lt;/', $body);
	$retval = ' data-toggle="popover" data-container="body" class="tips" ';
	if (isset($options['activation']) && $options['activation'] !== 'click') {
		$retval = ' data-trigger="hover" ';
	} else {
		$retval = ' data-trigger="click" ';
	}
	if ($title) {
		$retval .= ' title="' . $title . '"';
	}
	if ($body) {
		$retval .= ' data-content="' . $body . '" ';
	}

	return $retval;
}
