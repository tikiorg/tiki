<?php

function ajax_test_type() { return 'list'; }

function ajax_test() {

    switch (ajax_test_type()) {
    case 'scalar':
	return 'Hello world!';
    case 'item':
	return array('a' => 'first', 'b' => 'second', 'c' => 'third');
    case 'list':
	return array(array('a' => 'one', 'b' => 'two', 'c' => 'three'),
		     array('a' => 'eleven', 'b' => 'twelve', 'c' => 'thirteen'),
		     array('a' => '21', 'b' => '22', 'c' => '23'));
    }
}

?>