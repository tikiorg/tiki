<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

function wikiplugin_sql_help() {
	return tra("Run a sql query").":<br />~np~{SQL(db=>dsnname)}".tra("sql query")."{SQL}~/np~";
}

function wikiplugin_sql_info() {
	return array(
		'name' => tra('SQL'),
		'documentation' => 'PluginSQL',
		'description' => tra('Query a MySQL database and display the results'),
		'prefs' => array( 'wikiplugin_sql' ),
		'body' => tra('The SQL query goes in the body. Example: SELECT column1, column2 FROM table'),
		'validate' => 'all',
		'icon' => 'pics/icons/database_table.png',
		'params' => array(
			'db' => array(
				'required' => true,
				'name' => tra('DSN Name'),
				'description' => tra('DSN name of the database being queried. The DSN name needs to first be defined at tiki-admin_dsn.php'),
				'default' => '',
			),
		),
	);
}

function wikiplugin_sql($data, $params) {

	global $tikilib;
	extract ($params,EXTR_SKIP);

	if (!isset($db)) {
		return tra('Missing db param');
	}

	$perms = Perms::get( array( 'type' => 'dsn', 'object' => $db ) );
	if ( ! $perms->dsn_query ) {
		return tra('You do not have permission to use this feature');
	}

	$bindvars = array();
	$data = html_entity_decode($data);
	if ($nb = preg_match_all("/\?/", $data, $out)) {
		foreach($params as $key => $value) {
			if (preg_match('/^[0-9]*$/', $key)) {
				if (strpos($value, "$") === 0) {
					$value = substr($value, 1);
					global $$value;
					$bindvars[$key] = $$value;
				}
				else {
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

	if ($db = $tikilib->get_db_by_name( $db ) ) {
		$result = $db->query( $data, $bindvars );
	} else {
		return '~np~' . tra('Could not obtain valid DSN connection.') . '~/np~';
	}
	
	$first = true;
	$class = 'even';
	while ($result && $res = $result->fetchRow() ) {
		if ($first) {
			$ret .= "<table class='normal'><thead><tr>";

			$first = false;

			foreach (array_keys($res)as $col) {
				$ret .= "<th>$col</th>";
			}

			$ret .= "</tr></thead>";
		}

		$ret .= "<tr>";

		if ($class == 'even') {
			$class = 'odd';
		} else {
			$class = 'even';
		}
	
		foreach ($res as $name => $val) {
			$ret .= "<td class='$class'>$val</td>";
		}
		$ret .= "</tr>";
	}

	if ($ret) {
		$ret .= "</table>";
	}
	if ($dbmsg) {
		$ret .= $dbmsg;
	}

	return '~np~' . $ret . '~/np~';
} 
