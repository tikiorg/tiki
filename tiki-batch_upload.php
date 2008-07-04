<?php
// $Id: /cvsroot/tikiwiki/tiki/tiki-batch_upload.php,v 1.16 2007-10-12 07:55:24 nyloth Exp $

$section = 'galleries';
require_once ('tiki-setup.php');
include_once ('lib/categories/categlib.php');
include_once ('lib/imagegals/imagegallib.php');

if ($prefs['feature_galleries'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_galleries");
	$smarty->display("error.tpl");
	die;
}

if ($prefs['feature_gal_batch'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_gal_batch");
	$smarty->display("error.tpl");
	die;
}

// Now check permissions to access this page
if ($tiki_p_batch_upload_image_dir != 'y') {
	$smarty->assign('errortype', 401);
	$smarty->assign('msg', tra("Permission denied you cannot use the batch directory loading"));
	$smarty->display("error.tpl");
	die;
}

// check directory path 
if (!isset($prefs['gal_batch_dir']) or !is_dir($prefs['gal_batch_dir'])) {
	$msg = tra("Incorrect directory chosen for batch upload of images.")."<br />"; 
	if ($tiki_p_admin == 'y') {
		$msg.= tra("Please setup that dir on ").'<a href="tiki-admin.php?page=gal">'.tra('Image Galleries Admin Panel').'</a>.';
	} else {
		$msg.= tra("Please contact the website administrator.");
	}
	$smarty->assign('msg', $msg); 
	$smarty->display("error.tpl");
	die;
} else {
	$imgdir = $prefs['gal_batch_dir'];
}

$smarty->assign('imgdir',$imgdir);

$a_img = $imgstring = $feedback = array();
$a_path = array();

$allowed_types = array('.png','.jpg','.jpeg','.gif'); // list of filetypes you want to show

// recursively get all images from all subdirectories
function getDirContent($sub) {
	global $allowed_types;
	global $a_img;
	global $a_path;
	global $imgdir, $smarty;
	
	$allimg = array();
	$tmp=$imgdir;
	if ($sub <> "") $tmp .= '/'.$sub;
	if (!@($dimg = opendir($tmp))) {
		$msg= tra("Invalid directory name");
		$smarty->assign('msg', $msg); 
		$smarty->display("error.tpl");
		die;
	}
	while((false!==($imgf=readdir($dimg)))) {
		if ($imgf != "." && $imgf != ".." && substr($imgf,0,1) != ".") {
			$allimg[] = $imgf;
		}
	}
	sort($allimg);
	foreach ($allimg as $imgfile) {
		if (is_dir($tmp . "/" . $imgfile)) {
			if ((substr($sub,-1)<>"/") && (substr($sub,-1)<>"\\")) {
				$sub .= '/';
			}
			getDirContent($sub.$imgfile);
		} elseif (in_array(strtolower(substr($imgfile,-(strlen($imgfile)-strrpos($imgfile, ".")))),$allowed_types)) {
			$a_img[] = $imgfile;
			$a_path[] = $sub;
		}
	}
	closedir($dimg);
}

// build a complete list of all images on filesystem including all necessary image info
function buildImageList() {
	global $a_img;
	global $a_path;
	global $imgdir, $smarty;
	global $imgstring;

	getDirContent('');
	
	$totimg = count($a_img); // total image number
	$totalsize = 0;
	
	// build image data array
	for($x=0; $x < $totimg; $x++) {
		// get root dir
		while (substr($imgdir,-1)=='/') $imgdir = substr($imgdir,0,-1);
		$tmp = $imgdir;
		// add any subdir names
		if ($a_path[$x] <> "") $tmp .= $a_path[$x];
	
		// get image information
		$size = @getimagesize($tmp.'/'.$a_img[$x]);
		$filesize = @filesize($tmp.'/'.$a_img[$x]);	
		$halfwidth = ceil($size[0]/2);
		$halfheight = ceil($size[1]/2);
		$imgstring[$x][0]= $a_img[$x];
	
		if ($a_path[$x] <> "") $imgstring[$x][0]= $a_path[$x].'/'.$a_img[$x];
		$imgstring[$x][1]= $size[0];
		$imgstring[$x][2]= $size[1];
		$imgstring[$x][3]= $filesize;
	
		// type is string after last dot
		$tmp=strtolower(substr($a_img[$x],-(strlen($a_img[$x])-1-strrpos($a_img[$x], "."))));
		if ($tmp == "jpeg") $tmp="jpg";
		$imgstring[$x][4]= $tmp;
		$totalsize+= $filesize;
	}
	$smarty->assign('totimg',$totimg);
	$smarty->assign('totalsize',$totalsize);
	$smarty->assign('imgstring',$imgstring);
}


if (isset($_REQUEST["batch_upload"]) and isset($_REQUEST['imgs']) and is_array($_REQUEST['imgs'])) {

	// default is: image names from request
	$imgArray = $_REQUEST['imgs'];
	$totimgs = count($imgArray);

	// if ALL is given, get all the images from the filesystem (stored in $a_img[] already) 
	if ($totimgs==1) {
		if ($imgArray[0]=="ALL") {
			getDirContent('');
			$imgArray = $a_img;
			$imgPathArray = $a_path;
			$totimgs = count($imgArray);
		}
	}

	// for subdirToSubgal we need all existing sub galleries for the current gallery
	$subgals = array();	
	if (isset($_REQUEST["subdirTosubgal"])) {
		$subgals = $imagegallib->get_subgalleries(0, 9999, "name_asc", '', $_REQUEST["galleryId"]);
	}

	// cycle through all images to upload	
	for ($x=0; $x < $totimgs; $x++) {
		if ($imgPathArray[$x]<>"") {
			$imgPathArray[$x] .= '/';
		} else {
			// if there is a path in image name, move it to the path array
			if (strrpos($imgArray[$x],"/")>0) {
				$imgPathArray[$x] = substr($imgArray[$x],0,strrpos($imgArray[$x],"/")+1);
				$imgArray[$x] = substr($imgArray[$x],strrpos($imgArray[$x],"/")+1);
			}
		}
		
		$filepath = $imgdir.$imgPathArray[$x].$imgArray[$x];
		$size = @getimagesize($filepath);
		$filesize = @filesize($filepath);
		// type is string after last dot
		$type = strtolower(substr($imgArray[$x],-(strlen($imgArray[$x])-1-strrpos($imgArray[$x], "."))));
		if ($type == "jpeg") $type="jpg";
			$data = '';
		$fp = @fopen($filepath,'r');
		if (!$fp) {
			$feedback[] = "!!!". sprintf(tra('Could not read image %s.'),$filepath);
		} else {
			while (!feof($fp)) {
				$data .= @fread($fp,1024);
			}
			@fclose($fp);

			// replace \ with /
			$imgArray[$x] = strtr($imgArray[$x], "\\", "/");
			$imgPathArray[$x] = strtr($imgPathArray[$x], "\\", "/");

			// get path, maybe needed as subgallery name
			$tmppath=$imgPathArray[$x];
			
			if (substr($tmppath,0,1)=="/") $tmppath = substr($tmppath,1,999);
			if (substr($tmppath,-1)=="/") $tmppath = substr($tmppath,0,-1);

			// fix image name:
			$tmpName = '/'.$imgArray[$x];

			// remove possible path from filename
			$tmpName = substr($tmpName,strrpos($tmpName, "/")+1,999);
			// save filename without path
			$imgArray[$x] = $tmpName;

			// remove extension from name field
			if (isset($_REQUEST["removeExt"])) {
				$tmpName = substr($tmpName,0,strrpos($tmpName, "."));
			}

			// check which gallery to upload to
			$tmpGalId = $_REQUEST["galleryId"];
			if (isset($_REQUEST["subdirToSubgal"])) {
				// get parent gallery data
				$parent = @$imagegallib->get_gallery($_REQUEST["galleryId"]);

				if ($tmppath<>"") {
					$tmpGalName = $tmppath;
					// get last subdir 'last' from 'some/path/last'
					if (strpos($tmpGalName,"/")>0) $tmpGalName = substr($tmpGalName,strrpos($tmpGalName,"/")+1,999);

					$tmpGalId = @$imagegallib->replace_gallery(0, $tmpGalName, '',
					'', $user, $parent["maxRows"], $parent["rowImages"], $parent["thumbSizeX"], $parent["thumbSizeY"], $parent["public"],
					$parent["visible"],$parent['sortorder'],$parent['sortdirection'],$parent['galleryimage'],$_REQUEST["galleryId"],
					$parent['showname'],$parent['showimageid'],$parent['showdescription'],$parent['showcreated'],
					$parent['showuser'],$parent['showhits'],$parent['showxysize'],$parent['showfilesize'],$parent['showfilename'],
					$parent['defaultscale']);
					if ($tmpGalId == 0) $tmpGalId = $_REQUEST["galleryId"];
				}
			}
			
			// if subToDesc is set, set description:
			if (isset($_REQUEST["subToDesc"]))
				$tmpDesc = $tmppath;
			else
				$tmpDesc = '';

			// add image to gallery
			$imageId = $imagegallib->insert_image($tmpGalId, $tmpName, $tmpDesc, $imgArray[$x], $type, $data, $filesize, $size[0], $size[1], $user, '', '');
			if (!$imageId) {
				$feedback[] = "!!!". sprintf(tra('Image %s upload failed.'),$imgArray[$x]);
			} else {
				$feedback[] = sprintf(tra('Image %s uploaded successfully.'),$imgArray[$x]);
				if (@ unlink($filepath)) {
					$feedback[] = sprintf(tra('Image %s removed from Batch directory.'),$imgArray[$x]);
				} else {
					$feedback[] = "!!! ". sprintf(tra('Impossible to remove image %s from Batch directory.'),$imgArray[$x]);
				}
			}
		}
	}
}

$a_img=array();
$a_path=array();
buildImageList();

$smarty->assign('feedback', $feedback);

if (isset($_REQUEST["galleryId"])) {
	$smarty->assign_by_ref('galleryId', $_REQUEST["galleryId"]);
	$smarty->assign('permAddGallery', 'n');
	if ($tiki_p_admin_galleries == 'y' || $userlib->object_has_permission($user, $_REQUEST["galleryId"], 'image gallery', 'tiki_p_create_galleries')) {
		$smarty->assign('permAddGallery', 'y');
	}
} else {
	$smarty->assign('galleryId', '');
}

if ($tiki_p_admin_galleries != 'y') {
	$galleries = $imagegallib->list_visible_galleries(0, -1, 'lastModif_desc', $user, '');
} else {
	$galleries = $imagegallib->list_galleries(0, -1, 'lastModif_desc', $user, '');
}

$temp_max = count($galleries["data"]);
for ($i = 0; $i < $temp_max; $i++) {
	if ($userlib->object_has_one_permission($galleries["data"][$i]["galleryId"], 'image gallery')) {
		$galleries["data"][$i]["individual"] = 'y';
		$galleries["data"][$i]["individual_tiki_p_batch_upload_image_dir"] = 'n';
		if ($tiki_p_admin == 'y' 
			|| $userlib->object_has_permission($user, $galleries["data"][$i]["galleryId"], 'image gallery', 'tiki_p_batch_upload_image_dir')
			|| $userlib->object_has_permission($user, $galleries["data"][$i]["galleryId"], 'image gallery', 'tiki_p_admin_galleries')) {
			$galleries["data"][$i]["individual_tiki_p_batch_upload_image_dir"] = 'y';
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
$smarty->assign('mid', 'tiki-batch_upload.tpl');
$smarty->display("tiki.tpl");

?>
