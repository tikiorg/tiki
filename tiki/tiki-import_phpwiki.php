<?php
require_once('tiki-setup.php');

if($tiki_p_admin != 'y') {
    $smarty->assign('msg',tra("You dont have permission to use this feature"));
    $smarty->display('error.tpl');
    die;
}


require ("lib/webmail/mimeDecode.php");

function parse_output(&$obj, &$parts,$i) {  
  if(!empty($obj->parts)) {    
    for($i=0; $i<count($obj->parts); $i++)      
      parse_output($obj->parts[$i], $parts,$i);  
  }else{    
    $ctype = $obj->ctype_primary.'/'.$obj->ctype_secondary;    
    switch($ctype) {    
      case 'application/x-phpwiki':
         $aux["body"] = $obj->body;  
         $ccc=$obj->headers["content-type"];
         $items = split(';',$ccc);
         foreach($items as $item) {
           $portions = split('=',$item);
           if(isset($portions[0])&&isset($portions[1])) {
             $aux[trim($portions[0])]=trim($portions[1]);
           }
         }
         
         
         $parts[]=$aux;
         
    }  
  }
}


$smarty->assign('result','y');
if(isset($_REQUEST["import"])) {
$h=opendir("phpwikidump/");
$lines=Array();
while($file=readdir($h)) {
  if(is_file("phpwikidump/$file")) {
    $fp=fopen("phpwikidump/$file","r");
    $full=fread($fp,filesize("phpwikidump/$file"));
    fclose($fp);
    $params = array('input' => $full,
                  'crlf'  => "\r\n", 
                  'include_bodies' => TRUE,
                  'decode_headers' => TRUE, 
                  'decode_bodies'  => TRUE
                  );  
    $output = Mail_mimeDecode::decode($params);    
    unset($parts);
    parse_output($output, $parts,0);  
    foreach($parts as $part) {
      if(isset($part["pagename"])) {
        // Parse the body replacing links to Tiki links
        //print_r($part);die;
        $part["body"]=preg_replace("/ (http:\/\/[^ ]+) /"," [$1] ",$part["body"]);
        $part["body"]=preg_replace("/\[(http:\/\/[^\]]+)\]/","{img src=$1}",$part["body"]);
        $pagename=urldecode($part["pagename"]);
        $version=urldecode($part["version"]);
        $author=urldecode($part["author"]);
        $lastmodified=$part["lastmodified"];
        $authorid=urldecode($part["author_id"]);
        $hits=urldecode($part["hits"]);
        $ex=substr($part["body"],0,25);
        //print(strlen($part["body"]));
        $msg='';
        if($tikilib->page_exists($pagename)) {
          if($_REQUEST["crunch"]=='n') {
            $msg='<b>'.tra('page not added (Exists)').'</b>';
          } else {
            $msg='<b>'.tra('overwriting old page').'</b>';
            $tikilib->update_page($pagename,$part["body"],tra('updated by the phpwiki import process'),$author,$authorid);
          }
        } else {
          $msg=tra('page created');
          $tikilib->create_page($pagename,$hits,$part["body"],$lastmodified,tra('created from phpwiki import'),$author,$authorid);
        }
        $aux["page"]=$pagename;
        $aux["version"]=$version;
        $aux["part"]=$ex;
        $aux["msg"]=$msg;
        
        $lines[]=$aux;
      }
    }
    
  }
}
closedir($h);
$smarty->assign('lines',$lines);
$smarty->assign('result','y');
}
$smarty->assign('mid','tiki-import_phpwiki.tpl');
$smarty->display('tiki.tpl');

?>