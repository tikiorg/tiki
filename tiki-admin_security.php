<?php
// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
require_once ('tiki-setup.php');

// do we need it?
require_once('lib/admin/adminlib.php');

if ($tiki_p_admin != 'y') {
	$smarty->assign('msg', tra('You do not have permission to use this feature'));
	$smarty->display('error.tpl');
	die;
}

// get all dangerous php settings and check them 
$phpsettings=array();


// register globals
$s=ini_get('register_globals');
if ($s) {
   $phpsettings['register_globals']=array('risk' => tra('unsafe'),
       'setting' => $s,
       'message' => tra('register_globals should be off by default. See the php manual for details.'));
} else {
   $phpsettings['register_globals']=array('risk' => tra('safe'),
       'setting' => $s);
}

// trans_sid
$s=ini_get('session.use_trans_sid');
if ($s) {
   $phpsettings['session.use_trans_sid']=array('risk' => tra('unsafe'),
      'setting' => $s,
      'message' => tra('session.use_trans_sid should be off by default. See the php manual for details.'));
} else {
   $phpsettings['session.use_trans_sid']=array('risk' => tra('safe'),
     'setting' => $s);
}

// check file upload dir and compare it to tiki root dir
$s=ini_get('upload_tmp_dir');
$sn=substr($_SERVER['SCRIPT_NAME'],0,-23);
if (strpos($sn,$s) !== FALSE) {
   $phpsettings['upload_tmp_dir']=array('risk' => tra('unsafe'),
      'setting' => $s,
      'message' => tra('upload_tmp_dir is probably within your Tikiwiki directory. There is a risk that someone can upload any file to this directory and access them via web browser'));
} else {
   $phpsettings['upload_tmp_dir']=array(
      'risk' => tra('unknown'),
      'setting' => $s,
      'message' => tra('cannot check if the upload_tmp_dir is accessible via web browser. To be sure you should check your webserver config.'));
}

$s=ini_get('xbithack');
if($s==1) {
   $phpsettings['xbithack']=array('risk' => tra('unsafe'),
     'setting' => $s,
     'message' => tra('setting the xbithack option is unsafe. Depending on the file handling of your webserver and your tiki settings, it may be possible that a attacker can upload scripts to file gallery and execute them'));
} else {
   $phpsettings['xbithack']=array('risk' => tra('safe'), 'setting' => $s);
}

$s=ini_get('allow_url_fopen');
if($s==1) {
   $phpsettings['allow_url_fopen']=array('risk' => tra('risky'),
     'setting' => $s,
     'message' => tra('allow_url_fopen may potentially be used to upload remote data or scripts. If you dont use the blog feature, you can switch it off.'));
} else {
   $phpsettings['allow_url_fopen']=array('risk' => tra('safe'), 'setting' => $s);
}

ksort($phpsettings);
$smarty->assign_by_ref('phpsettings',$phpsettings);


// tikiwiki preferences check
// do we need to get the preferences or are they already loaded? 

$tikisettings=array();

if($prefs['feature_file_galleries']=='y' && !empty($prefs['fgal_use_dir']) && 
    substr($prefs['fgal_use_dir'],0,1)!='/') { // todo: check if absolute path is in tiki root
  $tikisettings['fgal_use_dir']=array('risk' => tra('unsafe'),'setting' => $prefs['fgal_use_dir'],
     'message' => tra('The Path to store files in the filegallery should be outside the tiki root directory'));
}

if($prefs['feature_galleries']=='y' && !empty($prefs['gal_use_dir']) && 
    substr($prefs['gal_use_dir'],0,1)!='/') {
  $tikisettings['gal_use_dir']=array('risk' => tra('unsafe'),'setting' => $prefs['gal_use_dir'],
     'message' => tra('The Path to store files in the imagegallery should be outside the tiki root directory'));
}

if($prefs['feature_edit_templates']='y') {
   $tikisettings['edit_templates']=array('risk' => tra('unsafe'),'setting' => tra('on'),
     'message' => tra('The feature "Edit Templates" is switched on. Do not allow anyone you cannot trust to use this feature. It can easily be used to inject php code.'));
}

if(file_exists('lib/wiki-plugins/wikiplugin_snarf.php')) {
   $tikisettings['wikiplugin_snarf']=array('risk' => tra('unsafe'),'setting' => tra('installed'),
     'message' => tra('The "Snarf Wikiplugin" is installed. It can be used by wiki editors to include pages from the local network and via regex replacement create any html.'));
} 

if(file_exists('lib/wiki-plugins/wikiplugin_regex.php')) {
   $tikisettings['wikiplugin_regex']=array('risk' => tra('unsafe'),'setting' => tra('installed'),
     'message' => tra('The "Regex Wikiplugin" is installed. It can be used by wiki editors to create any html via regex replacement.'));
}

if(file_exists('lib/wiki-plugins/wikiplugin_lsdir.php')) {
   $tikisettings['wikiplugin_lsdir']=array('risk' => tra('unsafe'),'setting' => tra('installed'),
     'message' => tra('The "Lsdir Wikiplugin" is installed. It can be used by wiki editors to view the contents of any directory.'));
}

