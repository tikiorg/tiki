<?php
require_once 'lib/core/lib/Perms/Check.php';

class Perms_Check_Direct implements Perms_Check
{
	function check( Perms_Resolver $resolver, array $context, $name, array $groups ) {
		return $resolver->check( $name, $groups );
	}
}
