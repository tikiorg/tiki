<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once 'lib/core/Perms/Check.php';

class Perms_Check_Creator implements Perms_Check
{
	private $user;
	private $key;
	private $suffix;
	
	function __construct( $user, $key = 'creator', $suffix = '_own' ) {
		$this->user = $user;
		$this->key = $key;
		$this->suffix = $suffix;
	}

	function check( Perms_Resolver $resolver, array $context, $name, array $groups ) {
		if( isset( $context[$this->key] ) && $context[$this->key] == $this->user ) {
			return $resolver->check( $name . $this->suffix, $groups );
		}

		return false;
	}
}
