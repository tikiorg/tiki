<?php
/*
Experimental work not finished yet
An attempt to componentize all tables showing query results

Usage:
{querytable
	table =""   (Table or tables, example tiki_pages,users)
	template = "" (Template to be used for table rows, put them in templates/tables)

	where = "" (Where condition for the query)
	columns="col1,col2,col3" (Columns to be selected from the query, default = *)
	colalign="left,center,right" (Alignement for columns you can also use the template for this)
	sort_column = "col2" (Column to sort the data initially)
	sort_order = "desc" (Sort order)
	max_rows = "10" (Max number of rows to display per page)
	height = "" (Height for the table area)
	directpagination = "0" (Use directlinks to pages)
	combopagination = "0" (Use a combo to directly jump to a page)
	tableclass = "normal" (CSS class name for the table)
	columnheadingclass = "normal" (CSS class name for the columnheadings)
	oddrowclass = "odd"
	evenrowclass = "even"
}
*/


//SECURITY HERE!

function smarty_function_querytable($params, &$smarty) {
	global $tikilib;
	extract($params);
	//Security here
	$arguments = Array('table','template','tableclass','where',
					   'columns','height',
					   'sort_column','sort_order','colalign','columnheadingclass',
					   'max_rows','offset','total','directpagination',
					   'combopagination');
	
	if(!isset($table)) {return "Table is a mandatory argument to querytable plugin!";}
	if(!isset($template)) {return "Template is a mandatory argument to querytable plugin!";}
	if(!isset($tableclass)) $tableclass='normal';
	if(!isset($oddrowclass)) $oddrowclass="odd";
	if(!isset($where)) $where="";
	if(!isset($evenrowclass)) $evenrowclass="even";
	if(!isset($columns)) $columns='*';
	if(!isset($height)) $height=400;
	if(!isset($directpagination)) $directpagination=0;
	if(!isset($combopagination)) $combopagination=0;
	if(!isset($max_rows)) $max_rows = 20;
	if(!isset($offset)) $offset = 0;
	if(!isset($total)) $total = 0;
	if(!isset($sort_column)) $sort_column='';
	if(!isset($sort_order)) $sort_order='';
	if(!isset($colalign)) $colalign='left';
	if(!isset($columnheadingclass)) $columnheadingclass='heading';
	$columns = urlencode($columns);
	$output = "<iframe marginwidth='4px' marginheight='4px' width='100%' height='$height' frameborder='0' scrolling='auto' src='tiki-querytable.php?f=1";
	foreach($arguments as $arg) {
		$val = $$arg;
		$output.="&amp;$arg=$val";
	}
	$output.="'></iframe>";
	
	return $output;

}



?>
