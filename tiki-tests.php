<?php  
//foo
include_once('tiki-setup.php');
include_once('PHPUnit.php');

$h = opendir('tests');
while($file=readdir($h)) {
  if(strstr($file,'.php') ) {
    print("<b>$file</b>");
    include_once("tests/$file");
  }
}
closedir($h);

?>