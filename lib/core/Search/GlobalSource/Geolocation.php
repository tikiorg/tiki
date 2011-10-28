<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: Interface.php 33195 2011-03-02 17:43:40Z changi67 $

class Search_GlobalSource_Geolocation implements Search_GlobalSource_Interface
{
	function getData($objectType, $objectId, Search_Type_Factory_Interface $typeFactory, array $data = array())
	{
		$geolib = TikiLib::lib('geo');
		$coordinates = $geolib->get_coordinates($objectType, $objectId);

		if ($coordinates) {
			return array(
				'geo_located' => $typeFactory->identifier('y'),
				'geo_location' => $typeFactory->identifier(implode(',', $coordinates)),
			);
		} else {
			return array(
				'geo_located' => $typeFactory->identifier('n'),
				'geo_location' => $typeFactory->identifier(''),
			);
		}
	}

	function getProvidedFields()
	{
		return array(
			'geo_located',
			'geo_location',
		);
	}
	
	function getGlobalFields()
	{
		return array(
		);
	}
}

