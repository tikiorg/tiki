<?php

require_once('tiki-setup.php');

if($tiki_p_admin != 'y') {
  die("You need to be admin to run this script");
}

// Cambiar lo que busca segun sea .php o .tpl

$languages = Array('tw','dk','ru','de','en','sp','fr','dk','ru','sw');
//$languages = Array('sw');

$files = Array();
  
$handle=opendir('templates');
while ($file = readdir($handle))
{
  if($file=='.'||$file=='..')
  continue;
  if(substr($file,strlen($file)-3,3)=="tpl") {
    print("File: $file<br/>");  
    $files[] = 'templates/'.$file;
  }
}    	
closedir($handle);

$handle=opendir('templates/modules');
while ($file = readdir($handle))
{
  if($file=='.'||$file=='..')
  continue;
  if(substr($file,strlen($file)-3,3)=="tpl") {
    print("File: $file<br/>");  
    $files[] = 'templates/modules/'.$file;
  }
}    	
closedir($handle);


$handle=opendir('.');
while ($file = readdir($handle))
{
  if($file=='.'||$file=='..')
  continue;
  if(substr($file,strlen($file)-3,3)=="php") {
    print("File: $file<br/>");  
    $files[] = $file;
  }
}    	
closedir($handle);

$handle=opendir('modules/');
while ($file = readdir($handle))
{
  if($file=='.'||$file=='..')
  continue;
  if(substr($file,strlen($file)-3,3)=="php") {
    print("File: $file<br/>");  
    $files[] = 'modules/'.$file;
  }
}    	
closedir($handle);



foreach($languages as $sel) {
unset($lang);
require("lang/$sel/language.php");
$fw = fopen("lang/$sel/new_language.php",'w+');
print("&lt;?php\n<br/>\$lang=Array(\n<br/>");
fwrite($fw,"<?php\n\$lang=Array(\n");
foreach($files as $file) {
  $fp = fopen($file,"r");
  $data = fread($fp,filesize($file));
  fclose($fp);
  preg_match_all("/\{tr\}([^\{]+)\{\/tr\}/",$data,$words);
  foreach(array_unique($words[1]) as $word) {
    if(isset($lang[$word])) {
      print('"'.$word.'" => "'.$lang[$word].'",'."\n<br/>");   
      //fwrite($fw,'"'.$word.'" => "'.$lang[$word].'",'."\n");
    } else {
      print('+++"'.$word.'" => "'.$word.'",'."\n<br/>");  
      $lang[$word]=$word;
      //fwrite($fw,'"'.$word.'" => "'.$word.'",'."\n");
    }
  }
  preg_match_all("/tra\(\"([^\"]+)\"\)/",$data,$words);
  foreach(array_unique($words[1]) as $word) {
    if(isset($lang[$word])) {
      print('<b>"'.$word.'" => "'.$lang[$word].'",'."</b>\n<br/>");   
      //fwrite($fw,'"'.$word.'" => "'.$lang[$word].'",'."\n");
    } else {
      print('<b>+++"'.$word.'" => "'.$word.'",'."</b>\n<br/>");  
      $lang[$word]=$word;
      //fwrite($fw,'"'.$word.'" => "'.$word.'",'."\n");
    }
  }
}
print('"'.'##end###'.'" => "'.'###end###'.'"'.");?&gt;\n<br/>");  
foreach($lang as $key=>$val) {
  fwrite($fw,'"'.$key.'" => "'.$val.'",'."\n");
}
fwrite($fw,'"'.'##end###'.'" => "'.'###end###'.'");'."\n".'?>'."\n");  
fclose($fw);
@unlink("lang/$sel/old.php");
rename("lang/$sel/language.php","lang/$sel/old.php");
rename("lang/$sel/new_language.php","lang/$sel/language.php");
}
?>