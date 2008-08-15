<?php
/* $Id$ */

$tikiversion='1.9';
if(!isset($_GET['version'])) {
   echo "version not given. Using default $tikiversion.<br />";
} else {
   if(preg_match('/\d\.\d/',$_GET['version'])) {
      $tikiversion=$_GET['version'];
   }
}


// read file
$file="../tiki-$tikiversion-mysql.sql";
@$fp = fopen($file,"r");
if(!$fp) echo "Error opening $file";
$data = '';
echo "reading $file: ";
while(!feof($fp)) {
  $data .= fread($fp,4096);
  echo ".";
}
fclose($fp);
echo "<br />\n";


// split into statements

$statements=preg_split("#(;\n)|(;\r\n)#",$data);

echo "<table>\n";
// step though statements
$fp=fopen($tikiversion.".to_pgsql72.sql","w");
foreach ($statements as $statement)
{
  echo "<tr><td><pre>\n";
  echo $statement.";";
  echo "\n</pre></td><td><pre>\n";
  $parsed=parse($statement);
  fwrite($fp,$parsed);
  echo $parsed;
  echo "\n</pre></td></tr>\n";
}
fclose($fp);
echo "</table>\n";

function parse($stmt)
{
  // variable for statements that have to be appended
  global $poststmt;
  $poststmt="\n\n";

  //replace comments
  $stmt=preg_replace("/#/","--",$stmt);
  //Remove odd characters.
  //These chars should probably never be in the mysql file, but there are some there now.  So this'll remove them.
  $stmt=preg_replace("/`/","",$stmt);
  // drop TYPE=MyISAM and AUTO_INCREMENT=1
  $stmt=preg_replace("/TYPE=MyISAM/","",$stmt);
  $stmt=preg_replace("/AUTO_INCREMENT=1/","",$stmt);
  //postgres cannot DROP TABLE IF EXISTS
  $stmt=preg_replace("/DROP TABLE IF EXISTS/","DROP TABLE",$stmt);
  //auto_increment things
  $stmt=preg_replace("/int(eger)* NOT NULL auto_increment/i","bigserial",$stmt);
  $stmt=preg_replace("/int(eger)*\(\d\) (unsigned )*NOT NULL auto_increment/i","serial",$stmt);
  $stmt=preg_replace("/int(eger)*\(\d\d\) (unsigned )*NOT NULL auto_increment/i","bigserial",$stmt);
  // integer types
  $stmt=preg_replace("/tinyint\([1-4]\)/","smallint",$stmt);
  $stmt=preg_replace("/int(eger)*\([1-4]\)( unsigned)*/","smallint",$stmt);
  $stmt=preg_replace("/int(eger)*\([5-9]\)( unsigned)*/","integer",$stmt);
  $stmt=preg_replace("/int(eger)*\(\d\d\)( unsigned)*/","bigint",$stmt);
  // timestamps
  $stmt=preg_replace("/timestamp\([^\)]+\)/","timestamp(3)",$stmt);
  // blobs
  $stmt=preg_replace("/longblob|tinyblob|blob/","bytea",$stmt);
  // text fields
  $stmt=preg_replace("/longtext/","text",$stmt);
  // quote column names
  $stmt=preg_replace("/\n[ \t]+([a-zA-Z0-9_]+)/","\n  \"$1\"",$stmt);
  // quote and record table names
  $stmt=preg_replace("/(DROP TABLE |CREATE TABLE )([a-zA-Z0-9_]+)( \()*/e","record_tablename('$1','$2','$3')",$stmt);
  // unquote the PRIMARY and other Keys
  $stmt=preg_replace("/\n[ \t]+\"(PRIMARY|KEY|FULLTEXT|UNIQUE)\"/","\n  $1",$stmt);
  // convert enums
  $stmt=preg_replace("/\n[ \t]+(\"[a-zA-Z0-9_]+\") enum *\(([^\)]+)\)/e","convert_enums('$1','$2')",$stmt);
  // quote column names in primary keys
  $stmt=preg_replace("/\n[ \t]+(PRIMARY KEY) *\((.+)\)/e","quote_prim_cols('$1','$2')",$stmt);
  // create indexes from KEY ...
  $stmt=preg_replace("/,\n[ \t]+KEY ([a-zA-Z0-9_]+) \((.+)\)/e","create_index('$1','$2')",$stmt);

  // Postgres does not support FULLTEXT indexing.  If we were to index the text columns
  // then there would be a limit to the size of the data that could occupy the text
  // columns.
  // Work arounds for this include adding the tsearch2 module to postgres and other drastic changes.
  //$stmt=preg_replace("/,\n[ \t]+FULLTEXT KEY ([a-zA-Z0-9_]+) \((.+)\)/e","create_index('$1','$2')",$stmt);
  $stmt=preg_replace("/,\n[ \t]+FULLTEXT KEY ([a-zA-Z0-9_]+) \((.+)\)/e","",$stmt);

  $stmt=preg_replace("/,\n[ \t]+(UNIQUE) KEY ([a-zA-Z0-9_]+) \((.+)\)/e","create_index('$2','$3','$1')",$stmt);
  $stmt=preg_replace("/,\n[ \t]+(UNIQUE) *\((.+)\)/e","create_index('unknown','$2','$1')",$stmt);
  // explicit create index
  $stmt=preg_replace("/CREATE *(UNIQUE)* *INDEX *([a-z0-9_]+) *ON *([a-z0-9_]+) *\((.*)\)/ei","create_explicit_index('$2','$3','$4','$1')",$stmt);
  // handle inserts
  $stmt=preg_replace("/INSERT INTO ([a-zA-Z0-9_]*).*\(([^\)]+)\) VALUES *(.*)/ie","do_inserts('$1','$2','$3')",$stmt);
  $stmt=preg_replace("/INSERT IGNORE INTO ([a-zA-Z0-9_]*).*\(([^\)]+)\) VALUES *(.*)/ie","do_inserts('$1','$2','$3')",$stmt);
  // the update
  $stmt=preg_replace("/update ([a-zA-Z0-9_]+) set (.*)/e","do_updates('$1','$2')",$stmt);
  $stmt=preg_replace("/UPDATE ([a-zA-Z0-9_]+) set (.*)/e","do_updates('$1','$2')",$stmt);
	// clean cases where UNIQUE was alone at the end
  $stmt=preg_replace("/,( *)\)/","$1)",$stmt);
  return $stmt.";".$poststmt;
}

