<?php

function ajax_test_list_type() { return 'list'; }

function ajax_test_list($offset, $maxRecords) {
    global $user, $userlib;
    
    return $userlib->get_permissions($offset, $maxRecords);
}

?>