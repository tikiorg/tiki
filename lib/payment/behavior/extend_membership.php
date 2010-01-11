<?php

function payment_behavior_extend_membership( $users, $group, $periods = 1 ) {
	global $userlib;

	$users = (array) $users;

	foreach( $users as $user ) {
		$userlib->extend_membership( $user, $group, $periods );
	}
}

