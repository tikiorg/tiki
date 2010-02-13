<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$access->check_script($_SERVER["SCRIPT_NAME"],basename(__FILE__));

$ajaxlib->registerFunction('save_draft');

function save_draft($pageId, $pageDesc, $pageData, $pageComment) {
    global $wikilib;
    require_once('lib/wiki/wikilib.php');

    $wikilib->save_draft($pageId, $pageDesc, $pageData, $pageComment);

    return new xajaxResponse();
}
