<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: function.pluralize.php 53803 2015-02-06 00:42:50Z jyhem $

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
    header("location: index.php");
    exit;
}

/**
 * \brief Smarty plugin to return plural or singular form of given word based on count
 * Usage format {pluralize word_count=2 singular_form="mouse" plural_form="mice"}
 *
 */
function smarty_function_pluralize($params, &$smarty)
{
    if (empty($params['singular_form']) || !isset($params['word_count'])) {
        return;
    }

    if (empty($params['plural_form'])) {
        $params['plural_form'] = $params['singular_form'] . 's';
    }

    return ($params['word_count'] == 1) ? $params['singular_form'] : $params['plural_form'];
}