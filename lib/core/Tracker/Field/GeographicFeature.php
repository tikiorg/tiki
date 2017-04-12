<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * Handler class for geographic features (points, lines, polygons)
 * 
 * Letter key: ~GF~
 *
 */
class Tracker_Field_GeographicFeature extends Tracker_Field_Abstract implements Tracker_Field_Synchronizable, Tracker_Field_Indexable
{
	public static function getTypes()
	{
		return array(
			'GF' => array(
				'name' => tr('Geographic Feature'),
				'description' => tr('Stores a geographic feature on a map.'),
				'help' => 'Location Tracker Field',
				'prefs' => array('trackerfield_geographicfeature'),
				'tags' => array('advanced'),
				'default' => 'n',
				'params' => array(
				),
			),
		);
	}

	function getFieldData(array $requestData = array())
	{
		if (isset($requestData[$this->getInsertId()])) {
			$value = $requestData[$this->getInsertId()];
		} else {
			$value = $this->getValue();
		}

		return array(
			'value' => $value,
		);
	}

	function renderInput($context = array())
	{
		return tr('Feature cannot be set or modified through this interface.');
	}

	function renderOutput($context = array())
	{
		return tr('Feature cannot be viewed.');
	}

	function importRemote($value)
	{
		return $value;
	}

	function exportRemote($value)
	{
		return $value;
	}

	function importRemoteField(array $info, array $syncInfo)
	{
		return $info;
	}

	function getDocumentPart(Search_Type_Factory_Interface $typeFactory)
	{
		return array(
			'geo_located' => $typeFactory->identifier('y'),
			'geo_feature' => $typeFactory->identifier($this->getValue()),
			'geo_feature_field' => $typeFactory->identifier($this->getConfiguration('permName')),
		);
	}

	function getProvidedFields()
	{
		return array('geo_located', 'geo_feature', 'geo_feature_field');
	}

	function getGlobalFields()
	{
		return array();
	}
}

