<?php

$ajaxlib->registerFunction('save_draft');

function save_draft($pageId, $pageDesc, $pageData, $pageComment) {
    global $wikilib;
    require_once('lib/wiki/wikilib.php');

    $wikilib->save_draft($pageId, $pageDesc, $pageData, $pageComment);

    return new xajaxResponse();
}

?>