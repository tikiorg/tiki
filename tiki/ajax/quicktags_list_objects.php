<?php

function ajax_quicktags_list_objects($category = '', $offset = 0, $sort_mode = 'taglabel_asc', $find='') {
    global $quicktagslib;

    require_once ('lib/quicktags/quicktagslib.php');

    $maxRecords = 10;

    $res = $quicktagslib->list_quicktags($offset, $maxRecords, $sort_mode, $find, $category);

    //    $quicktagslib->query($category);//serialize($res['data']));

    return $res;
}

?>