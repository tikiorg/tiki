<?php
// $Header: /cvsroot/tikiwiki/tiki/doc/devtools/tiki_create_md5.php,v 1.2.2.1 2007-11-04 22:08:07 nyloth Exp $

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
  $d=dir($dir);
  while (false !== ($e = $d->read())) {
    $entry=$dir.'/'.$e;
    if(is_dir($entry)) {
      if($e != '..' && $e != '.' && $e != 'CVS') { // do not descend and no CVS files
        md5_check_dir($entry,$result);
      }
    } else {
       if($e != 'tiki_create_md5.php') {
         echo "creating sum of $entry <br />\n";
         $result[$entry]=md5_file($entry);
       }
    }
  }
  $d->close();
}

$tikimd5=array();
md5_check_dir('.',$tikimd5);

$s=serialize($tikimd5);

$fp=fopen('lib/admin/secdb.php','wb');
fwrite($fp,"<?php\n");
fwrite($fp,"\$tikimd5=unserialize('");
fwrite($fp,$s);
fwrite($fp,"');\n?>");
fclose($fp);
?>
