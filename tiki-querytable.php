<?php
include_once('tiki-setup.php');
?>
<!DOCTYPE html 
     PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> 
    <link rel="StyleSheet"  href="styles/<?=$style?>" type="text/css" />
  </head>
  <body>
<div align="center" style="width:100%;height:100%">  
<br />
<?php

$arguments = Array('table','template','tableclass','where',
				   'columns','height',
				   'sort_column','sort_order','colalign','columnheadingclass',
				   'max_rows','offset','directpagination','combopagination',
				   'searchfields','searchwords','deco');


					   
foreach($arguments as $arg) {
	$$arg = $_REQUEST["$arg"];
}					   

if($deco != $_SESSION['deco']) {
	print(tra('Security check failed!'));
	die;
}
$deco = md5(uniqid(rand()));
$_SESSION['deco'] = $deco;

$acolalign = explode(',',$colalign);


$whereq = '';
if(!empty($where)) {
	$whereq = " where $where ";
}


if(!empty($searchfields) && !empty($searchwords)) {
	//We have something to find
	$search_cols = explode(',',$searchfields);
	$words = explode(',',$searchwords);
	$fq = Array();
	foreach($search_cols as $col) {
 		foreach($words as $word) {
			$fq[] = " $col like '%$word%' ";
		}
	}
	$fq = implode(' or ',$fq);
	if(empty($where)) {
		$whereq = " where $fq ";
	} else {
		$whereq.= " and ($fq) ";
	}
}

$query = "select $columns from $table $whereq";
if(!empty($sort_column)) {
	$query.= " order by $sort_column $sort_order ";
}
$query.= " limit $offset,$max_rows";


// QUERY THE DATABASE HERE
$results = $tikilib->query($query);

$total = $tikilib->getOne("select count(*) from $table $whereq ");



// Pagination calculation
// If there're more records then assign next_offset
$cant_pages = ceil($total / $max_rows);
$actual_page = 1 + ($offset / $max_rows);
if ($total > ($offset + $max_rows)) {
	$next_offset = $offset + $max_rows;
} else {
	$next_offset = -1;
}
// If offset is > 0 then prev_offset
if ($offset > 0) {
	$prev_offset = $offset - $max_rows;
} else {
	$prev_offset = -1;
}


$old_offset = $offset;
$firsthref = "tiki-querytable.php?f=1";
$offset = 0;
foreach($arguments as $arg) {
		$val = $$arg;
		$firsthref.="&amp;$arg=$val";
}

$lasthref = "tiki-querytable.php?f=1";
$offset = $total-$max_rows+1;
foreach($arguments as $arg) {
		$val = $$arg;
		$lasthref.="&amp;$arg=$val";
}


$prevhref = "tiki-querytable.php?f=1";
$offset = $prev_offset;
foreach($arguments as $arg) {
		$val = $$arg;
		$prevhref.="&amp;$arg=$val";
}

$nexthref = "tiki-querytable.php?f=1";
$offset = $next_offset;
foreach($arguments as $arg) {
		$val = $$arg;
		$nexthref.="&amp;$arg=$val";
}



print("<table class='normal'><tr>");
if(!empty($searchfields)) {
	print("<td colspan='99'><form id='find' method='get' action='tiki-querytable.php'>");
	$offset = 0;
    foreach($arguments as $arg) {
      $val = $$arg;
      if($arg != 'searchwords') {
	  	print("<input type='hidden' name='$arg' value='$val' />\n");
	  }
    }
    print(tra('find').': '."<input type='text' name='searchwords' value='$searchwords' />");
	print("<input type='submit' value='".tra('find')."' /></form></td>");
}
print("<td style='text-align:right;'>");
print("($total records) ");
print("<a href='$firsthref'><img src='img/icons2/nav_first.gif' border='0' /></a>");
if($prev_offset>=0) {
	print("<a href='$prevhref'><img src='img/icons2/nav_dot_right.gif' border='0' /></a>");
} else {
	print("<img src='img/icons2/nav_dot_right.gif' border='0' />");
}	
print(" $actual_page/$cant_pages ");
if($next_offset>0) {
	print("<a href='$nexthref'><img src='img/icons2/nav_dot_left.gif' border='0' /></a>");
} else {
	print("<img src='img/icons2/nav_dot_left.gif' border='0' />");
}
print("<a href='$lasthref'><img src='img/icons2/nav_last.gif' border='0' /></a>");
print("</td></tr></table>");

$offset = $old_offset;

print("<table class='$tableclass' />\n");

$first = true;
$i=0;
$old_sort_column = $sort_column;
$offset=0;
while($res = $results->fetchRow()) {
	if($first) {
		$first = false;
		print("  <tr>\n");
		$j=0;
		foreach(array_keys($res) as $name) {
			if(isset($acolalign[$j])) {
				$st = 'style="text-align:'.$acolalign[$j].';"';
			} else {
				$st ='';
			}
			print("    <td class='$columnheadingclass' $st >");
			if($old_sort_column == $name) {
				if($sort_order == 'desc') {
					$sort_order = 'asc';
				} else {
					$sort_order = 'desc';
				}
			}
			print("<a class='tableheading' href='tiki-querytable.php?f=1");
			$sort_column = $name;
			foreach($arguments as $arg) {
				$val = $$arg;
				print("&amp;$arg=$val");
			}					   
			print("'>$name</a>");
			print("</td>\n");
			$j++;
		}
		print("  </tr>\n");
	}
	print("  <tr>\n");
	$j=0;
	$class = $i%2 ? "even":"odd";
	$smarty->assign('class',$class);
	foreach($res as $colname => $colval) {
		$smarty->assign("$colname",$colval);
	}
	$data = $smarty->fetch($template);
	print($data);
	print("  </tr>\n");
	$i++;
}
print("</table>\n");

/*
print("<small>");
if($prev_offset>-1) {
  print("<a href='$prevhref' class='link'>[prev]</a> ");
}
print("($total records) Page: $actual_page/$cant_pages");
if($next_offset>-1) {
  print(" <a class='link' href='$nexthref'>[next]</a>");
} 
print("</small><br />");
*/
if($directpagination == 1 && $cant_pages < 50) {
	print("<small>");
	for($i=0;$i<$cant_pages;$i++) {
		$page = $i+1;
	    $href = "tiki-querytable.php?f=1";
        $offset = $i*$max_rows;
        foreach($arguments as $arg) {
		  $val = $$arg;
		  $href.="&amp;$arg=$val";
        }
		
		print(" <a href='$href' class='link'>");
		if($actual_page == $page) {
			print("[$page]");
		} else {
			print("$page");
		}
		print("</a> ");
	}
	print("</small><br />");
}

if($combopagination == 1 && $cant_pages < 550) {
	print("<form id='combopag' method='get' action='tiki-querytable.php'>");
    foreach($arguments as $arg) {
     $val = $$arg;
	 print("<input type='hidden' name='$arg' value='$val' />");
    }
    print("<small>Go to:</small> <select onChange='document.getElementById(\"combopag\").submit();' name='offset'>");
	for($i=0;$i<$cant_pages;$i++) {
		$page = $i+1;
        $offset = $i*$max_rows;
		print("<option value='$offset'");
		if($actual_page == $page) {
			print(" selected='selected' ");
		}
		print(">$page</option> ");
	}
	print("</select></form><br />");
}

?>
<br />
</div>
</body>
</html>