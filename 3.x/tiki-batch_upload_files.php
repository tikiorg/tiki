<?php
// $Header$

$section = 'file_galleries';
require_once ('tiki-setup.php');
include_once ('lib/filegals/filegallib.php');

if ($prefs['feature_file_galleries'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_file_galleries");
	$smarty->display("error.tpl");
	die;
}

if ($prefs['feature_file_galleries_batch'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_file_galleries_batch");
	$smarty->display("error.tpl");
	die;
}

// Now check permissions to access this page
if ($tiki_p_batch_upload_file_dir != 'y') {
	$smarty->assign('errortype', 401);
	$smarty->assign('msg', tra("Permission denied you cannot use the batch directory loading"));
	$smarty->display("error.tpl");
	die;
}

// check directory path 
if (!isset($prefs['fgal_batch_dir']) or !is_dir($prefs['fgal_batch_dir'])) {
	$msg = tra("Incorrect directory chosen for batch upload of files.")."<br />"; 
	if ($tiki_p_admin == 'y') {
		$msg.= tra("Please setup that dir on ").'<a href="tiki-admin.php?page=fgal">'.tra('File Galleries Admin Panel').'</a>.';
	} else {
		$msg.= tra("Please contact the website administrator.");
	}
	$smarty->assign('msg', $msg); 
	$smarty->display("error.tpl");
	die;
} else {
	$filedir = $prefs['fgal_batch_dir'];
}

// We need a galleryId
if (!isset($_REQUEST['galleryId'])) {
	$_REQUEST['galleryId']=0;
	$podCastGallery = false;
} else {
	$gal_info = $tikilib->get_file_gallery($_REQUEST["galleryId"]);
	$podCastGallery = $filegallib->isPodCastGallery($_REQUEST["galleryId"], $gal_info);
}

$smarty->assign('filedir',$filedir);

$a_file = $filestring = $feedback = array();
$a_path = array();

$disallowed_types = array('.php','php3','php4','phtml','phps','.py','.pl','.sh','php~'); // list of filetypes you DO NOT want to show

// recursively get all files from all subdirectories
function getDirContent($sub) {
	global $disallowed_types;
	global $a_file;
	global $a_path;
	global $filedir, $smarty;
	
	$tmp=$filedir;
	if ($sub <> "") $tmp .= '/'.$sub;
	if (!@($dfile = opendir($tmp))) {
		$msg= tra("Invalid directory name");
		$smarty->assign('msg', $msg); 
		$smarty->display("error.tpl");
		die;
	}
	$allfile = array();
	while((false!==($filef=readdir($dfile)))) {
		if ($filef != "." && $filef != ".." && substr($filef,0,1) != "." ) {
			$allfile[] = $filef;
		}
	}
	sort($allfile);
	foreach ($allfile as $filefile) {
		if (is_dir($tmp . "/" . $filefile)) {
			if ((substr($sub,-1)<>"/") && (substr($sub,-1)<>"\\")) {
				$sub .= '/';
			}
			getDirContent($sub.$filefile);
		} elseif (!in_array(strtolower(substr($filefile,-(strlen($filefile)-strrpos($filefile, ".")))),$disallowed_types)) {
			$a_file[] = $filefile;
			$a_path[] = $sub;
		}
	}
	closedir($dfile);
}

// build a complete list of all files on filesystem including all necessary file info
function buildFileList() {
	global $a_file;
	global $a_path;
	global $filedir, $smarty;
	global $filestring;

	getDirContent('');
	
	$totfile = count($a_file); // total file number
	$totalsize = 0;
	
	// build file data array
	for($x=0; $x < $totfile; $x++) {
		// get root dir
		while (substr($filedir,-1)=='/') $filedir = substr($filedir,0,-1);
		$tmp = $filedir;
		// add any subdir names
		if ($a_path[$x] <> "") $tmp .= $a_path[$x];
	
		// get file information
		$filesize = @filesize($tmp.'/'.$a_file[$x]);
		$filestring[$x][0]= $a_file[$x];
	
		if ($a_path[$x] <> "") $filestring[$x][0]= $a_path[$x].'/'.$a_file[$x];
		$filestring[$x][1]= $filesize;
	
		// type is string after last dot
		$tmp=strtolower(substr($a_file[$x],-(strlen($a_file[$x])-1-strrpos($a_file[$x], "."))));
		$filestring[$x][2]= $tmp;
		$totalsize+= $filesize;
	}
	$smarty->assign('totfile',$totfile);
	$smarty->assign('totalsize',$totalsize);
	$smarty->assign('filestring',$filestring);
}


if (isset($_REQUEST["batch_upload"]) and isset($_REQUEST['files']) and is_array($_REQUEST['files'])) {
	// default is: file names from request
	$fileArray = $_REQUEST['files'];
	$totfiles = count($fileArray);

	// if ALL is given, get all the files from the filesystem (stored in $a_file[] already) 
	if ($totfiles==1) {
		if ($fileArray[0]=="ALL") {
			getDirContent('');
			$fileArray = $a_file;
			$filePathArray = $a_path;
			$totfiles = count($fileArray);
		}
	}

	// for subdirToSubgal we need all existing sub galleries for the current gallery
	$subgals = array();	
	if (isset($_REQUEST["subdirTosubgal"])) {
		$subgals = $filegallib->get_subgalleries(0, 9999, "name_asc", '', $_REQUEST["galleryId"]);
	}

	// cycle through all files to upload	
	for ($x=0; $x < $totfiles; $x++) {
		$error=false;
		if (!isset($filePathArray[$x])) {
			$filePathArray[$x] = '';
		} else if ($filePathArray[$x]<>"") {
			$filePathArray[$x] .= '/';
		} else {
			// if there is a path in file name, move it to the path array
			if (strrpos($fileArray[$x],"/")>0) {
				$filePathArray[$x] = substr($fileArray[$x],0,strrpos($fileArray[$x],"/")+1);
				$fileArray[$x] = substr($fileArray[$x],strrpos($fileArray[$x],"/")+1);
			}
		}
		
		$filepath = $filedir.$filePathArray[$x].$fileArray[$x];
		$filesize = @filesize($filepath);
		// type is string after last dot
		$type = strtolower(substr($fileArray[$x],-(strlen($fileArray[$x])-1-strrpos($fileArray[$x], "."))));
		$data = '';
		$sizeArray[$x] = 0;
		$typeArray[$x] = "";
		$savedir = '';

		$fp = @fopen($filepath,'r');
		if (!$fp) {
			$feedback[] = "!!!". sprintf(tra('Could not read file %s.'),$filepath);
			$error=true;
			continue;
		}
		$data = '';
		$fhash = '';

		$path_parts = pathinfo($filepath);
		$ext=strtolower($path_parts["extension"]);
		include_once ('lib/mime/mimetypes.php');
		$typeArray[$x] = $mimetypes["$ext"];

		if (($prefs['fgal_use_db'] == 'n') || ($podCastGallery)) {
			$fhash = md5($name = $fileArray[$x]);
			$fhash = md5(uniqid($fhash));

			// for podcast galleries add the extension so the
			// file can be called directly if name is known,
			$savedir=$prefs['fgal_use_dir'];
			if ($podCastGallery) {
				if (in_array($ext,array("m4a", "mp3", "mov", "mp4", "m4v", "pdf"))) {
					$fhash .= ".".$ext;
				}
				$savedir=$prefs['fgal_podcast_dir'];
			}
			@$fw = @fopen($savedir . $fhash, "wb");
			if (!$fw) {
				$feedback[] = "!!!". sprintf(tra('Could not write to file %s.').$savedir.$fhash);
				$error=true;
			}
		}
		while (!feof($fp)) {
			if (($prefs['fgal_use_db'] == 'y') && (!$podCastGallery)) {
				$data .= @fread($fp, 8192 * 16);
			} else {
				$data = @fread($fp, 8192 * 16);
				@fwrite($fw, $data);
			}
		}
		@fclose ($fp);
		$sizeArray[$x] = @filesize($savedir . $fhash);

		// file system is used:
		if (($prefs['fgal_use_db'] == 'n') || ($podCastGallery)) {
			fclose ($fw);
			$data = '';
		} else {
			// database is used:
			if (!isset($data) || strlen($data) < 1) {
				$feedback[] = "!!!". sprintf(tra('File %s upload failed.'),$fileArray[$x]);
				$error=true;
			}
		}

		if (!$error) {
			// check which gallery to upload to
			$tmpGalId = (int)$_REQUEST["galleryId"];

			// if subToDesc is set, set description:
			if (isset($_REQUEST["subToDesc"])) {
				// get last subdir 'last' from 'some/path/last'
				$tmpDesc = preg_replace('/.*([^\/]*)\/([^\/]+)$/U','$1', $fileArray[$x]);
			} else {
				$tmpDesc = '';
			}
			// remove possible path from filename
			$fileArray[$x] = preg_replace('/.*([^\/]*)$/U','$1', $fileArray[$x]);
			$name = $fileArray[$x];
			// remove extension from name field
			if (isset($_REQUEST["removeExt"])) {
				$name = substr($name,0,strrpos($name, "."));
			}

			$fileId	= $filegallib->insert_file($tmpGalId, $name,
				$tmpDesc, $fileArray[$x], $data, $sizeArray[$x], $typeArray[$x], $user, $fhash);

			if ($fileId) {
				$feedback[] = tra('Upload was successful'). ': ' . $name;
				if (@unlink ($filepath))
					$feedback[] = sprintf(tra('File %s removed from Batch directory.'),$name);
				else
						$feedback[] = "!!! ". sprintf(tra('Impossible to remove file %s from Batch directory.'),$name);
			} else {
				$feedback[] = "!!!".tra('Upload was not successful'). ': ' . $name;
				if (($prefs['fgal_use_db'] == 'n') || ($podCastGallery)) {
					@unlink($savedir . $fhash);
				}
			}
		} // if (!$error)
	} // for ($x=0; $x < $totfiles; $x++)
}

$a_file=array();
$a_path=array();
buildFileList();

$smarty->assign('feedback', $feedback);

if (isset($_REQUEST["galleryId"])) {
	$smarty->assign_by_ref('galleryId', $_REQUEST["galleryId"]);
	$smarty->assign('permAddGallery', 'n');
	if ($tiki_p_admin_file_galleries == 'y' || $userlib->object_has_permission($user, $_REQUEST["galleryId"], 'image gallery', 'tiki_p_create_file_galleries')) {
		$smarty->assign('permAddGallery', 'y');
	}
} else {
	$smarty->assign('galleryId', '');
}

$galleries = $filegallib->list_file_galleries(0, -1, 'name_asc', $user, '');

$temp_max = count($galleries["data"]);
for ($i = 0; $i < $temp_max; $i++) {
	if ($userlib->object_has_one_permission($galleries["data"][$i]["galleryId"], 'file gallery')) {
		$galleries["data"][$i]["individual"] = 'y';
		$galleries["data"][$i]["individual_tiki_p_batch_upload_file_dir"] = 'n';
		if ($tiki_p_admin == 'y' 
			|| $userlib->object_has_permission($user, $galleries["data"][$i]["galleryId"], 'file gallery', 'tiki_p_batch_upload_file_dir')
			|| $userlib->object_has_permission($user, $galleries["data"][$i]["galleryId"], 'file gallery', 'tiki_p_admin_file_galleries')) {
			$galleries["data"][$i]["individual_tiki_p_batch_upload_file_dir"] = 'y';
		}
	} else {
		$galleries["data"][$i]["individual"] = 'n';
	}
}

$smarty->assign_by_ref('galleries', $galleries["data"]);

include_once ('tiki-section_options.php');

// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');

// Display the template
$smarty->assign('mid', 'tiki-batch_upload_files.tpl');
$smarty->display("tiki.tpl");

?>
