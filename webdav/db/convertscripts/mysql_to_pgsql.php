<?php

// Set tikiversion variable
require 'tikiversion.php';
if(!isset($_GET['version'])) {
	echo "version not given. Using default $tikiversion.<br />";
} else {
	if(preg_match('/\d\.\d/',$_GET['version'])) {
		$tikiversion=$_GET['version'];
	}
}


// read file
$file="../tiki.sql";
@$fp = fopen($file,"r");
if(!$fp)
{
	echo "Error opening $file";
	exit();
}
$data = '';
echo "reading $file: ";
while(!feof($fp)) {
	$data .= fread($fp,4096);
	echo ".";
}
fclose($fp);
echo "<br />\n";


// split into statements
$statements = preg_split("#(;\n)|(;\r\n)#", $data);

// step though statements
$fp=fopen($tikiversion.".to_pgsql.sql","w");
foreach ($statements as $statement)
{
	$parsed = parse($statement);
	fwrite($fp, $parsed);
}
fclose($fp);


/** parse MySQL statements and convert to PostgreSQL statements
 * return parsed string
 */
function parse($stmt)
{
	// variable for statements that have to be appended
	global $poststmt;
	$poststmt = "\n";
	
	
	// Replace MySQL specific quotes with SQL quotes (`with ") – these mark table- and columnames
	$stmt = preg_replace('/`/', '"', $stmt);
	
	// add missing quotation
	$stmt = preg_replace('/TABLE ([a-z0-9_]+) ?\(/i', 'TABLE "$1" (', $stmt);
	//$stmt = preg_replace('/(UNIQUE \()(.*)(\))/e', 'add_quotation("$1", "$2", "$3")', $stmt);
	// unique
	$stmt = preg_replace('/UNIQUE (KEY )? ?"?[a-z0-9_]*"? ?\((.*)\)/ie', 'stripParanthesisWithNumbers("UNIQUE (", "$2", ")")', $stmt);
	$stmt = preg_replace('/UNIQUE (KEY )? ?"?[a-z0-9_]*"? ?\((.*)\)/ie', 'add_quotation("UNIQUE (", "$2", ")")', $stmt);
	// index
	$stmt = preg_replace('/(\n[ \t]*)INDEX ("?[a-z0-9_]*"? ?)?\((.*)\)/ie', 'stripParanthesisWithNumbers("$1INDEX $2(", "$3", ")")', $stmt);
	$stmt = preg_replace('/(\n[ \t]*)INDEX ("?[a-z0-9_]*"? ?)?\((.*)\)/ie', 'add_quotation("$1INDEX $2(", "$3", ")")', $stmt);
	// key
	$stmt = preg_replace('/(^PRIMARY |^FOREIGN )KEY ("?([a-z0-9_]+)"? )?\((.*)\)/ie', 'stripParanthesisWithNumbers("KEY \"$2\"(", "$3", ")")', $stmt);
	$stmt = preg_replace('/(^PRIMARY |^FOREIGN )KEY ("[a-z0-9_]+" )?\((.*)\)/ie', 'add_quotation("KEY $1(", "$2", ")")', $stmt);
	$stmt = preg_replace('/PRIMARY KEY ?\((.*)\)/ie', 'stripParanthesisWithNumbers("PRIMARY KEY (", "$1", ")")', $stmt);
	$stmt = preg_replace('/PRIMARY KEY ?\((.*)\)/ie', 'add_quotation("PRIMARY KEY (", "$1", ")")', $stmt);
	
	$stmt = preg_replace('/WHERE "?([a-z0-9_]+)"?/i', 'WHERE "$1"', $stmt);
	
	
	
	// record table names
	$stmt = preg_replace('/(DROP TABLE IF EXISTS |DROP TABLE |CREATE TABLE )"?([a-zA-Z0-9_]+)"? \(/e', 'sprintf("%s\"%s\" (", "$1", record_tablename("$2"))', $stmt);
	
	// in key declarations, remove length if there
	$stmt = preg_replace(
		"/ KEY (\"[a-zA-Z0-9_]+\" )?\((.*)\)/e",
		'\' KEY (\' . strip_paranthesisWithNumbers("$2") . \')\'',
		$stmt);
	
	// drop ENGINE=MyISAM and AUTO_INCREMENT=1
	$stmt=preg_replace('/ ENGINE ?= ?MyISAM/', '', $stmt);
	$stmt=preg_replace('/ AUTO_INCREMENT=1/', '', $stmt);
	
	
	//auto_increment things
	$stmt=preg_replace("/mediumint\(\d\) (unsigned )?NOT NULL auto_increment/i", "serial", $stmt);
	$stmt=preg_replace("/int(eger)? NOT NULL auto_increment/i", "bigserial", $stmt);
	$stmt=preg_replace("/int(eger)?\(\d\) (unsigned )?NOT NULL auto_increment/i", "serial", $stmt);
	$stmt=preg_replace("/int(eger)?\(\d\d\) (unsigned )?NOT NULL auto_increment/i", "bigserial", $stmt);
	
	
	// integer types
	$stmt=preg_replace("/tinyint\([1-4]\)( unsigned)?/i", "smallint", $stmt);
	$stmt=preg_replace("/mediumint\([5-9]\)( unsigned)?/i", "integer", $stmt);
	$stmt=preg_replace("/int(eger)?\([1-4]\)( unsigned)?/i","smallint",$stmt);
	$stmt=preg_replace("/int(eger)?\([5-9]\)( unsigned)?/i","integer",$stmt);
	$stmt=preg_replace("/int(eger)?\(\d\d\)( unsigned)?/i","bigint",$stmt);
	
	// timestamps
	$stmt=preg_replace("/timestamp\([^\)]+\)/", 'timestamp(3)', $stmt);
	$stmt=preg_replace("/(\n[ \t]*)\"?([a-zA-Z0-9_]+)\"? datetime/", '$1"$2" timestamp(3)', $stmt);
	
	// blobs
	$stmt=preg_replace("/longblob|tinyblob|blob/", "bytea", $stmt);
	
	// text fields
	$stmt = preg_replace("/tinytext/i", "text", $stmt);
	$stmt = preg_replace("/mediumtext/i", "text", $stmt);
	$stmt = preg_replace("/longtext/i", "text", $stmt);
	
	// todo: do this in a check constraint, similiar to enums
	$stmt = preg_replace("/SET ?\(.*\)/i", "text", $stmt);
	
	// convert enums
	$stmt=preg_replace("/\n[ \t]*\"?([a-zA-Z0-9_]+)\"? enum ?\((.*)\)/ie", "convert_enums('$1','$2')", $stmt);
	
	
	// foreign keys
	//	before: CONSTRAINT tablename \n FOREIGN KEY (colname) REFERENCES tablename(colname) \n ON UPDATE CASCADE ON DELETE SET NULL
	$stmt = preg_replace(
		"/CONSTRAINT ([a-zA-Z0-9_]+)\n[ \t]+FOREIGN KEY \(([a-zA-Z0-9_]+)\) REFERENCES ([a-zA-Z0-9_]+) ?\(([a-zA-Z0-9_]+)\)/",
		"FOREIGN KEY ('$2') REFERENCES $3 ($4)",
		$stmt);
	
	
	// TODO: Postgres does support FULLTEXT indexing since recent version. Add it here later…
	// Work arounds for this include adding the tsearch2 module to postgres and other drastic changes.
	//$stmt=preg_replace("/,\n[ \t]+FULLTEXT KEY ([a-zA-Z0-9_]+) \((.+)\)/e","create_index('$1','$2')",$stmt);
	// remove text indices
	$stmt = preg_replace("/,\n[ \t]+FULLTEXT KEY (\"[a-zA-Z0-9_]+\" )?\((.+)\)/", '', $stmt);
	
	// convert UNIQUE KEY to UNIQUE		// TODO: is unique automatically indexed in pgsql? Maybe add index as well.
	$stmt = preg_replace(
		"/,\n([ \t]+)UNIQUE KEY (\"[a-zA-Z0-9_]+\" )?\((.*)\)/e",
		'",\n$1UNIQUE (".strip_paranthesisWithNumbers("$3").")"',
		$stmt);
	
	// explicit create index
	$stmt=preg_replace(
		"/CREATE INDEX \"?([a-z0-9_]+)\"? ON \"?([a-z0-9_]+)\"? \((.*)\)/ei",
		"create_explicit_index('$1','$2','$3')",
		$stmt);
	
	// create indexes from KEY …
	$stmt = preg_replace("/,\n[ \t]*INDEX \"?([a-zA-Z0-9_]+)\"? ?\((.+)\)/e", 'create_index("$1", "$2")', $stmt);
	$stmt = preg_replace("/,\n[ \t]*INDEX ?\((.+)\)/e",                       'create_index("", "$1")', $stmt);
	$stmt = preg_replace("/,\n[ \t]*KEY \"?([a-zA-Z0-9_]+)\"? ?\((.+)\)/e",   'create_index("$1", "$2")', $stmt);
	$stmt = preg_replace("/,\n[ \t]*KEY ?\((.+)\)/e",                         'create_index("", "$1")', $stmt);
	
	
	// convert inserts
	$stmt = preg_replace("/INSERT (IGNORE )?INTO \"?([a-zA-Z0-9_]*)\"? ?\((.*)\) ?VALUES ?\((.*)\)/ie", "do_inserts('$2', \"$3\", '$4')", $stmt);
	
	// convert updates
	$stmt=preg_replace("/update ([a-zA-Z0-9_]+) set (.*)/e","do_updates('$1','$2')",$stmt);
	$stmt=preg_replace("/UPDATE ([a-zA-Z0-9_]+) set (.*)/e","do_updates('$1','$2')",$stmt);
	
	// clean cases where UNIQUE was alone at the end: remove commas at the end of table definition
	$stmt=preg_replace("/,( *)\)/","$1)",$stmt);
	
	$poststmt .= "\n";
	return $stmt.";".$poststmt;
}

