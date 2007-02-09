<?php

// Displays a list of users
// Use:
// {USERLIST(sep=>", ",max=>10,sort=>asc|desc,layout=>table)}substring{USERLIST}
//
// If no pattern is given returns all users or all that contain 'substring'

function wikiplugin_userlist_help() {
        return tra("Displays a list of registered users").":<br />~np~{USERLIST(sep=>\"SEPARATOR\",max=>MAXROWS,sort=>asc|desc,layout=>table)}substring{USERLIST}~/np~";
}

function wikiplugin_userlist($data, $params) {
    global $tikilib;
    global $userlib;

    extract ($params,EXTR_SKIP);

    if (!isset($sep)) $sep=', ';
    if (!isset($max)) { $numRows = -1; } else { $numRows = (int) $max; }

    if ($data) {
        $mid = '`login` like ?';
        $findesc = '%' . $data . '%';
	 $bindvars=array($findesc);
    } else {
        $mid = '1';
        $bindvars=array();
    }
    if (isset($sort)) {
    	$sort=strtolower($sort);
        if (($sort=='asc') || ($sort=='desc')) {
            $mid .= ' ORDER BY `login` '.$sort;
        }
    }
    $pre=''; $post='';
    if (isset($layout)) {
        if ($layout=='table') {
        	$pre='<table class=\'sortable\' id=\''.$tikilib->now.'\'><tr><th>'.tra('users').'</th></tr><tr><td>';
        	$sep = '</td></tr><tr><td>';
        	$post='</td></tr></table>';
        }
    }
    
    $query = 'select `login` from `users_users` where '.$mid;
    $result = $tikilib->query($query, $bindvars, $numRows);
    $ret = array();

    while ($row = $result->fetchRow()) {
        $ret[] = $row['login'];
    }
    return $pre.implode ( $sep, $ret ).$post;
}

?>
