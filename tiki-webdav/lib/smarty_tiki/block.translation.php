<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 * Smarty {translation lang=XX}{/translation} block plugin
 *
 * Type:     block function<br>
 * Name:     translation<br>
 * Purpose:  Support many languages in a template, only showing block
             if language matches
 * @param array
 * <pre>
 * Params:   lang: string (language, ex: en, pt-br)
 * </pre>
 * @param string contents of the block
 * @param Smarty clever simulation of a method
 * @return string string $content re-formatted
 */
function smarty_block_translation($params, $content, &$smarty)
{
    if (isset($content)) {
        $lang = $params['lang'];
	if ($smarty->get_template_vars('language') == $lang) {
	    return $content;
	} else {
	    return '';
	}
    }
}
