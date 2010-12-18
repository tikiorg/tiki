<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: wikiplugin_tracker.php 29720 2010-09-29 13:57:55Z sylvieg $

// Test a tracker field value
// Usage:
// {TRACKERIF(test="(...)")}{ELSE}{TRACKERIF}
// test can be for example :
//      - (1=1)
//      - (1!=2)
//      - (2>1)
//      - (1<2)
//      - (3>=2)
//      - (2<=4)
//      - (f_xx=some_value) where xx is a field id in the tracker
//      - (f_xx=*value) f_xx must ends with value
//      - (f_xx=value*) f_xx must starts with value
//      - (f_xx=*value*) f_xx must contain value
//      - (&(f_xx=value)(f_yy=other_value)) test A and test B
//      - (|(f_xx=value)(f_yy=other_value)) test A or test B

require_once('lib/ldap/filter.php');
require_once('lib/trackers/trackerlib.php');

function wikiplugin_trackerif_info()
{
	return array(
		'name' => tra('Trackerif'),
		'documentation' => 'PluginTrackerif',
		'description' => tra('Display content based on results of a tracker field test'),
		'prefs' => array( 'feature_trackers', 'wikiplugin_tracker' ),
		'params' => array(
			'test' => array(
				'required' => true,
				'name' => tra('Test'),
				'description' => tra('Test'),
			),
			'ignore' => array(
				'required' => false,
				'name' => tra('Ignore'),
				'default' => 'y',
				'description' => tra('Ignore test in edit mode'),
			),
		),
	);
}

function wikiplugin_trackerif($data, $params)
{
        global $trklib;
	$test = null;
        $values = array();
        $dataelse = '';

	if (strpos($data,'{ELSE}')) {
                // Else bloc when test does not pass
		$dataelse = substr($data,strpos($data,'{ELSE}')+6);
		$data = substr($data,0,strpos($data,'{ELSE}'));
	}

        if (!isset($_REQUEST['trackerId']) || !isset($_REQUEST['itemId'])) {
		// Edit mode
		if (!isset($params['ignore']) || $params['ignore'] == 'y') {
			return $data;
		}

                return '';
        }

        try {
                // Parse test
                $test = LDAPFilter::parse($params['test']);
        } catch (Exception $e) {
                return $e->getMessage();
        }

        $xfields = $trklib->list_tracker_fields($_REQUEST["trackerId"]);

        foreach ($xfields['data'] as $field) {
                // Retrieve values from the current item
                $values[$field['fieldId']] = $trklib->get_item_value($_REQUEST['trackerId'], $_REQUEST['itemId'], $field['fieldId']);
        }

        if (!wikiplugin_trackerif_test($test, $values)) {
                if ($dataelse) {
                        return $dataelse;
                }

                return '';
        }

        return $data;
}

function wikiplugin_trackerif_test(LDAPFilter $test, array $values) {
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
                        $_a = null;
                        $_b = null;

                        if (preg_match('/f_([1-9]+)/', $matches[1], $matches_f) && isset($values[$matches_f[1]])) {
                                // Retrieve the field f_*
                                $_a = $values[$matches_f[1]];
                        } else {
                                $_a = '';
                        }

                        if (preg_match('/f_([1-9]+)/', $matches[3], $matches_f) && isset($values[$matches_f[1]])) {
                                // Retrieve the field f_*
                                $_b = $values[$matches_f[1]];
                        } else {
                                $_b = '';
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

function wikiplugin_trackerif_starts_with($haystack, $needle, $case=true) {
        if ($case) {
                return strcmp(substr($haystack, 0, strlen($needle)), $needle) === 0;
        }

        return strcasecmp(substr($haystack, 0, strlen($needle)), $needle) === 0;
}

function wikiplugin_trackerif_ends_with($haystack, $needle, $case=true) {
        if ($case) {
                return strcmp(substr($haystack, strlen($haystack) - strlen($needle)), $needle) === 0;
        }

        return strcasecmp(substr($haystack, strlen($haystack) - strlen($needle)), $needle) === 0;
}
