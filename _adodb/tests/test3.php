<?php
/*
  V4.05 13 Dec 2003  (c) 2000-2003 John Lim (jlim@natsoft.com.my). All rights reserved.
  Released under both BSD license and Lesser GPL library license. 
  Whenever there is any discrepancy between the two licenses, 
  the BSD license will take precedence.
  Set tabs to 8.
 */


error_reporting(E_ALL);
//include("../adodb-exceptions.inc.php");
include("../adodb.inc.php");	
$db = NewADOConnection("oci8");
$db->Connect('','juris9','natsoft');
$db->debug=1;
$rs = $db->Execute("select * FROM KBSTEP 
	WHERE S_STEPCAT like :0 and trim(S_STEPCAT)=:1 
	ORDER BY S_STAGECAT, s_procat, s_stagecat, s_seq",array('2DRP%','2DRP'));
adodb_pr($rs);
/*
try {
$db = NewADOConnection("oci8");
$db->Connect('','scott','natsoft');
$db->debug=1;
$rs = $db->Execute("select * from adoxyz");

foreach($rs as $k => $v) {
	echo $k; adodb_pr($v);
}


$rs = $db->Execute("select bad from badder");

} catch (exception $e) {
	adodb_pr($e);
	$e = adodb_backtrace($e->trace);
}
*/
?>