function record_tablename($stmt,$tabnam,$tail)
{
  global $table_name;
  $table_name=$tabnam;
  return($stmt."\"".$tabnam."\"".$tail);
}

function create_explicit_index($name,$table_name,$content,$type)
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

  return("CREATE $type INDEX \"" . $name . "\" ON \"" . $table_name . "\" (" . $allvals . ");\n");
}

function create_index($name,$content,$type="")
{
  global $table_name;
  global $poststmt;
  $poststmt.="CREATE $type INDEX \"".$table_name."_".$name."\" ON \"".$table_name."\"(";
  $cols=split(",",$content);
  $allvals="";
  foreach ($cols as $vals) {
    $vals=preg_replace("/^ */","",$vals);
    $vals=preg_replace("/ *$/","",$vals);
    $vals=preg_replace("/([a-z0-9_]+)/i","\"$1\"",$vals);

    // Do var(val) conversion to substr(var, 0, val); since that's what is expected for these indexes
    $vals=preg_replace("/([\"a-z0-9_]+) *\(\"([0-9]+)\"\)/i","substr($1, 0, $2)",$vals);

    $allvals.=$vals;
  }
  // Put commas between elements.
  $allvals=preg_replace("/\"\"/","\",\"",$allvals);
  $allvals=preg_replace("/\"substr/","\",substr",$allvals);
  $allvals=preg_replace("/(substr\(.*\))\"/","$1,\",substr",$allvals);

  $poststmt.=$allvals.");\n";
}

function do_updates($tab,$content)
{
  $ret="UPDATE \"".$tab."\" SET ";
  $cols=split(",",$content);
  foreach ($cols as $vals) {
    $vals=preg_replace("/([a-zA-Z0-9_]+)=([a-zA-Z0-9_]+)/","\"$1\"=\"$2\"",$vals);
    $ret.=$vals;
  }
  $ret=preg_replace("/\" *\"/","\",\"",$ret);
  return($ret);
}

function do_inserts($tab,$content,$tail)
{
  // for some reason are the quotes in $tail addslashed. i dont know why
  $tail=preg_replace('/\\\"/','"',$tail);
//  echo "tail: $tail :tail";
  $ret="INSERT INTO \"".$tab."\" (";
  $cols=split(",",$content);
  foreach ($cols as $vals) {
    $vals=preg_replace("/ /","",$vals);
    $ret.="\"$vals\"";
  }
  $ret=preg_replace("/\"\"/","\",\"",$ret);
  $ret.=")";
  
  $tail=preg_replace("/md5\(\'(.+)\'\)/e","quotemd5('$1')",$tail);
  return $ret." VALUES ".$tail;
}

function quotemd5($a)
{ return ("'".md5($a)."'");}

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

function convert_enums($colname,$content)
{
 $enumvals=split(",",$content);
 $isnum=true;
 $length=0;
 $colname=stripslashes($colname);
 $ret="\n  $colname ";
 foreach ($enumvals as $vals) {
   if (!is_int($vals)) $isnum=false;
   if (strlen($vals)>$length) $length=strlen($vals);
 }
 if ($isnum) {
   if ($length < 4) $ret.="smallint ";
   elseif ($length < 9) $ret.="integer ";
   else $ret.="bigint ";
 } else {
   $ret.="varchar($length) ";
 }
 $ret.="CHECK ($colname IN ($content))";
 return $ret;
}
?>
