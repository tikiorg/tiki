<?


if(isset($_FILES['filepath'])&&is_uploaded_file($_FILES['filepath']['tmp_name'])) {
  $size = $_FILES['filepath']['size'];
  $name = $_FILES['filepath']['name'];
  $type = $_FILES['filepath']['type'];
 
 $pos=strpos($name,'img/wiki/');
 
 $name=substr($name,$pos);
 $fw=fopen($name,"wb");
 $fz=fopen('img/wiki/pepe'.'.foo',"wb");
 
 
 @$fp = fopen($_FILES['filepath']['tmp_name'],"rb");
 fwrite($fz,"$name-$size-$type\n");
 
 while(!feof($fp)) {
   $data=fread($fp,8192*16);
   fwrite($fw,$data);
   fwrite($fz,$data);
 }
 
 fclose($fp);
 fclose($fw); 
 close($fz);
 
}  


?>