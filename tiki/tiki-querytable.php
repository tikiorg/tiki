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
<br/>
<?php

$arguments = Array('table','template','tableclass','where',
				   'columns','height',
				   'sort_column','sort_order','colalign','columnheadingclass',
				   'max_rows','offset','total','directpagination','combopagination');
					   
foreach($arguments as $arg) {
	$$arg = $_REQUEST["$arg"];
}					   
$acolalign = explode(',',$colalign);

if(!empty($where)) {
	$where = " where $where ";
}

$query = "select $columns from $table $where";
if(!empty($sort_column)) {
	$query.= " order by $sort_column $sort_order ";
}
$query.= " limit $offset,$max_rows";
$results = $tikilib->query($query);

if($total == 0) {
	$total = $tikilib->getOne("select count(*) from $table $where ");
}


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
print("<small>");
if($prev_offset>-1) {
  print("<a href='$prevhref' class='link'>[prev]</a> ");
}
print("($total records) Page: $actual_page/$cant_pages");
if($next_offset>-1) {
  print(" <a class='link' href='$nexthref'>[next]</a>");
} 
print("</small><br/>");
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
	print("</small><br/>");
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
	print("</select></form><br/>");
}

?>
<br/>
</div>
</body>
</html>