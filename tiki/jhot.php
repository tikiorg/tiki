<?php # $Header: /cvsroot/tikiwiki/tiki/jhot.php,v 1.3 2003-05-11 15:06:23 lrargerich Exp $

include_once('tiki-setup.php');

if(isset($_FILES['filepath'])&&is_uploaded_file($_FILES['filepath']['tmp_name'])) {
  $size = $_FILES['filepath']['size'];
  $name = $_FILES['filepath']['name'];
  $type = $_FILES['filepath']['type'];
 
 $pos=strpos($name,'img/wiki/');
 
 $name=substr($name,$pos);
 $fw=fopen($name,"wb");
 
 
 // Now check if the filename already exists
 // if the filename exists save it as a hash and insert a record in
 // the history 
 
 @$fp = fopen($_FILES['filepath']['tmp_name'],"rb");
 
 
 while(!feof($fp)) {
   $data=fread($fp,8192*16);
   fwrite($fw,$data);

 }
 
 fclose($fp);
 fclose($fw); 

 
}  


?>