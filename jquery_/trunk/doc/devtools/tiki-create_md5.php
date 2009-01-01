<?php
// $Id$

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.


/* this script generates the lib/adminlib/secdb.php md5 file database
   in future these sums shall go to the database itself to get rid of the problem that
   the secdb.php file itself will always show a error

   to use the script you have to move it to your tiki-root.

   release managers should create a clean release copy and recreate the md5 database.

   shall we checkin the lib/adminlib/secdb.php into CVS?

   the $tiki_p_admin check can be removed in the release process, but be sure that you don't
   deliver a unshielded script!
*/

require_once ('tiki-setup.php');

if ($tiki_p_admin != 'y') {
   $smarty->assign('msg', tra("You do not have permission to use this feature"));
   $smarty->display("error.tpl");
   die;
}


function md5_check_dir($dir,&$result) { // save all files in $result
  echo "opening $dir <br />\n";
  flush();
  $d=dir($dir);
  while (false !== ($e = $d->read())) {
    $entry=$dir.'/'.$e;
    if(is_dir($entry)) {
      if($e != '..' && $e != '.' && $e != 'CVS' && $entry!='./templates_c') { // do not descend and no CVS files
        md5_check_dir($entry,$result);
      }
    } else {
       if(substr($e,-4,4)==".php" && $entry != './tiki-create_md5.php' && $entry!='./db/local.php') {
         // echo "creating sum of $entry <br />\n";
         $result[$entry]=md5_file($entry);
       }
    }
  }
  $d->close();
}


$tikimd5=array();
$chkdir=isset($_REQUEST['chkdir'])?$_REQUEST['chkdir']:'.';
echo "creating md5 sums for dir $chkdir <br>";
flush();
md5_check_dir($chkdir,$tikimd5);

if(isset($_REQUEST['secdb']) && $_REQUEST['secdb']='fs') {
$s=serialize($tikimd5);

$fp=fopen('lib/admin/secdb.php.inc','wb');
fwrite($fp,"<?php\n");
fwrite($fp,"\$tikimd5=unserialize('");
fwrite($fp,$s);
fwrite($fp,"');\n?>");
fclose($fp);
} else {
   global $tikilib;
   echo "inserting into db table tiki_secdb.<br>";
   flush();
   if(!isset($_REQUEST['tikiver'])) {
      echo "you have to set the tiki version. Example: tiki-create_md5.php?tikiver=1.9";
      die;
   }

   // we update a whole revision. so we delete all old values from db!
   $query='delete from `tiki_secdb` where `tiki_version`=?';
   $tikilib->query($query,array($_REQUEST['tikiver']));
   $query='insert into `tiki_secdb`(`md5_value`,`filename`,`tiki_version`,`severity`) values (?,?,?,?)';
   foreach ($tikimd5 as $filename=>$filemd5) {
      if($chkdir != '.') {
        $filename=preg_replace("#^".preg_quote($chkdir)."#",".",$filename);
      }
      $tikilib->query($query,array($filemd5,$filename,$_REQUEST['tikiver'],0));
   }
   echo "done. use mysqldump to extract the secdb table and to add it to the release<br>";
}


?>
