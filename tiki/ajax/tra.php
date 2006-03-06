<?php

function ajax_tra_type() { return "item"; }

function ajax_tra($str) {
    return array('from' => $str,
		 'to' => tra($str));
}

?>