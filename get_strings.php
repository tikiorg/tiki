<?php
/** \file
 * $Header: /cvsroot/tikiwiki/tiki/get_strings.php
 * \brief Update the language.php files
 * \param lang the abbrevaitaion of the language - if no parameter all the languages are processed
 */
require_once('tiki-setup.php');

if($tiki_p_admin != 'y') {
  die("You need to be admin to run this script");
}

///Get the languages where the language.php has to be updated
$languages = Array();
if (isset ($_REQUEST["lang"])) {
  $languages[] = $_REQUEST["lang"];
}
else {
  $languages = Array('cn','de','dk','en','fr','he','it','nl','no','pl','ru','sp','sw','tw');
}    	
print("Languages:");
foreach($languages as $sel) {
   print($sel );
}
print("</br>");

$files = Array();

/// Get the tpl files: the filenames end with tpl and the files are in the directory templates/ and tempaltes/*/
$dirs = Array('templates/');
chdir('templates/');// see bug on is_dir on php.net
$handle=opendir('.');
while ($file = readdir($handle)) {
  if (is_dir($file) && $file != "." && $file != "..")
     $dirs[] = "templates/$file/";
}
chdir("..");
foreach ($dirs as $dir) { 
  $handle=opendir($dir);
  while ($file = readdir($handle)) {
    if($file=='.'||$file=='..')
      continue;
    if(substr($file,strlen($file)-3,3)=="tpl") {
      print("File: $dir$file<br/>");  
      $files[] = $dir.$file;
    }
  }    	
  closedir($handle);
}

/// get the php files: the filenames end with php and the files are located in the list of directories ./, modules/; lib/, lib/*/, Smarty/plugins/
$dirs=Array('./', 'modules/', 'lib/', 'Smarty/plugins/');
chdir('lib/');
$handle=opendir('.');
while ($file = readdir($handle)) {
  if (is_dir($file) && $file != "." && $file != "..")
     $dirs[] = "lib/$file/";
}
chdir("..");
foreach ($dirs as $dir) {
  $handle=opendir($dir);
  while ($file = readdir($handle)) {
    if($file=='.'||$file=='..')
      continue;
    if(substr($file,strlen($file)-3,3)=="php") {
      print("File: $dir$file<br/>");  
      $files[] = $dir.$file;
    }
  }    	
  closedir($handle);
  }

foreach($languages as $sel) {
unset($lang);
unset($used);
include("lang/$sel/language.php");
$nbTrads=count($lang);
$fw = fopen("lang/$sel/new_language.php",'w+');
print("&lt;?php\n<br/>\$lang=Array(\n<br/>");
// the generated comment coding:utf-8 is for the benefit of emacs
fwrite($fw,"<?php\n// -*- coding:utf-8 -*-\n\$lang=Array(\n");
foreach($files as $file) {
  $fp = fopen($file,"r");
  $data = fread($fp,filesize($file));
  fclose($fp);
  if(substr($file,strlen($file)-3,3)=="tpl") {
     preg_match_all("/(?s)\{tr\}(.+?)\{\/tr\}/",$data,$words);
     foreach(array_unique($words[1]) as $word) {
       if (ereg("^\{[$][^\}]*\}$", $word))
          continue;
       if (!isset($used[$word]))
          $used[$word] = 1;
       if(isset($lang[$word])) {
          print('"'.$word.'" => "'.$lang[$word].'",'."\n<br/>");
          //fwrite($fw,'"'.$word.'" => "'.$lang[$word].'",'."\n");
       } else {
          print('+++"'.$word.'" => "'.$word.'",'."\n<br/>");  
          $lang[$word]=$word;
          //fwrite($fw,'"'.$word.'" => "'.$word.'",'."\n");
       }
     }
  }
  preg_match_all("/(?s)tra[ \t]*\( *\"([^\"]+)\"[ \t]*\)/",$data,$words);
  foreach(array_unique($words[1]) as $word) {
    if (!isset($used[$word]))
       $used[$word] = 1;
    if(isset($lang[$word])) {
      print('<b>"'.$word.'" => "'.$lang[$word].'",'."</b>\n<br/>");   
      //fwrite($fw,'"'.$word.'" => "'.$lang[$word].'",'."\n");
    } else {
      print('<b>+++"'.$word.'" => "'.$word.'",'."</b>\n<br/>");  
      $lang[$word]=$word;
      //fwrite($fw,'"'.$word.'" => "'.$word.'",'."\n");
    }
  }
  preg_match_all("/(?s)tra[ \t]*\( *\'([^\']+)\'[ \t]*\)/",$data,$words);
  foreach(array_unique($words[1]) as $word) {
	if (!isset($used[$word]))
      $used[$word] = 1;
    if(isset($lang[$word])) {
      print('<b>"'.$word.'" => "'.$lang[$word].'",'."</b>\n<br/>");   
    } else {
      print('<b>+++"'.$word.'" => "'.$word.'",'."</b>\n<br/>");  
      $lang[$word]=$word;
    }
  }
}
print('"'.'##end###'.'" => "'.'###end###'.'"'.");?&gt;\n<br/>");
$nb = 0;  
foreach($lang as $key=>$val) {
  if ($key == '##end###' && $val == '###end###')
     continue;
  // backslash $ and \n
  fwrite($fw,'"'.str_replace("\$", "\\\$", str_replace("\n", "\\n", $key)).'" => "'.str_replace("\$", "\\\$", str_replace("\n", "\\n", $val)).'",');
  if (++$nb == $nbTrads)
     fwrite($fw, "//##First new line");
  if (isset($used[$key]))
     fwrite($fw, "\n");
  else
     fwrite($fw, "//perhaps not used\n");
}
fwrite($fw,'"'.'##end###'.'" => "'.'###end###'.'");'."\n".'?>'."\n");  
fclose($fw);
@unlink("lang/$sel/old.php");
@rename("lang/$sel/language.php","lang/$sel/old.php");
rename("lang/$sel/new_language.php","lang/$sel/language.php");
}
?>
