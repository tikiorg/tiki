<?php

interface Search_GlobalSource_Interface
{
	function getData($objectType, $objectId, Search_Type_Factory_Interface $typeFactory, array $data = array());

	function getProvidedFields();
	
	function getGlobalFields();
}