function stripParanthesisWithNumbers($pre, $txt, $post)
{
	$txt = preg_replace("/\(\d+\)/U", '', $txt);
	return $pre.$txt.$post;
}
function strip_paranthesisWithNumbers($txt)
{
	$txt = preg_replace("/\(\d+\)/", '', $txt);
	return $txt;
}

/**
 * @param $pre prefix – preceding text
 * @param $str list of columns or table name
 * @param $post postfix – text behind
 * @return complete string with quoted columns/tablename
 */
function add_quotation($pre, $str, $post)
{
	$cols = split(',', $str);
	$str = '';
	foreach($cols AS $col)
	{
		$col = trim($col);
		if(substr($col, 0, 1) != '"')
		{
			$col = '"'.$col.'"';
		}
		$str .= $col;
	}
	$str = preg_replace('/""/', '","', $str);
	return $pre.$str.$post;
}

function replace_apostroph_in_string($str){
	//$str = preg_replace("/'(.*)\\\\'(.*)\\\\'(.*)'/U", "'$1''$2''$3'", $str);
	// \\ regex, \\ in "-quoted string. Thus \\\\
	$str = preg_replace("/\\\\'/U", "''", $str);
	$str = preg_replace('/\\\\"/U', '"', $str);
	return $str;
}

function record_tablename($tabnam)
{
	global $table_name;
	$table_name = $tabnam;
	return $tabnam;
}

