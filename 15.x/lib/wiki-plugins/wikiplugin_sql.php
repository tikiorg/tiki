<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_sql_info()
{
	return array(
		'name' => tra('SQL'),
		'documentation' => 'PluginSQL',
		'description' => tra('Query a MySQL database and display the results'),
		'prefs' => array( 'wikiplugin_sql' ),
		'body' => tr('The SQL query goes in the body. Example: ') . '<code>SELECT column1, column2 FROM table</code>',
		'validate' => 'all',
		'iconname' => 'database',
		'introduced' => 1,
		'params' => array(
			'db' => array(
				'required' => true,
				'name' => tra('DSN Name'),
				'description' => tr('DSN name of the database being queried. The DSN name needs to first be defined at
					%0', '<code>tiki-admin_dsn.php</code>'),
				'since' => '1',
				'default' => ''
			),
			'raw' => array(
				'required' => false,
				'name' => tra('Raw return'),
				'description' => tra('Return with table formatting (default) or raw data with no table formatting'),
				'since' => '11.0',
				'default' => '0',
				'filter' => 'digits',
				'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('Normal'), 'value' => '0'),
					array('text' => tra('Raw'), 'value' => '1')
				)
			),
			'delim' => array(
				'required' => false,
				'name' => tra('Delim'),
				'description' => tr('The delimiter to be used between data elements (sets %0)', '<code>raw=1</code>'),
				'since' => '11.0',
			),
			'wikiparse' => array(
				'required' => false,
				'name' => tra('Wiki Parse'),
				'description' => tr('Turn wiki parsing of select results on and off (default is on)'),
				'since' => '11.0',
				'default' => '1',
				'filter' => 'digits',
				'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('Off'), 'value' => '0'),
					array('text' => tra('On'), 'value' => '1')
				)
			)
		)
	);
}

function wikiplugin_sql($data, $params)
{

	global $tikilib;
	extract($params, EXTR_SKIP);

	if (!isset($db)) {
		return tra('Missing db param');
	}

	$perms = Perms::get(array( 'type' => 'dsn', 'object' => $db ));
	if ( ! $perms->dsn_query ) {
		return tra('You do not have permission to use this feature');
	}

	$bindvars = array();
	$data = html_entity_decode($data);
	if ($nb = preg_match_all("/\?/", $data, $out)) {
		foreach ($params as $key => $value) {
			if (preg_match('/^[0-9]*$/', $key)) {
				if (strpos($value, "$") === 0) {
					$value = substr($value, 1);
					global $$value;
					$bindvars[$key] = $$value;
				} else {
					$bindvars[$key] = $value;
				}
			}
		}
		if (count($bindvars) != $nb) {
			return tra('Missing db param');
		}
	}

	$ret = '';
	$sql_oke = true;
 	$dbmsg = '';

	if ($db = $tikilib->get_db_by_name($db) ) {
		$result = $db->query($data, $bindvars);
	} else {
		return '~np~' . tra('Could not obtain valid DSN connection.') . '~/np~';
	}

	$setup_table = ( isset( $raw ) or isset( $delim ) ) ? false : true;
	$class = 'even';
	while ($result && $res = $result->fetchRow() ) {
		if ( $setup_table ) {
			$ret .= "<table class='normal'><thead><tr>";

			$setup_table = false;

			foreach (array_keys($res)as $col) {
				$ret .= "<th>$col</th>";
			}

			$ret .= "</tr></thead>";
		}

		if ( !isset( $raw ) && !isset( $delim ) ) {
			$ret .= "<tr>";
		}

		if ($class == 'even') {
			$class = 'odd';
		} else {
			$class = 'even';
		}

		$first_field = true;
		foreach ($res as $name => $val) {
			if ( isset( $delim ) && !$first_field ) {
				$ret .= $delim;
			}

			if ( isset( $raw ) || isset( $delim ) ) {
				$ret .= "$val";
			} else {
				$ret .= "<td class=\"$class\">$val</td>";
			}

			$first_field = false;
		}

		if ( !isset( $raw ) && !isset( $delim ) ) {
			$ret .= "<tr>";
		} elseif ( isset( $delim ) ) {
			$ret .= "<br>";
		}
	}

	if ($ret && !isset( $raw )) {
		$ret .= "</table>";
	}
	if ($dbmsg) {
		$ret .= $dbmsg;
	}

	if ($wikiparse) {
		return $ret;
	} else {
		return '~np~' . $ret . '~/np~';
	}
}
