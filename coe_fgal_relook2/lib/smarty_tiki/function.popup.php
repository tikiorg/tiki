<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
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
 * @return   string now formatted to use convertOverlib() in tiki-jquery.js
 * 
 * params still relevant:
 * 
 *     text        Required: the text/html to display in the popup window
 *     trigger     'onMouseOver' or 'onClick' (onMouseOver default)
 *     sticky      false/true
 *     width       in pixels?
 *     fullhtml    
 */
function smarty_function_popup($params, &$smarty)
{
    $append = '';
    foreach ($params as $_key=>$_value) {
        switch ($_key) {
            case 'text':
            case 'trigger':
            case 'function':
            case 'inarray':
                $$_key = (string)$_value;
                if ($_key == 'function' || $_key == 'inarray')
                    $append .= ',\'' . strtoupper($_key) . "=$_value'";
                break;

            case 'caption':
            case 'closetext':
            case 'status':
                $append .= ',\'' . strtoupper($_key) . "=" . str_replace("'","\'",$_value) . "'";
                break;

            case 'fgcolor':
            case 'bgcolor':
            case 'textcolor':
            case 'capcolor':
            case 'closecolor':
            case 'textfont':
            case 'captionfont':
            case 'closefont':
            case 'fgbackground':
            case 'bgbackground':
            case 'caparray':
            case 'capicon':
            case 'background':
            case 'frame':
                $append .= ',\'' . strtoupper($_key) . "=$_value'";
                break;

            case 'textsize':
            case 'captionsize':
            case 'closesize':
            case 'width':
            case 'height':
            case 'border':
            case 'offsetx':
            case 'offsety':
            case 'snapx':
            case 'snapy':
            case 'fixx':
            case 'fixy':
            case 'padx':
            case 'pady':
            case 'timeout':
            case 'delay':
                $append .= ',\'' . strtoupper($_key) . "=$_value'";
                break;

            case 'sticky':
            case 'left':
            case 'right':
            case 'center':
            case 'above':
            case 'below':
            case 'noclose':
            case 'autostatus':
            case 'autostatuscap':
            case 'fullhtml':
            case 'hauto':
            case 'vauto':
            case 'mouseoff':
            case 'followmouse':
            case 'closeclick':
                if ($_value) $append .= ',\'' . strtoupper($_key) . '\'';
                break;

            default:
                $smarty->trigger_error("[popup] unknown parameter $_key", E_USER_WARNING);
        }
    }

    if (empty($text) && !isset($inarray) && empty($function)) {
        $smarty->trigger_error("overlib: attribute 'text' or 'inarray' or 'function' required");
        return false;
    }

    if (empty($trigger)) {
    	$trigger = "onmouseover";
	} else {
		$append .= ',\'' . $trigger . '\'';
	}

	$text = preg_replace(array('/\\\\r\n/','/\\\\n/','/\\\\r/'), "", $text);	// Remove newlines to avoid JavaScript statement over several lines
	$retval = $trigger . '="return convertOverlib(this,\''.$text.'\'';
	$append = trim($append, ',');
    $retval .= ',[' . $append . ']);"';

    return $retval;
}

/* vim: set expandtab: */
