<?php

interface Perms_Check
{
	function check( Perms_Resolver $resolver, array $context, $name, array $groups );
}
