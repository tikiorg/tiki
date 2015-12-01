<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * Handler class for kaltura video integration
 *
 * Letter key: ~kaltura~
 *
 */
class Tracker_Field_Kaltura extends Tracker_Field_Abstract implements Tracker_Field_Synchronizable
{
	public static function getTypes()
	{
		return array(
			'kaltura' => array(
				'name' => tr('Kaltura Video'),
				'description' => tr('Displays a series of attached Kaltura videos.'),
				'help' => 'Kaltura',
				'prefs' => array('trackerfield_kaltura', 'feature_kaltura', 'wikiplugin_kaltura'),
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
			$value = implode(',', $requestData[$this->getInsertId()]);
		} else {
			$value = $this->getValue();
		}

		return array(
			'value' => $value,
		);
	}

	function renderInput($context = array())
	{
		$kalturalib = TikiLib::lib('kalturauser');
		$movies = array_filter(explode(',', $this->getValue()));

		$movieList = $kalturalib->getMovieList($movies);
		$extra = array_diff(
			$movies,
			array_map(
				function ($movie) {
					return $movie['id'];
				},
				$movieList
			)
		);
		return $this->renderTemplate(
			'trackerinput/kaltura.tpl',
			$context,
			array(
				'movies' => $movieList,
				'extras' => $extra,
			)
		);
	}

	function renderOutput($context = array())
	{
		return $this->renderTemplate(
			'trackeroutput/kaltura.tpl',
			$context,
			array(
				'movieIds' => array_filter(explode(',', $this->getValue())),
			)
		);
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
}

