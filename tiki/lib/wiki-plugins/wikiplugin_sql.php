<?php
function wikiplugin_sql_help() {
	return tra("Run a sql query").": ~np~{SQL db=>}".tra("sql query")."{SQL}~/np~";
}

function wikiplugin_sql($data, $params) {
	global $tikilib;

	extract ($params);

	if (!isset($db)) {
		return tra('Missing db param');
	}

	$perm_name = 'tiki_p_dsn_' . $db;
	global $$perm_name;

	if ($$perm_name != 'y') {
		return ('');
	}

	$ret = '';
	$dsn = $tikilib->get_dsn_by_name($db);
	$dbPlugin = DB::connect($dsn);

	if (DB::isError($dbPlugin)) {
		return ($dbPlugin->getMessage());
	}

	@$result = $dbPlugin->query($data);

	if (DB::isError($result))
		return $result->getMessage();

	$first = true;
	$class = 'even';

	while ($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
		if ($first) {
			$ret .= "<div align='center'><table class='normal'><tr>";

			$first = false;

			foreach (array_keys($res)as $col) {
				$ret .= "<td class='heading'>$col</td>";
			}

			$ret .= "</tr>";
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
		$ret .= "</table></div>";
	}

	return $ret;
}

?>