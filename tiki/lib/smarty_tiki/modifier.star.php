<?php

function smarty_modifier_star($score)
{
    global $feature_score, $tikilib;

    if ($feature_score != 'y') {
	return '';
    }

    return $tikilib->get_star($score);
}

/* vim: set expandtab: */

?>
