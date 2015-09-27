<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once('lib/ldap/filter.php');

function wikiplugin_trackerif_info()
{
	return array(
		'name' => tra('Tracker If'),
		'documentation' => 'PluginTrackerIf',
		'description' => tra('Display content based on results of a tracker field test'),
		'prefs' => array( 'wikiplugin_trackerif', 'feature_trackers', 'wikiplugin_tracker' ), // ML: is wikiplugin_tracker necessary?
		'iconname' => 'trackers',
		'introduced' => 7,
		'defaultfilter' => 'wikicontent',
		'params' => array(
			'test' => array(
				'required' => true,
				'name' => tra('Test'),
				'description' => tra('Test'),
				'since' => '7.0',
			),
			'ignore' => array(
				'required' => false,
				'name' => tra('Ignore'),
				'default' => 'y',
				'description' => tra('Ignore test in edit mode'),
				'since' => '7.0',
				'filter' => 'alpha',
				'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('Yes'), 'value' => 'y'),
					array('text' => tra('No'), 'value' => 'n')
				)
			),
		),
	);
}

function wikiplugin_trackerif ($data, $params)
{
	$trklib = TikiLib::lib('trk');
	$test = null;
	$values = array();
	$dataelse = '';

	if (strpos($data, '{ELSE}')) {
		// Else bloc when test does not pass
		$dataelse = substr($data, strpos($data, '{ELSE}')+6);
		$data = substr($data, 0, strpos($data, '{ELSE}'));
	}

	if (empty($_REQUEST["trackerId"])) {
                $trackerId = $trklib->get_tracker_for_item($_REQUEST['itemId']);
	} else {
		$trackerId = $_REQUEST["trackerId"];
	}

	if (!$trackerId || !isset($_REQUEST['itemId'])) {
		// Edit mode
		if (!isset($params['ignore']) || $params['ignore'] == 'y') {
			return $data;
		}

		return $dataelse;
	}

	try {
		// Parse test
		$test = LDAPFilter::parse($params['test']);
	} catch (Exception $e) {
		return $e->getMessage();
	}

	$xfields = $trklib->list_tracker_fields($trackerId);

	foreach ($xfields['data'] as $field) {
		// Retrieve values from the current item
		$values[$field['fieldId']] = $trklib->get_item_value($trackerId, $_REQUEST['itemId'], $field['fieldId']);
	}

	if (!wikiplugin_trackerif_test($test, $values)) {
		if ($dataelse) {
			return $dataelse;
		}

		return '';
	}

	return $data;
}

function wikiplugin_trackerif_test(LDAPFilter $test, array $values)
{
	$return = true;

	if ($test->_subfilters != null) {
		// Current filter is not a leaf
		foreach ($test->_subfilters as $subfilter) {
			switch ($test->_match) {
				case '&':
					// And
					$return &= wikiplugin_trackerif_test($subfilter, $values);
    				break;
				case '|':
					// Or
					$return |= wikiplugin_trackerif_test($subfilter, $values);
    				break;
			}
		}

	} else if ($test->_filter != null) {
		// a operator b
		preg_match('/^\(\'?([^!\']*)\'?(=|!=|<|<=|>|>=)\'?([^\']*)\'?\)$/', $test->_filter, $matches);

		if (count($matches) == 4) {
			$_a = $matches[1];
			$_b = $matches[3];

			if (preg_match('/f_([0-9]+)/', $matches[1], $matches_f)) {
				// Retrieve the field f_*
				$_a = isset( $values[$matches_f[1]] ) ? $values[$matches_f[1]] : '';
			}

			if (preg_match('/f_([0-9]+)/', $matches[3], $matches_f)) {
				// Retrieve the field f_*
				$_b = isset( $values[$matches_f[1]] ) ? $values[$matches_f[1]] : '';
			}

			switch ($matches[2]) {
				case '=':
					$_begins = wikiplugin_trackerif_ends_with($_b, '*');
					$_ends = wikiplugin_trackerif_starts_with($_b, '*');
					$_b = str_replace('*', '', $_b);

					if ($_begins) {
						if ($_ends) {
							return preg_match('/'. $_b . '/', $_a);
						}
						return preg_match('/^'. $_b . '/', $_a);

					} else if ($_ends) {
						return preg_match('/'. $_b . '$/', $_a);
					}

					return $_a == $_b;
				case '!=':
					return $_a != $_b;
				case '<':
					return $_a < $_b;
				case '<=':
					return $_a <= $_b;
				case '>':
					return $_a > $_b;
				case '>=':
					return $_a >= $_b;
			}
		}
	}

	return $return;
}

function wikiplugin_trackerif_starts_with($haystack, $needle, $case=true)
{
	if ($case) {
		return strcmp(substr($haystack, 0, strlen($needle)), $needle) === 0;
	}

	return strcasecmp(substr($haystack, 0, strlen($needle)), $needle) === 0;
}

function wikiplugin_trackerif_ends_with($haystack, $needle, $case=true)
{
	if ($case) {
		return strcmp(substr($haystack, strlen($haystack) - strlen($needle)), $needle) === 0;
	}

	return strcasecmp(substr($haystack, strlen($haystack) - strlen($needle)), $needle) === 0;
}
