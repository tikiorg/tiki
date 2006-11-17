<?php
// CVS: $Id: tiki-download_file.php,v 1.27 2006-11-17 18:32:45 sylvieg Exp $
// Initialization
include_once("lib/init/initlib.php");
require_once('tiki-setup.php');
include_once ('lib/stats/statslib.php');
include_once('lib/filegals/filegallib.php');

if($feature_file_galleries != 'y') {
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display("error.tpl");
  die;
}

/*
Borrowed from http://php.net/manual/en/function.readfile.php#54295
to come over the 2MB readfile() limitation
*/
function readfile_chunked($filename,$retbytes=true) {
   $chunksize = 1*(1024*1024); // how many bytes per chunk
   $buffer = '';
   $cnt =0;
   $handle = fopen($filename, 'rb');
   if ($handle === false) {
       return false;
   }
   while (!feof($handle)) {
       $buffer = fread($handle, $chunksize);
       echo $buffer;
       ob_flush();
       flush();
       if ($retbytes) {
           $cnt += strlen($buffer);
       }
   }
       $status = fclose($handle);
   if ($retbytes && $status) {
       return $cnt; // return num. bytes delivered like readfile() does.
   }
   return $status;
}

if(!isset($_REQUEST['fileId']) || !($info = $tikilib->get_file($_REQUEST['fileId']))) {
	$smarty->assign('msg', tra('incorrect fieldId'));
	$smarty->display('error.tpl');
	die;
}

$_REQUEST["galleryId"] = $info["galleryId"];

$smarty->assign('individual','n');
if($userlib->object_has_one_permission($_REQUEST["galleryId"],'file gallery')) {
  $smarty->assign('individual','y');
  if($tiki_p_admin != 'y') {
    // Now get all the permissions that are set for this type of permissions 'file gallery'
    $perms = $userlib->get_permissions(0,-1,'permName_desc','','file galleries');
    foreach($perms["data"] as $perm) {
      $permName=$perm["permName"];
      if($userlib->object_has_permission($user,$_REQUEST["galleryId"],'file gallery',$permName)) {
        $$permName = 'y';
        $smarty->assign("$permName",'y');
      } else {
        $$permName = 'n';
        $smarty->assign("$permName",'n');
      }
    }
  }
}
if($tiki_p_admin_file_galleries == 'y') {
  $tiki_p_download_files = 'y';
}


if($tiki_p_download_files != 'y') {
  $smarty->assign('msg',tra("You can not download files"));
  $smarty->display("error.tpl");
  die;
}

if (!empty($_REQUEST['user'])) {
	if (!empty($info['lockedby']) && $info['lockedby'] != $user) {
		$smarty->assign('msg', tra(sprintf('The file is locked by %s', $info['lockedby'])));
		$smarty->display('error.tpl');
		die;
	}
	$filegallib->lock_file($_REQUEST['fileId'], $user);
}	 

$fgal_use_db=$tikilib->get_preference('fgal_use_db','y');
$fgal_use_dir=$tikilib->get_preference('fgal_use_dir','');

if (!IsSet($_SERVER['REQUEST_URI'])) { 
	$_SERVER['REQUEST_URI'] = ''; 
	
	if (IsSet($_SERVER['PHP_SELF'])) { 
	$_SERVER['REQUEST_URI'] = $_SERVER 
	['REQUEST_URI'].$_SERVER['PHP_SELF']; 
	} 
	
	if (IsSet($_SERVER['QUERY_STRING'])) { 
	$_SERVER['REQUEST_URI'] = $_SERVER 
	['REQUEST_URI'].'?'.$_SERVER['QUERY_STRING']; 
	} 
}

$foo = parse_url($_SERVER["REQUEST_URI"]);
$foo1=str_replace("tiki-browse_image","tiki-browse_image",$foo["path"]);
$foo2=str_replace("tiki-browse_image","show_image",$foo["path"]);
$smarty->assign('url_browse',$tikilib->httpPrefix().$foo1);
$smarty->assign('url_show',$tikilib->httpPrefix().$foo2);


$tikilib->add_file_hit($_REQUEST["fileId"]);

$type=&$info["filetype"];
$file=&$info["filename"];
$content=&$info["data"];

//add a hit
$statslib->stats_hit($file,"file",$_REQUEST["fileId"]);
if ($feature_actionlog == 'y') {
	include_once('lib/logs/logslib.php');
	$logslib->add_action('Downloaded', $_REQUEST['galleryId'], 'file gallery', 'fileId='.$_REQUEST["fileId"]);
}
// close the session in case of large downloads to enable further browsing
session_write_close();

//print("File:$file<br />");
//die;
header("Content-type: $type");

// Added by Jenolan  31/8/2003 /////////////////////////////////////////////
// File galleries should always be attachments (files) not inline (textual)
header( "Content-Disposition: attachment; filename=\"$file\"" );
//header( "Content-Disposition: inline; filename=$file" );

if( $info["path"] )
{
	header("Content-Length: ". filesize( $fgal_use_dir.$info["path"] ) );
}
else
{
	header("Content-Length: ". $info[ "filesize" ] );
}

////////////////////////////////////////////////////////////////////////////
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Pragma: public");
if($info["path"]) {
  readfile_chunked($fgal_use_dir.$info["path"]);
} else {
  echo "$content";
}
?>
