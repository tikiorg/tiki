<?php

require_once 'lib/core/lib/Perms/Reflection/Object.php';

class Perms_Reflection_Category extends Perms_Reflection_Object
{
	function getParentPermissions() {
		return $this->factory->get( 'global', null )->getDirectPermissions();
	}
}