if(file_exists('lib/wiki-plugins/wikiplugin_bloglist.php')) {
   $tikisettings['wikiplugin_bloglist']=array('risk' => tra('unsafe'),'setting' => tra('installed'),
     'message' => tra('The "Bloglist Wikiplugin" is installed. It can be used by wiki editors to disclose private blog posts.'));
}

ksort($tikisettings);
$smarty->assign_by_ref('tikisettings',$tikisettings);


// array for severity in tiki_secdb table. This can go into a extra table if
// the array grows to much.

$secdb_severity=array(
    //1000 Path disclosure
    1000=>tra('Path disclosure'),
    1001=>tra('Path disclosure through error message'),
    //2000 SQL injection
    2000=>tra('SQL injection'),
    2001=>tra('SQL injection by authenticated user'),
    2002=>tra('SQL injection by authenticated user with special privileges'),
    2003=>tra('SQL injection without authentication'),
    //3000 command injection
    3000=>tra('PHP command injection'),
    3001=>tra('PHP command injection by authenticated user'),
    3002=>tra('PHP command injection by authenticated user with special privileges'),
    3003=>tra('PHP command injection without authentication'),
    //4000 File upload
    4000=>tra('File upload')
    );




// dir walk & check functions
function md5_check_dir($dir,&$result) { // save all suspicious files in $result
  global $tikilib;
  global $tiki_versions;
  $c_tiki_versions=count($tiki_versions);
  $query="select * from `tiki_secdb` where `filename`=?";
  $d=dir($dir);
  while (false !== ($e = $d->read())) {
    $entry=$dir.'/'.$e;
    if(is_dir($entry)) {
      if($e != '..' && $e != '.' && $entry!='./templates_c') { // do not descend and no checking of templates_c since the file based md5 database would grow to big
        md5_check_dir($entry,$result);
      }
    } else if(substr($e,-4,4) == ".php") {
      if(!is_readable($entry)) {
	 $result[$entry]=tra('File is not readable. Unable to check.');
      } else {
	 $md5val=md5_file($entry);
         $dbresult=$tikilib->query($query,array($entry));
	 $is_tikifile=false;
	 $is_tikiver=array();
	 $valid_tikiver=array();
	 $severity=0;
	 // we could avoid the following with a second sql, but i think, this is faster.
         while($res=$dbresult->FetchRow()) {
	    $is_tikifile=true; // we know the filename ... probably modified
	    if($res['md5_value']==$md5val) {
	       $is_tikiver[]=$res['tiki_version']; // found
	       $severity=$res['severity'];
	    }

      $k=array_search($res['tiki_version'],$tiki_versions);
      if($k>0) {
        //record the valid versions in this array
        if($res['md5_value']==$md5val) {
          $valid_tikiver[$k]=true;
        } else {
          $valid_tikiver[$k]=false;
        }
      }
	 }
//        echo "<pre>";print_r($valid_tikiver);echo"</pre>";

        if($is_tikifile==false) {
           $result[$entry]=tra('This is not a Tikiwiki file. Check if this file was uploaded and if it is dangerous.');
        } else if($is_tikifile==true && count($is_tikiver)==0) {
	   $result[$entry]=tra('This is a modified File. Cannot check version. Check if it is dangerous.');
        } else {
          // check if we have a most recent valid version
          $most_recent=false;
          for ($i=$c_tiki_versions;$i>0;$i--) { // search $valid_tikiver top to down to find the most recent version
            if(isset($valid_tikiver[$i])) {
              if($valid_tikiver[$i]==false) {
                //$most_recent stays false. we break
                break;
              } else {
                $most_recent=true; // in this case we have found the most recent version. good
                break;
              }
            }
          }
          
          // use result of most_recent to decide

            if($most_recent==false) {
           $result[$entry]=tra('This file is from another Tikiwiki version: ').implode(tra(' or '),$is_tikiver);
            }
        }
      }
    }
  }
  $d->close();
  }


// if check installation is pressed, walk through all files and compute md5 sums
if (isset($_REQUEST['check_files'])) {
  global $tiki_versions;
  $tiki_versions=array(
		1=>'1.9.1',
		2=>'1.9.1.1',
		3=>'1.9.2',
		4=>'1.9.3.1',
		5=>'1.9.3.2',
		6=>'1.9.4',
		7=>'1.9.5',
		8=>'1.9.6',
		9=>'1.9.7'
	); // all valid versions. Newer versions have a higher array index
  $result=array();
  md5_check_dir(".",$result);
  // echo "<pre>"; print_r($tikimd5);echo "</pre><br />";
  // echo "<pre>"; print_r($result);echo "</pre><br />";
  $smarty->assign('filecheck',true);
  $smarty->assign_by_ref('tikifiles',$result);
}

// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');

$smarty->assign('mid', 'tiki-admin_security.tpl');
$smarty->display("tiki.tpl");

?>
