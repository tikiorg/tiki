<?php

interface Perms_Resolver
{
	function check( $permission, array $groups );
}

