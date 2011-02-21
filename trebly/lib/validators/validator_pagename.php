<?php

function validator_pagename($input, $parameter = '', $message = '') {
	global $tikilib, $prefs;
	if ($parameter == 'not') {
		if ($tikilib->page_exists($input)) {
			return tra("Page already exists");
		}
	} else {
		if (!$tikilib->page_exists($input)) {
			return tra("Page does not exist");
		}
	}
	return true; 
}