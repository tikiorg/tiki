<?php

function ajax_test_item() {
    global $user, $userlib;
    
    return $userlib->get_user_info($user);
}

?>