function create_explicit_index($name, $table_name, $content, $type='')
{
	$cols=split(",",$content);
	$allvals="";
	foreach ($cols as $vals) {
	$vals=preg_replace("/([a-zA-Z0-9_]+)/","\"$1\"",$vals);

	// Do var(val) conversion to substr(var, 0, val); since that's what is expected for these indexes
	$vals=preg_replace("/([\"a-z0-9_]+) *\(\"([0-9]+)\"\)/i","substr($1, 0, $2)",$vals);

	$allvals.=$vals;
	}
	// Put commas between elements.
	$allvals=preg_replace("/\"\"/","\",\"",$allvals);
	$allvals=preg_replace("/\"substr/","\",substr",$allvals);
	$allvals=preg_replace("/(substr\(.*\))\"/","$1,\",substr",$allvals);

	return("CREATE " . ( !empty($type)?$type.' ' : '' ) . "INDEX \"" . $name . "\" ON \"" . $table_name . "\" (" . $allvals . ");\n");
}

/**
 * create an index
 * SQL command will be added to global $poststmt
 * @param $name name of index
 * @param $content comma-seperated list of columns the index is created for
 * @param $type optional: index type
 */
function create_index($name, $columnlist, $type='')
{
	global $table_name;
	global $poststmt;
	
	$columnlist = str_replace('"', '', $columnlist);
	$cols = split(',', $columnlist);
	
	// if the index has no name, give it one – based on columns
	if(empty($name))
	{
		$name = $table_name;
		for($i=0; $i<count($cols); $i++)
		{
			$name .= '_' . $cols[$i];
		}
	}
	
	// trim column names and add quotes
	$allvals = '';
	foreach ($cols AS $col) {
		// strip whitespaces
		$col = trim($col);
		
		// add quotes if column is not quoted
		if(substr($col, 0, 1) != '"')
		{
			//$col = preg_replace('/\"?([a-zA-Z0-9_]+)\"?/', '"$1"', $col);
			$col = '"'.$col.'"';
		}
		
		// TODO: index textlengths were removed above. Is it possible to use it this way, with the pgsql substr method? Does it remember it's a fn and use it when building indices?
		// Do var(val) conversion to substr(var, 0, val); since that's what is expected for these indexes
		// $col = preg_replace("/([\"a-z0-9_]+) *\(\"([0-9]+)\"\)/i","substr($1, 0, $2)",$col);
		$allvals .= $col;
	}
	// Put commas between elements.
	$allvals = preg_replace('/""/', '","', $allvals);
	
	$poststmt .= 'CREATE ' . ( !empty($type)?$type.' ' : '' ) . 'INDEX "'.$table_name.'_'.$name.'" ON "'.$table_name.'" (';
	$poststmt .= $allvals . ");\n";
}

