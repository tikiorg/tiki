<?php # $Header: /cvsroot/tikiwiki/tiki/jhot.php,v 1.5 2003-06-24 21:04:46 mose Exp $

include_once('tiki-setup.php');
include_once('lib/drawings/drawlib.php');

if(isset($_FILES['filepath'])&&is_uploaded_file($_FILES['filepath']['tmp_name'])) {
  $size = $_FILES['filepath']['size'];
  $name = $_FILES['filepath']['name'];
  $type = $_FILES['filepath']['type'];
 
 $pos=strpos($name,'img/wiki/');
 $name=substr($name,$pos);
 
 $absolute_name = str_replace("img/wiki/$tikidomain",'',$name); 
 $absolute_name = str_replace('.gif','',$absolute_name); 
 $absolute_name = str_replace('.pad_xml','',$absolute_name); 

 $now = date("U");
 
 
 if(strstr($name,'.gif')) {
 	$hash = $absolute_name.md5(uniqid('.')).'.gif';
 }
 
 if(strstr($name,'.pad_xml')) {
 	$hash = $absolute_name.md5(uniqid('.')).'.pad_xml';
 } 


 
 if(strstr($name,'.pad_xml')) {
   $drawlib->update_drawing($absolute_name,$hash,$user);
 } else {
   $drawlib->set_drawing_gif($absolute_name,$hash);
 }
 
 
 
 @$fw=fopen("img/wiki/$tikidomain$hash","wb");
 @$fw2=fopen($name,"wb");
 @$fp = fopen($_FILES['filepath']['tmp_name'],"rb");
 
 
 while(!feof($fp)) {
   $data=fread($fp,8192*16);
   fwrite($fw,$data);
   fwrite($fw2,$data);

 }
 
 fclose($fp);
 fclose($fw); 
 fclose($fw2);

 
}  


?>
