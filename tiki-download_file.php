<?php
// CVS: $Id: tiki-download_file.php,v 1.33.2.2 2008-02-29 12:33:37 nyloth Exp $
// Initialization
$force_no_compression = true;
require_once('tiki-setup.php');
include_once ('lib/stats/statslib.php');
include_once('lib/filegals/filegallib.php');

if($prefs['feature_file_galleries'] != 'y') {
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

if (isset($_REQUEST["fileId"])) {
	$info = $tikilib->get_file($_REQUEST["fileId"]);
} elseif (isset($_REQUEST["galleryId"]) && isset($_REQUEST["name"])) {
	$info = $tikilib->get_file_by_name($_REQUEST["galleryId"], $_REQUEST["name"]);
	$_REQUEST['fileId'] = $info['fileId'];
} else {
	$smarty->assign('msg',tra('Incorrect param'));
	$smarty->display('error.tpl');
  die;
}
if (!is_array($info)) {
	$smarty->assign('msg',tra('Incorrect param'));
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

if ( ! empty($_REQUEST['lock']) ) {
	if (!empty($info['lockedby']) && $info['lockedby'] != $user) {
		$smarty->assign('msg', tra(sprintf('The file is locked by %s', $info['lockedby'])));
		$smarty->display('error.tpl');
		die;
	}
	$filegallib->lock_file($_REQUEST['fileId'], $user);
}	 

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
$file = preg_replace('/.*([^\/]*)$/U','$1', $info['filename']); // IE6 can not download file with / in the name (the / can be there from a previous bug)
$content=&$info["data"];

//add a hit
$statslib->stats_hit($file,"file",$_REQUEST["fileId"]);
if ($prefs['feature_actionlog'] == 'y') {
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
if (!isset($_GET['display'])) {
header( "Content-Disposition: attachment; filename=\"$file\"" );
}
//header( "Content-Disposition: inline; filename=$file" );

if( $info["path"] )
{
	header("Content-Length: ". filesize( $prefs['fgal_use_dir'].$info["path"] ) );
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
  readfile_chunked($prefs['fgal_use_dir'].$info["path"]);
} else {
  echo "$content";
}

?>