function do_updates($tab,$content)
{
	$ret="UPDATE \"".$tab."\" SET ";
	$cols=split(",",$content);
	foreach ($cols as $vals) {
		$vals=preg_replace("/([a-zA-Z0-9_]+)=([a-zA-Z0-9_]+)/", "\"$1\"=\"$2\"", $vals);
		$ret.=$vals;
	}
	$ret=preg_replace("/\" *\"/","\",\"",$ret);
	return($ret);
}

function do_inserts($tablename, $columnlist, $values)
{
	// for some reason are the quotes in $tail addslashed. i dont know why
	//$tail = preg_replace('/\\\"/', '"', $tail);
	//$tail = preg_replace('/\\\'/', '\'', $tail);
	
	$ret = 'INSERT INTO "'.$tablename.'" (';
	//$ret .= $columnlist;
	$cols = split(",", $columnlist);
	foreach ($cols as $col) {
		$col = trim($col);
		// add quotes if column is not quoted yet
		if (strpos($col, '"') === false) {
			$ret .= '"'.$col.'"';
		} else {
			$ret .= $col;
		}
	}
	// seperate columnnames with commas
	$ret = preg_replace('/""/', '","', $ret);
	// do it the correct SQL standard way: use '' (2 apostr.) in strings instead of escaping with \'
	$values = replace_apostroph_in_string($values);
	$ret .= ') VALUES (' . $values . ')';

	//$tail = preg_replace("/md5\(\'(.+)\'\)/e", "quotemd5('$1')", $tail);		// md5() does work, at least on pg 8.3
	return $ret;
}

function quotemd5($a)
{
	return ("'".md5($a)."'");
}

function quote_prim_cols($key,$content)
{
	$ret="\n  $key (";
	$cols=split(",",$content);
	foreach ($cols as $vals) {
		$vals=preg_replace("/\(.*\)/","",$vals);
		$ret.="\"".trim($vals)."\"";
	}
	$ret=preg_replace("/\"\"/","\",\"",$ret);
	$ret.=")";
	return $ret;
}

/**
 * @param $colname column name that has the enum as datatype
 * @param $values list of values
 * @return column definition
 */
function convert_enums($colname, $values)
{
	$enumvals = split(",", $values);
	$isnum = true;
	$maxlength = 0;
	//$colname = stripslashes($colname);
	$ret="\n\t" . $colname .' ';
	foreach ($enumvals AS $val) {
		if (!is_int($val)) {
			$isnum = false;
		}
		if (strlen($val) > $maxlength){
			$maxlength = strlen($val);
		}
	}
	if ($isnum) {
		if ($maxlength < 4){
			$ret .= 'smallint ';
		} elseif ($maxlength < 9){
			$ret .= 'integer ';
		} else {
			$ret .= 'bigint ';
		}
	} else {
		$ret .= "varchar($maxlength) ";
	}
	$ret .= 'CHECK ( "' . $colname . '" IN (' . $values . ') )';
	return $ret;
}
