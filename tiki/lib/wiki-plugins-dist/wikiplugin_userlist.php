<?php

// Displays a list of users
// Use:
// {USERLIST(sep=>", ",max=>10,sort=>asc|desc)}substring{USERLIST}
//
// If no pattern is given returns all users or all that contain 'substring'

function wikiplugin_userlist_help() {
        return tra("Displays a list of registered users").":<br />~np~{USERLIST(sep=>\"SEPARATOR\",max=>MAXROWS,sort=>asc|desc)}substring{USERLIST}~/np~";
}

function wikiplugin_userlist($data, $params) {
    global $tikilib;
    global $userlib;

    extract ($params,EXTR_SKIP);

    if (!isset($sep)) $sep=", ";
    if (!isset($max)) { $numRows = -1; } else { $numRows = (int) $max; }

    if ($data) {
        $mid = "`login` like ?";
        $findesc = "%" . $data . '%';
	 $bindvars=array($findesc);
    } else {
        $mid = "1";
        $bindvars=array();
    }
    if (isset($sort)) {
    	$sort=strtolower($sort);
        if (($sort=="asc") || ($sort=="desc")) {
            $mid .= " ORDER BY `login` ".$sort;
        }
    }
    
    $query = "select `login` from `users_users` where ".$mid;
    $result = $tikilib->query($query, $bindvars, $numRows);
    $ret = array();

    while ($row = $result->fetchRow()) {
        $ret[] = $row['login'];
    }
    return implode ( $sep, $ret );
}

?>
