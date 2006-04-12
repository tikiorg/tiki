<?php
function wikiplugin_sql_help() {
	return tra("Run a sql query").":<br />~np~{SQL(db=>dsnname)}".tra("sql query")."{SQL}~/np~";
}

function wikiplugin_sql($data, $params) {
	global $tikilib;

	extract ($params,EXTR_SKIP);

	if (!isset($db)) {
		return tra('Missing db param');
	}

	$perm_name = 'tiki_p_dsn_' . $db;
	global $$perm_name;

	if ($$perm_name != 'y') {
		return (tra('You do not have permission to use this feature'));
	}

	$bindvars = array();
	if ($nb = preg_match_all("/\?/", $data, $out)) {
		foreach($params as $key => $value) {
			if (ereg("^[0-9]*$", $key)) {
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

	if ($db == 'local') {
		$result = $tikilib->query($data,$bindvars);
	} else {

		$dsnsqlplugin = $tikilib->get_dsn_by_name($db);

		$parsedsn=$dsnsqlplugin;
		$dbdriver=strtok($parsedsn, ":");
		$parsedsn=substr($parsedsn,strlen($dbdriver)+3);
		$dbuserid=strtok($parsedsn, ":");
		$parsedsn=substr($parsedsn,strlen($dbuserid)+1);
		$dbpassword=strtok($parsedsn, "@");
		$parsedsn=substr($parsedsn,strlen($dbpassword)+1);
		$dbhost=strtok($parsedsn, "/");
		$parsedsn=substr($parsedsn,strlen($dbhost)+1);
		$database = $parsedsn;

		$dbsqlplugin = &ADONewConnection($dbdriver);
		if (!$dbsqlplugin) {
			$dberror = $dbsqlplugin->ErrorMsg();
            $dbmsg = "<div>$dberror</div>";
			$sql_oke = false;
		} else {
        		if (!$dbsqlplugin->NConnect($dbhost, $dbuserid, $dbpassword, $database)) {
					$dberror = $dbsqlplugin->ErrorMsg();
            	   	$dbmsg = "<div>$dberror</div>";
					$sql_oke = false;
				} else {
           			$result=$dbsqlplugin->Execute($data, $bindvars); 
					if (!$result) {
						$dberror = $dbsqlplugin->ErrorMsg();
               			$dbmsg = "<div>$dberror</div>";
						$sql_oke = false;
					}
				}
		}

	}

	$first = true;
	$class = 'even';

	while ($sql_oke && $res = $result->fetchRow()) {
		if ($first) {
			$ret .= "<div align='center'><table class='sortable'><tr>";

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
	if ($dbmsg) {
		$ret .= $dbmsg;
	}

	if ($db != 'local') {
		$dbsqlplugin->Close();
	}

	return $ret;
} 

?>
