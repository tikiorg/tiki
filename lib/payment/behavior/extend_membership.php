<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function payment_behavior_extend_membership( $users, $group, $periods = 1 ) {
	global $userlib;

	$users = (array) $users;

	foreach( $users as $user ) {
		$userlib->extend_membership( $user, $group, $periods );
	}
}

