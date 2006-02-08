<?
/**
* file which fetching generated html2pdf output and display it as plain html
* author Nik Chankov <nchankov@abv.bg>
* creation date: 01/02/2006 20:17
*/

$_REQUEST['file'] = "temp/".$_REQUEST['file'];
@readfile($_REQUEST['file']);
?>